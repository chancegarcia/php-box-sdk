<?php

namespace Box\Resource;

use Box\Enum\SharedLinkAccess;
use Box\Resource\SharedLink\Permissions\Permissions;
use DateTimeInterface;

class SharedLink
{
    protected ?SharedLinkAccess $access = null;
    protected DateTimeInterface|string|null $unsharedAt = null;
    protected ?string $password = null;
    protected ?Permissions $permissions = null;
    protected ?string $effectiveAccess = null;

    public function getAccess(): ?SharedLinkAccess
    {
        return $this->access;
    }

    public function setAccess(?SharedLinkAccess $access = null): void
    {
        $this->access = $access;
    }

    public function getUnsharedAt(): DateTimeInterface|string|null
    {
        return $this->unsharedAt;
    }

    public function setUnsharedAt(DateTimeInterface|string|null $unsharedAt = null): void
    {
        $this->unsharedAt = $unsharedAt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password = null): void
    {
        $this->password = $password;
    }

    public function getPermissions(): ?Permissions
    {
        return $this->permissions;
    }

    public function setPermissions(?Permissions $permissions = null): void
    {
        $this->permissions = $permissions;
    }

    public function getEffectiveAccess(): ?string
    {
        return $this->effectiveAccess;
    }

    public function setEffectiveAccess(?string $effectiveAccess = null): void
    {
        $this->effectiveAccess = $effectiveAccess;
    }

    public function toArray(): array
    {
        return [
            'access' => $this->access?->value,
            'unshared_at' => $this->unsharedAt,
            'password' => $this->password,
        ];
    }
}
