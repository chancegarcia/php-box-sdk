<?php

namespace Box\Resource;

class AdminEvent extends Event
{
    private string $streamType = "admin_logs";

    protected int $limit = 100;
    protected ?string $streamPosition = null;
    protected ?string $createdAfter = null;
    protected ?string $createdBefore = null;

    public function getStreamType(): string
    {
        return $this->streamType;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit = 100): void
    {
        $this->limit = $limit;
    }

    public function getStreamPosition(): ?string
    {
        return $this->streamPosition;
    }

    public function setStreamPosition(?string $streamPosition = null): void
    {
        $this->streamPosition = $streamPosition;
    }

    public function getCreatedAfter(): ?string
    {
        return $this->createdAfter;
    }

    public function setCreatedAfter(?string $createdAfter = null): void
    {
        $this->createdAfter = $createdAfter;
    }

    public function getCreatedBefore(): ?string
    {
        return $this->createdBefore;
    }

    public function setCreatedBefore(?string $createdBefore = null): void
    {
        $this->createdBefore = $createdBefore;
    }
}
