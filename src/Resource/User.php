<?php

declare(strict_types=1);

namespace Box\Resource;

use Box\Enum\UserStatus;
use DateTimeImmutable;

/**
 * Box User Resource.
 *
 * This is a passive resource object representing a Box User.
 * It follows the V1 architecture: strictly typed, passive, and using DateTimeImmutable.
 */
class User
{
    protected ?string $id = null;
    protected ?string $name = null;
    protected ?string $login = null;
    protected ?DateTimeImmutable $createdAt = null;
    protected ?DateTimeImmutable $modifiedAt = null;
    protected ?UserStatus $status = null;
    protected ?string $language = null;
    protected ?string $timezone = null;
    protected ?int $spaceAmount = null;
    protected ?int $spaceUsed = null;
    protected ?int $maxUploadSize = null;
    protected ?bool $canSeeManagedUsers = null;
    protected ?bool $isSyncEnabled = null;
    protected ?bool $isExemptFromDeviceLimits = null;
    protected ?bool $isExemptFromLoginVerification = null;
    protected ?bool $isExternalCollabRestricted = null;
    protected ?string $enterprise = null; // Can be a DTO later
    protected ?string $jobTitle = null;
    protected ?string $phone = null;
    protected ?string $address = null;
    protected ?string $avatarUrl = null;
    protected ?string $role = null;
    protected ?array $trackingCodes = null;
    protected ?string $type = 'user';

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getModifiedAt(): ?DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?DateTimeImmutable $modifiedAt): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    public function getStatus(): ?UserStatus
    {
        return $this->status;
    }

    public function setStatus(?UserStatus $status): void
    {
        $this->status = $status;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getSpaceAmount(): ?int
    {
        return $this->spaceAmount;
    }

    public function setSpaceAmount(?int $spaceAmount): void
    {
        $this->spaceAmount = $spaceAmount;
    }

    public function getSpaceUsed(): ?int
    {
        return $this->spaceUsed;
    }

    public function setSpaceUsed(?int $spaceUsed): void
    {
        $this->spaceUsed = $spaceUsed;
    }

    public function getMaxUploadSize(): ?int
    {
        return $this->maxUploadSize;
    }

    public function setMaxUploadSize(?int $maxUploadSize): void
    {
        $this->maxUploadSize = $maxUploadSize;
    }

    public function getCanSeeManagedUsers(): ?bool
    {
        return $this->canSeeManagedUsers;
    }

    public function setCanSeeManagedUsers(?bool $canSeeManagedUsers): void
    {
        $this->canSeeManagedUsers = $canSeeManagedUsers;
    }

    public function getIsSyncEnabled(): ?bool
    {
        return $this->isSyncEnabled;
    }

    public function setIsSyncEnabled(?bool $isSyncEnabled): void
    {
        $this->isSyncEnabled = $isSyncEnabled;
    }

    public function getIsExemptFromDeviceLimits(): ?bool
    {
        return $this->isExemptFromDeviceLimits;
    }

    public function setIsExemptFromDeviceLimits(?bool $isExemptFromDeviceLimits): void
    {
        $this->isExemptFromDeviceLimits = $isExemptFromDeviceLimits;
    }

    public function getIsExemptFromLoginVerification(): ?bool
    {
        return $this->isExemptFromLoginVerification;
    }

    public function setIsExemptFromLoginVerification(?bool $isExemptFromLoginVerification): void
    {
        $this->isExemptFromLoginVerification = $isExemptFromLoginVerification;
    }

    public function getIsExternalCollabRestricted(): ?bool
    {
        return $this->isExternalCollabRestricted;
    }

    public function setIsExternalCollabRestricted(?bool $isExternalCollabRestricted): void
    {
        $this->isExternalCollabRestricted = $isExternalCollabRestricted;
    }

    public function getEnterprise(): ?string
    {
        return $this->enterprise;
    }

    public function setEnterprise(?string $enterprise): void
    {
        $this->enterprise = $enterprise;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public function getTrackingCodes(): ?array
    {
        return $this->trackingCodes;
    }

    public function setTrackingCodes(?array $trackingCodes): void
    {
        $this->trackingCodes = $trackingCodes;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
