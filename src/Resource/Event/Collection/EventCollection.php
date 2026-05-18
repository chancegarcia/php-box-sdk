<?php

namespace Box\Resource\Event\Collection;

use Box\Exception\BoxException;
use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;
use Doctrine\Common\Collections\Collection;

class EventCollection
{
    protected int|string|null $chunkSize = null;
    protected int|string|null $nextStreamPosition = null;

    protected ?Collection $entries = null;
    // mixed: raw entries array before conversion to Collection; preserved for reference
    protected mixed $originalEntries = null;

    public function getOriginalEntries(): mixed
    {
        return $this->originalEntries;
    }

    public function setOriginalEntries(mixed $originalEntries = null): void
    {
        $this->originalEntries = $originalEntries;
    }

    public function getChunkSize(): int|string|null
    {
        return $this->chunkSize;
    }

    public function setChunkSize(int|string|null $chunkSize = null): void
    {
        $this->chunkSize = $chunkSize;
    }

    public function getNextStreamPosition(): int|string|null
    {
        return $this->nextStreamPosition;
    }

    public function setNextStreamPosition(int|string|null $nextStreamPosition = null): void
    {
        $this->nextStreamPosition = $nextStreamPosition;
    }

    public function getEntries(): ?Collection
    {
        return $this->entries;
    }

    /**
     * @throws BoxException
     */
    public function setEntries(Collection|array|null $entries = null): void
    {
        if (is_array($entries)) {
            $this->originalEntries = $entries;
            $entries = new DoctrineArrayCollection($entries);
        } else {
            if (!$entries instanceof Collection && null !== $entries) {
                throw new BoxException('entries must be an array or instance of \Doctrine\Common\Collections\Collection');
            }
        }

        $this->entries = $entries;
    }
}
