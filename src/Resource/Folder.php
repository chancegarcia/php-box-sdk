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
use Countable;
use DateTimeInterface;

class Folder
{
    protected string $type = "folder";
    protected string|int|null $id = null;
    protected string|int|null $sequenceId = null;
    protected ?string $etag = null;
    protected ?string $name = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;
    protected ?string $description = null;
    protected ?int $size = null;
    protected ?PathCollection $pathCollection = null;
    // mixed: hydrator may deliver a User as array or object depending on API response shape
    protected mixed $createdBy = null;
    // mixed: hydrator may deliver a User as array or object depending on API response shape
    protected mixed $modifiedBy = null;
    // mixed: hydrator may deliver a User as array or object depending on API response shape
    protected mixed $ownedBy = null;
    // mixed: hydrator may deliver a SharedLink as array or object depending on API response shape
    protected mixed $sharedLink = null;
    // mixed: folder_upload_email is an optional nested object from the Box API
    protected mixed $folderUploadEmail = null;
    // mixed: hydrator may deliver a Folder as array or object depending on API response shape
    protected mixed $parent = null;
    protected ?string $itemStatus = null;
    // mixed: item_collection is a paginated collection structure from the Box API, not yet modeled as a DTO
    protected mixed $itemCollection = null;
    protected ?bool $canNonOwnersInvite = null;
    protected ?array $allowedInviteRoles = null;
    protected ?bool $hasCollaborations = null;

    public function getParentId(): string|int
    {
        $parent = $this->getParent();

        $parentId = 0;

        if (is_object($parent)) {
            /**
             * @var Folder $parent
             */
            $parentId = $parent->getId();
        }

        if (is_array($parent)) {
            $parentId = $parent['id'];
            return $parentId;
        }
        return $parentId;
    }

    /**
     * convenience function
     *
     * @return mixed
     */
    public function getItems(): mixed
    {
        return $this->getItemCollection();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        $itemCollection = $this->getItemCollection();

        if (is_array($itemCollection) && isset($itemCollection['total_count'])) {
            return 0 === (int) $itemCollection['total_count'];
        }

        if ($itemCollection instanceof Countable) {
            return 0 === count($itemCollection);
        }

        if (is_array($itemCollection)) {
            return 0 === count($itemCollection);
        }

        return true;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    /**
     * @param string|int|null $id
     *
     * @return void
     */
    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    /**
     * @param DateTimeInterface|string|null $createdAt
     *
     * @return void
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    public function setCreatedBy(mixed $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    /**
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $etag
     *
     * @return void
     */
    public function setEtag(?string $etag = null): void
    {
        $this->etag = $etag;
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function setFolderUploadEmail(mixed $folderUploadEmail = null): void
    {
        $this->folderUploadEmail = $folderUploadEmail;
    }

    public function getFolderUploadEmail(): mixed
    {
        return $this->folderUploadEmail;
    }

    public function setHasCollaborations(?bool $hasCollaborations = null): void
    {
        $this->hasCollaborations = $hasCollaborations;
    }

    public function getHasCollaborations(): ?bool
    {
        return $this->hasCollaborations;
    }

    public function setItemCollection(mixed $itemCollection = null): void
    {
        $this->itemCollection = $itemCollection;
    }

    public function getItemCollection(): mixed
    {
        return $this->itemCollection;
    }

    /**
     * @param string|null $itemStatus
     *
     * @return void
     */
    public function setItemStatus(?string $itemStatus = null): void
    {
        $this->itemStatus = $itemStatus;
    }

    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

    /**
     * @param DateTimeInterface|string|null $modifiedAt
     *
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

    public function setModifiedBy(mixed $modifiedBy = null): void
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function getModifiedBy(): mixed
    {
        return $this->modifiedBy;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name = null): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setOwnedBy(mixed $ownedBy = null): void
    {
        $this->ownedBy = $ownedBy;
    }

    public function getOwnedBy(): mixed
    {
        return $this->ownedBy;
    }

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

    public function setSequenceId(string|int|null $sequenceId = null): void
    {
        $this->sequenceId = $sequenceId;
    }

    public function getSequenceId(): string|int|null
    {
        return $this->sequenceId;
    }

    public function setSharedLink(mixed $sharedLink = null): void
    {
        $this->sharedLink = $sharedLink;
    }

    public function getSharedLink(): mixed
    {
        return $this->sharedLink;
    }

    /**
     * @param int|null $size
     *
     * @return void
     */
    public function setSize(?int $size = null): void
    {
        $this->size = $size;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setType(string $type = 'folder'): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setCanNonOwnersInvite(?bool $canNonOwnersInvite): void
    {
        $this->canNonOwnersInvite = $canNonOwnersInvite;
    }

    public function getCanNonOwnersInvite(): ?bool
    {
        return $this->canNonOwnersInvite;
    }

    public function setAllowedInviteRoles(?array $allowedInviteRoles): void
    {
        $this->allowedInviteRoles = $allowedInviteRoles;
    }

    public function getAllowedInviteRoles(): ?array
    {
        return $this->allowedInviteRoles;
    }
}
