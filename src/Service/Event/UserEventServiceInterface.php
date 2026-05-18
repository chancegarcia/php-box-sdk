<?php

namespace Box\Service\Event;

use Box\Dto\Event\EventResponse;
use Box\Service\ServiceInterface;
use Box\Mapper\EventResponseMapper;

interface UserEventServiceInterface extends ServiceInterface
{
    public const int LIMIT_MAX = 800;
    public const int LIMIT_DEFAULT = 100;

    /**
     * @return array<string>
     */
    public function getValidStreamTypes(): array;

    public function getStreamType(): string;

    public function setStreamType(?string $streamType = null): void;

    public function getLimit(): int;

    /**
     * @param string|int|null $limit set null to reset to default value
     */
    public function setLimit(string|int|null $limit = null): void;

    public function getStreamPosition(): string|int;

    public function setStreamPosition(string|int|null $streamPosition = null): void;

    public function getEvents(): EventResponse;

    public function getEventsUri(): string;

    public function setEventResponseMapper(EventResponseMapper $mapper): void;
}
