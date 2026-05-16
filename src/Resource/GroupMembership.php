<?php

declare(strict_types=1);

namespace Box\Resource;

use DateTimeInterface;

class GroupMembership
{
    protected string $type = 'group_membership';
    protected string|int|null $id = null;
    // mixed: hydrator may deliver a User as array or object depending on API response shape
    protected mixed $user = null;
    // mixed: hydrator may deliver a Group as array or object depending on API response shape
    protected mixed $group = null;
    protected ?string $role = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getUser(): mixed
    {
        return $this->user;
    }

    public function setUser(mixed $user = null): void
    {
        $this->user = $user;
    }

    public function getGroup(): mixed
    {
        return $this->group;
    }

    public function setGroup(mixed $group = null): void
    {
        $this->group = $group;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role = null): void
    {
        $this->role = $role;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }
}
