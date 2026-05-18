<?php

namespace Box\Resource;

class Event
{
    protected ?string $type = null;

    protected string|int|null $eventId = null;

    // mixed: hydrator may deliver a User array or User object depending on API response shape
    protected mixed $createdBy = null;

    protected ?string $eventType = null;

    protected ?string $sessionId = null;

    // mixed: source can be a File, Folder, User, Comment, or other resource type depending on the event
    protected mixed $source = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type = null): void
    {
        $this->type = $type;
    }

    public function getEventId(): string|int|null
    {
        return $this->eventId;
    }

    public function setEventId(string|int|null $eventId = null): void
    {
        $this->eventId = $eventId;
    }

    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    public function setCreatedBy(mixed $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType = null): void
    {
        $this->eventType = $eventType;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId = null): void
    {
        $this->sessionId = $sessionId;
    }

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function setSource(mixed $source = null): void
    {
        $this->source = $source;
    }
}
