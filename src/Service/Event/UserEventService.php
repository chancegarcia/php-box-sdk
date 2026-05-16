<?php

namespace Box\Service\Event;

use Box\Dto\Event\EventResponse;
use Box\Exception\BoxException;
use Box\Mapper\EventResponseMapper;
use Box\Service\Service;
use Psr\Log\LoggerInterface;

class UserEventService extends Service implements UserEventServiceInterface
{
    public const string ENDPOINT = "https://api.box.com/2.0/events";

    protected array $validStreamTypes = [
        'all'
        /* returns everything */
        ,
        'changes'
        /* returns tree changes */
        ,
        'sync'
        /* returns tree changes only for sync folders */
    ];

    protected string $streamType = "all";
    protected string|int $streamPosition = 'now';
    protected int $limit = self::LIMIT_DEFAULT;

    private ?EventResponseMapper $eventResponseMapper = null;

    public function getValidStreamTypes(): array
    {
        return $this->validStreamTypes;
    }

    public function getStreamType(): string
    {
        return $this->streamType;
    }

    public function setStreamType(?string $streamType = null): void
    {
        $validStreamTypes = $this->getValidStreamTypes();
        if (!in_array($streamType, $validStreamTypes)) {
            throw new BoxException("unexpect type ("
                                   . var_export($streamType, true)
                                   . ") valid types include: "
                                   . implode(", ", $validStreamTypes));
        }

        $this->streamType = $streamType;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(string|int|null $limit = null): void
    {
        if (null === $limit) {
            $limit = self::LIMIT_DEFAULT;
        }

        if (!is_numeric($limit)) {
            throw new BoxException('limit must be a valid integer value, (' . var_export($limit, true) . ') given');
        }

        if ($limit > self::LIMIT_MAX) {
            $limit = self::LIMIT_MAX;
        }

        $this->limit = (int) $limit;
    }

    public function getStreamPosition(): string|int
    {
        return $this->streamPosition;
    }

    public function setStreamPosition(string|int|null $streamPosition = null): void
    {
        if (null === $streamPosition) {
            $streamPosition = 'now';
        }

        if ("now" !== $streamPosition && !is_numeric($streamPosition)) {
            throw new BoxException('stream_position must be a valid integer value or "now", ('
                                   . var_export($streamPosition, true)
                                   . ') given');
        }

        $this->streamPosition = $streamPosition;
    }

    public function getEvents(): EventResponse
    {
        $uri = $this->getEventsUri();

        $eventsData = $this->handleBoxResponse($this->getConnection()->query($uri), 'decoded');
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'events data: ' . var_export($eventsData, true),
                [
                                          __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        return $this->getEventResponseMapper()->map($eventsData);
    }

    protected function getEventResponseMapper(): EventResponseMapper
    {
        if (null === $this->eventResponseMapper) {
            $this->eventResponseMapper = new EventResponseMapper();
        }

        return $this->eventResponseMapper;
    }

    public function setEventResponseMapper(EventResponseMapper $mapper): void
    {
        $this->eventResponseMapper = $mapper;
    }

    public function getEventsUri(): string
    {
        $query = http_build_query([
            'stream_type' => $this->getStreamType(),
            'stream_position' => $this->getStreamPosition(),
            'limit' => $this->getLimit(),
        ]);

        return self::ENDPOINT . "?" . $query;
    }
}
