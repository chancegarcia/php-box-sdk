<?php

/**
 * @package     Box
 * @subpackage  Box_File
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
 *
 */

namespace Box\File;

use Box\Mapper\Hydrator;
use Box\File\FileInterface;
use DateTimeInterface;
use Box\User\User;
use Box\Folder\Folder;
use Box\Item\SharedLink\SharedLink;

class File implements FileInterface
{
    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            (new Hydrator())->hydrate($this, $options);
        }
    }
    protected string|int|null $id = null;
    protected string $type = "file";
    protected ?string $sequenceId = null;
    protected ?string $etag = null;
    protected ?string $sha1 = null;
    protected ?string $name = null;
    protected ?string $description = null;
    protected ?int $size = null;
    protected mixed $pathCollection = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;
    protected DateTimeInterface|string|null $trashedAt = null;
    protected DateTimeInterface|string|null $purgedAt = null;
    protected DateTimeInterface|string|null $contentCreatedAt = null;
    protected DateTimeInterface|string|null $contentModifiedAt = null;
    protected mixed $createdBy = null;
    protected mixed $modifiedBy = null;
    protected mixed $ownedBy = null;
    protected mixed $sharedLink = null;
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

    /**
     * @param string|int|null $id
     * @return void
     */
    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    /**
     * @param int|null $commentCount
     * @return void
     */
    public function setCommentCount(?int $commentCount = null): void
    {
        $this->commentCount = $commentCount;
    }

    /**
     * @return int|null
     */
    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    /**
     * @param DateTimeInterface|string|null $contentCreatedAt
     * @return void
     */
    public function setContentCreatedAt(DateTimeInterface|string|null $contentCreatedAt = null): void
    {
        $this->contentCreatedAt = $contentCreatedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getContentCreatedAt(): DateTimeInterface|string|null
    {
        return $this->contentCreatedAt;
    }

    /**
     * @param DateTimeInterface|string|null $contentModifiedAt
     * @return void
     */
    public function setContentModifiedAt(DateTimeInterface|string|null $contentModifiedAt = null): void
    {
        $this->contentModifiedAt = $contentModifiedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getContentModifiedAt(): DateTimeInterface|string|null
    {
        return $this->contentModifiedAt;
    }

    /**
     * @param DateTimeInterface|string|null $createdAt
     * @return void
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    /**
     * @param User|array|null $createdBy
     *
     * @return void
     */
    public function setCreatedBy(mixed $createdBy = null): void
    {
        if (is_array($createdBy)) {
            // @todo v1.0 remove array support
        }

        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $etag
     * @return void
     */
    public function setEtag(?string $etag = null): void
    {
        $this->etag = $etag;
    }

    /**
     * @return string|null
     */
    public function getEtag(): ?string
    {
        return $this->etag;
    }

    /**
     * @param string|null $itemStatus
     * @return void
     */
    public function setItemStatus(?string $itemStatus = null): void
    {
        $this->itemStatus = $itemStatus;
    }

    /**
     * @return string|null
     */
    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

    /**
     * @param DateTimeInterface|string|null $modifiedAt
     * @return void
     */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    /**
     * @param User|array|null $modifiedBy
     *
     * @return void
     */
    public function setModifiedBy(mixed $modifiedBy = null): void
    {
        if (is_array($modifiedBy)) {
            // @todo v1.0 remove array support
        }

        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @return mixed
     */
    public function getModifiedBy(): mixed
    {
        return $this->modifiedBy;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name = null): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
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
     *
     * @return void
     */
    public function setOwnedBy(mixed $ownedBy = null): void
    {
        if (is_array($ownedBy)) {
            // @todo v1.0 remove array support
        }

        $this->ownedBy = $ownedBy;
    }

    /**
     * @return mixed
     */
    public function getOwnedBy(): mixed
    {
        return $this->ownedBy;
    }

    /**
     * @param Folder|array|null $parent
     *
     * @return void
     */
    public function setParent(mixed $parent = null): void
    {
        if (is_array($parent)) {
            // @todo v1.0 remove array support
        }

        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent(): mixed
    {
        return $this->parent;
    }

    /**
     * @param mixed $pathCollection
     *
     * @return void
     */
    public function setPathCollection(mixed $pathCollection = null): void
    {
        if (is_array($pathCollection)) {
            // @todo v1.0 remove array support once PathCollection DTO is implemented
        }

        $this->pathCollection = $pathCollection;
    }

    /**
     * @return mixed
     */
    public function getPathCollection(): mixed
    {
        return $this->pathCollection;
    }

    /**
     * @param mixed $permissions
     *
     * @return void
     */
    public function setPermissions(mixed $permissions = null): void
    {
        $this->permissions = $permissions;
    }

    /**
     * @return mixed
     */
    public function getPermissions(): mixed
    {
        return $this->permissions;
    }

    /**
     * @param DateTimeInterface|string|null $purgedAt
     * @return void
     */
    public function setPurgedAt(DateTimeInterface|string|null $purgedAt = null): void
    {
        $this->purgedAt = $purgedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getPurgedAt(): DateTimeInterface|string|null
    {
        return $this->purgedAt;
    }

    /**
     * @param string|int|null $sequenceId
     *
     * @return void
     */
    public function setSequenceId(string|int|null $sequenceId = null): void
    {
        $this->sequenceId = $sequenceId;
    }

    /**
     * @return string|int|null
     */
    public function getSequenceId(): string|int|null
    {
        return $this->sequenceId;
    }

    /**
     * @param string|null $sha1
     *
     * @return void
     */
    public function setSha1(?string $sha1 = null): void
    {
        $this->sha1 = $sha1;
    }

    /**
     * @return string|null
     */
    public function getSha1(): ?string
    {
        return $this->sha1;
    }

    /**
     * @param SharedLink|array|null $sharedLink
     *
     * @return void
     */
    public function setSharedLink(mixed $sharedLink = null): void
    {
        if (is_array($sharedLink)) {
            // @todo v1.0 remove array support
        }

        $this->sharedLink = $sharedLink;
    }

    /**
     * @return mixed
     */
    public function getSharedLink(): mixed
    {
        return $this->sharedLink;
    }

    /**
     * @param int|null $size
     * @return void
     */
    public function setSize(?int $size = null): void
    {
        $this->size = $size;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param DateTimeInterface|string|null $trashedAt
     * @return void
     */
    public function setTrashedAt(DateTimeInterface|string|null $trashedAt = null): void
    {
        $this->trashedAt = $trashedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getTrashedAt(): DateTimeInterface|string|null
    {
        return $this->trashedAt;
    }

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType(string $type = "file"): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string|int|null $versionNumber
     *
     * @return void
     */
    public function setVersionNumber(string|int|null $versionNumber = null): void
    {
        $this->versionNumber = $versionNumber;
    }

    /**
     * @return string|int|null
     */
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
