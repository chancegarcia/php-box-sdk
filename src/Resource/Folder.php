<?php

/**
 * @package     Box
 * @subpackage  Box_Resource
 *
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

use Box\Exception\BoxException;
use Countable;
use DateTimeInterface;

class Folder
{
    protected string $type = "folder";
    protected string|int|null $id = null;
    protected mixed $sequenceId = null;
    protected mixed $etag = null;
    protected mixed $name = null;
    protected mixed $createdAt = null;
    protected mixed $modifiedAt = null;
    protected mixed $description = null;
    protected mixed $size = null;
    protected mixed $pathCollection = null;
    protected mixed $createdBy = null;
    protected mixed $modifiedBy = null;
    protected mixed $ownedBy = null;
    protected mixed $sharedLink = null;
    protected mixed $folderUploadEmail = null;
    protected mixed $parent = null;
    protected mixed $itemStatus = null;
    protected mixed $itemCollection = null;
    protected mixed $syncState = null;
    protected ?bool $canNonOwnersInvite = null;
    protected ?array $allowedInviteRoles = null;
    protected mixed $hasCollaborations = null;

    /**
     * @param string $syncState
     *
     * @throws BoxException
     * @return array
     */
    public function classArray(string $syncState = "synced"): array
    {
        $aFolder = [
            'type' => $this->type,
            'id' => $this->getId(),
            'name' => $this->name,
            'description' => $this->description,
        ];

        if (
            !in_array(
                $syncState,
                [
                          "synced",
                          "not_synced",
                          "partially_synced"
                ]
            )
        ) {
            throw new BoxException("invalid sync state value given (" . var_export($syncState, true) . ").\n
            Expecting one of the following values: synced, not_synced, partially_synced
            ");
        }

        $aFolder['parent'] = [
            "id" => $this->getParentId()
        ];

        $aFolder['sync_state'] = $syncState;

        return $aFolder;
    }

    /**
     * @return int|string
     */
    public function getParentId()
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

    /**
     * @return DateTimeInterface|string|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription($description = null): void
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $etag
     *
     * @return void
     */
    public function setEtag($etag = null): void
    {
        $this->etag = $etag;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function setFolderUploadEmail($folderUploadEmail = null)
    {
        $this->folderUploadEmail = $folderUploadEmail;
    }

    public function getFolderUploadEmail()
    {
        return $this->folderUploadEmail;
    }

    public function setHasCollaborations($hasCollaborations = null)
    {
        $this->hasCollaborations = $hasCollaborations;
    }

    public function getHasCollaborations()
    {
        return $this->hasCollaborations;
    }

    public function setItemCollection($itemCollection = null)
    {
        $this->itemCollection = $itemCollection;
    }

    public function getItemCollection()
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

    public function getItemStatus()
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
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function setModifiedBy($modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName($name = null): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setOwnedBy($ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
    }

    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    public function setParent($parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setPathCollection($pathCollection = null)
    {
        $this->pathCollection = $pathCollection;
    }

    public function getPathCollection()
    {
        return $this->pathCollection;
    }

    public function setSequenceId($sequenceId = null)
    {
        $this->sequenceId = $sequenceId;
    }

    public function getSequenceId()
    {
        return $this->sequenceId;
    }

    public function setSharedLink($sharedLink = null)
    {
        $this->sharedLink = $sharedLink;
    }

    public function getSharedLink()
    {
        return $this->sharedLink;
    }

    /**
     * @param int|null $size
     *
     * @return void
     */
    public function setSize($size = null): void
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSyncState($syncState = null)
    {
        $this->syncState = $syncState;
    }

    public function getSyncState()
    {
        return $this->syncState;
    }

    public function setType($type = null)
    {
        $this->type = $type;
    }

    public function getType()
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
