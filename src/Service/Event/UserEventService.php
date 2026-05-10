<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 6:22 PM
 * @package     Box
 * @subpackage  Box_Model
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013 Chance Garcia, chancegarcia.com
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2016 Chance Garcia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Box\Service\Event;

use Box\Dto\Event\EventResponse;
use Box\Exception\BoxException;
use Box\Mapper\EventResponseMapper;
use Box\Service\Service;
use Psr\Log\LoggerInterface;

/**
 * Class UserEventService
 * @package Box\Service
 */
class UserEventService extends Service implements UserEventServiceInterface
{
    public const ENDPOINT = "https://api.box.com/2.0/events";

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

    /**
     * {@inheritdoc}
     */
    public function getValidStreamTypes(): array
    {
        return $this->validStreamTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamType(): string
    {
        return $this->streamType;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getStreamPosition(): string|int
    {
        return $this->streamPosition;
    }

    /**
     * {@inheritdoc}
     */
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

        $eventsData = $this->getFromBox($uri, 'decoded');
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
