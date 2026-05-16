<?php

namespace Box\Resource\Event\Collection\Entry\Source;

use Box\Resource\Folder;
use DateTimeInterface;

class EntrySource
{
    protected string|int|null $sequenceId = null;
    protected string $type = "file";
    protected string|int|null $id = null;
    protected ?string $etag = null;
    protected ?string $name = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;
    protected ?string $description = null;
    protected ?int $size = null;
    protected ?string $createdBy = null;
    protected ?string $modifiedBy = null;
    protected ?string $ownedBy = null;
    protected ?string $sharedLink = null;
    protected Folder|array|null $parent = null;
    protected ?string $itemStatus = null;
    // mixed: the 'synced' field is a deprecated Box Sync field that may be bool, null, or absent
    protected mixed $synced = null;

    public function getSequenceId(): string|int|null
    {
        return $this->sequenceId;
    }

    public function setSequenceId(string|int|null $sequenceId): void
    {
        $this->sequenceId = $sequenceId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setId(string|int|null $id): void
    {
        $this->id = $id;
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function setEtag(?string $etag): void
    {
        $this->etag = $etag;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface|string|null $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getModifiedBy(): ?string
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(?string $modifiedBy): void
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function getOwnedBy(): ?string
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?string $ownedBy): void
    {
        $this->ownedBy = $ownedBy;
    }

    public function getSharedLink(): ?string
    {
        return $this->sharedLink;
    }

    public function setSharedLink(?string $sharedLink): void
    {
        $this->sharedLink = $sharedLink;
    }

    public function getParent(): Folder|array|null
    {
        return $this->parent;
    }

    public function setParent(Folder|array|null $parent): void
    {
        $this->parent = $parent;
    }

    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

    public function setItemStatus(?string $itemStatus): void
    {
        $this->itemStatus = $itemStatus;
    }

    public function getSynced(): mixed
    {
        return $this->synced;
    }

    public function setSynced(mixed $synced): void
    {
        $this->synced = $synced;
    }
}
