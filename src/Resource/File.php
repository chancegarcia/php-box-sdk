<?php

/**
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
 */

namespace Box\Resource;

use Box\Dto\PathCollection;
use Box\Resource\SharedLink;
use DateTimeInterface;

class File
{
    protected string|int|null $id = null;
    protected string $type = "file";
    protected ?string $sequenceId = null;
    protected ?string $etag = null;
    protected ?string $sha1 = null;
    protected ?string $name = null;
    protected ?string $description = null;
    protected ?int $size = null;
    protected ?PathCollection $pathCollection = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;
    protected DateTimeInterface|string|null $trashedAt = null;
    protected DateTimeInterface|string|null $purgedAt = null;
    protected DateTimeInterface|string|null $contentCreatedAt = null;
    protected DateTimeInterface|string|null $contentModifiedAt = null;
    protected mixed $createdBy = null;
    protected mixed $modifiedBy = null;
    protected mixed $ownedBy = null;
    protected ?SharedLink $sharedLink = null;
    protected mixed $parent = null;
    protected ?string $itemStatus = null;

    // the following will not appear in default file requests and must be explicitly asked for
    // using the fields parameter.
    protected ?string $versionNumber = null;
    protected ?int $commentCount = null;
    protected mixed $permissions = null;
    protected ?bool $isExternallyOwned = null;
    protected mixed $allowedInviteRoles = null;
    protected ?bool $hasCollaborations = null;
    protected mixed $metadata = null;

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    public function setCommentCount(?int $commentCount = null): void
    {
        $this->commentCount = $commentCount;
    }

    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    public function setContentCreatedAt(DateTimeInterface|string|null $contentCreatedAt = null): void
    {
        $this->contentCreatedAt = $contentCreatedAt;
    }

    public function getContentCreatedAt(): DateTimeInterface|string|null
    {
        return $this->contentCreatedAt;
    }

    public function setContentModifiedAt(DateTimeInterface|string|null $contentModifiedAt = null): void
    {
        $this->contentModifiedAt = $contentModifiedAt;
    }

    public function getContentModifiedAt(): DateTimeInterface|string|null
    {
        return $this->contentModifiedAt;
    }

    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    /**
     * @param User|array|null $createdBy
     */
    public function setCreatedBy(mixed $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setEtag(?string $etag = null): void
    {
        $this->etag = $etag;
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function setItemStatus(?string $itemStatus = null): void
    {
        $this->itemStatus = $itemStatus;
    }

    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    /**
     * @param User|array|null $modifiedBy
     */
    public function setModifiedBy(mixed $modifiedBy = null): void
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function getModifiedBy(): mixed
    {
        return $this->modifiedBy;
    }

    public function setName(?string $name = null): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getExtension(): string
    {
        $name = $this->getName();

        if (null === $name || '' === $name) {
            return '';
        }

        $pathInfo = pathinfo($name);

        if (!isset($pathInfo['extension'])) {
            return '';
        }

        // handle .env style files (no extension in common parlance if only leading dot)
        if (isset($pathInfo['filename']) && '' === $pathInfo['filename'] && str_starts_with($name, '.')) {
            // Check if there's actually an extension part after the first dot
            // pathinfo('.env') gives extension='env', filename=''
            // pathinfo('.gitignore') gives extension='gitignore', filename=''
            // If the user wants .env to have NO extension, we keep it this way.
            // But if they want .gitignore to have NO extension too, then we are fine.
            // Typically .env and .gitignore are considered to have no extension or the extension IS the name.
            // The requirement said: For names like .env, treat them as having no extension unless
            // existing project conventions suggest otherwise.
            return '';
        }

        return $pathInfo['extension'];
    }

    /**
     * @param User|array|null $ownedBy
     */
    public function setOwnedBy(mixed $ownedBy = null): void
    {
        $this->ownedBy = $ownedBy;
    }

    public function getOwnedBy(): mixed
    {
        return $this->ownedBy;
    }

    /**
     * @param Folder|array|null $parent
     */
    public function setParent(mixed $parent = null): void
    {
        $this->parent = $parent;
    }

    public function getParent(): mixed
    {
        return $this->parent;
    }

    public function setPathCollection(array|PathCollection|null $pathCollection = null): void
    {
        if (is_array($pathCollection)) {
            $totalCount = (int) ($pathCollection['total_count'] ?? 0);
            $entries = $pathCollection['entries'] ?? [];
            $pathCollection = new PathCollection($totalCount, $entries);
        }

        $this->pathCollection = $pathCollection;
    }

    public function getPathCollection(): ?PathCollection
    {
        return $this->pathCollection;
    }

    public function setPermissions(mixed $permissions = null): void
    {
        $this->permissions = $permissions;
    }

    public function getPermissions(): mixed
    {
        return $this->permissions;
    }

    public function setPurgedAt(DateTimeInterface|string|null $purgedAt = null): void
    {
        $this->purgedAt = $purgedAt;
    }

    public function getPurgedAt(): DateTimeInterface|string|null
    {
        return $this->purgedAt;
    }

    public function setSequenceId(string|int|null $sequenceId = null): void
    {
        $this->sequenceId = $sequenceId;
    }

    public function getSequenceId(): string|int|null
    {
        return $this->sequenceId;
    }

    public function setSha1(?string $sha1 = null): void
    {
        $this->sha1 = $sha1;
    }

    public function getSha1(): ?string
    {
        return $this->sha1;
    }

    public function setSharedLink(?SharedLink $sharedLink = null): void
    {
        $this->sharedLink = $sharedLink;
    }

    public function getSharedLink(): ?SharedLink
    {
        return $this->sharedLink;
    }

    public function setSize(?int $size = null): void
    {
        $this->size = $size;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setTrashedAt(DateTimeInterface|string|null $trashedAt = null): void
    {
        $this->trashedAt = $trashedAt;
    }

    public function getTrashedAt(): DateTimeInterface|string|null
    {
        return $this->trashedAt;
    }

    public function setType(string $type = "file"): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setVersionNumber(string|int|null $versionNumber = null): void
    {
        $this->versionNumber = $versionNumber;
    }

    public function getVersionNumber(): string|int|null
    {
        return $this->versionNumber;
    }

    public function setIsExternallyOwned(?bool $isExternallyOwned): void
    {
        $this->isExternallyOwned = $isExternallyOwned;
    }

    public function getIsExternallyOwned(): ?bool
    {
        return $this->isExternallyOwned;
    }

    public function setAllowedInviteRoles(mixed $allowedInviteRoles): void
    {
        $this->allowedInviteRoles = $allowedInviteRoles;
    }

    public function getAllowedInviteRoles(): mixed
    {
        return $this->allowedInviteRoles;
    }

    public function setHasCollaborations(?bool $hasCollaborations): void
    {
        $this->hasCollaborations = $hasCollaborations;
    }

    public function getHasCollaborations(): ?bool
    {
        return $this->hasCollaborations;
    }

    public function setMetadata(mixed $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getMetadata(): mixed
    {
        return $this->metadata;
    }
}
