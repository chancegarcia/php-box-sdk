<?php

declare(strict_types=1);

namespace Box\Mapper;

use ArrayAccess;
use Box\Dto\Event\EventResponse;
use Box\Event\Event;
use Box\Exception\BoxException;
use Doctrine\Common\Collections\ArrayCollection;
use stdClass;

final class EventResponseMapper
{
    public function __construct(
        private readonly Hydrator $hydrator = new Hydrator()
    ) {
    }

    /**
     * @param array|stdClass|ArrayAccess<string, mixed> $data
     * @throws BoxException
     */
    public function map(array|stdClass|ArrayAccess $data): EventResponse
    {
        $entries = new ArrayCollection();
        $entriesData = $this->getEntriesData($data);

        foreach ($entriesData as $entryData) {
            $entries->add($this->hydrator->hydrate(Event::class, $entryData));
        }

        $nextStreamPosition = $this->getValue($data, 'next_stream_position', null);

        if (null === $nextStreamPosition) {
            throw new BoxException(
                'Events response is missing required "next_stream_position" field.',
                BoxException::INVALID_INPUT
            );
        }

        return new EventResponse(
            $entries,
            (int) $this->getValue($data, 'chunk_size', 0),
            (string) $nextStreamPosition
        );
    }

    private function getEntriesData(array|stdClass|ArrayAccess $data): array
    {
        $entries = $this->getValue($data, 'entries', []);

        return is_array($entries) ? $entries : [];
    }

    private function getValue(array|stdClass|ArrayAccess $data, string $key, mixed $default): mixed
    {
        if (is_array($data)) {
            return $data[$key] ?? $default;
        }

        if ($data instanceof stdClass) {
            return $data->$key ?? $default;
        }

        if ($data instanceof ArrayAccess) {
            return $data->offsetExists($key) ? $data->offsetGet($key) : $default;
        }

        return $default;
    }
}
