<?php

declare(strict_types=1);

namespace Box\Dto\Event;

use Box\Event\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EventResponse
{
    /**
     * @param Collection<int, Event> $entries
     * @param int $chunkSize
     * @param string $nextStreamPosition
     */
    public function __construct(
        private readonly Collection $entries,
        private readonly int $chunkSize,
        private readonly string $nextStreamPosition,
    ) {
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEntries(): Collection
    {
        return new ArrayCollection($this->entries->toArray());
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    public function getNextStreamPosition(): string
    {
        return $this->nextStreamPosition;
    }
}
