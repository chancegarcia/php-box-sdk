<?php
/**
 * @package     Box
 * @subpackage  Box_Folder
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

namespace Box\Model\Folder;

use Box\Exception\BoxException;
use Box\Model\Model;

class Folder extends Model implements FolderInterface
{
    protected string $type = "folder";
    protected mixed $id = null;
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
    protected mixed $hasCollaborations = null;

    public function classArray(string $syncState = "synced"): array
    {
        $aFolder = parent::classArray();

        if (!in_array($syncState,
                      array(
                          "synced",
                          "not_synced",
                          "partially_synced"
                      ))
        )
        {
            throw new BoxException("invalid sync state value given (" . var_export($syncState, true) . ").\n
            Expecting one of the following values: synced, not_synced, partially_synced
            ");
        }

        foreach ($aFolder as $key => $value)
        {
            $aAllowedRequestAttributes = array(
                "name",
                "description",
                "parent",
                "shared_link",
                "folder_upload_email",
                "owned_by"
            );

            if (!in_array($key, $aAllowedRequestAttributes))
            {
                unset($aFolder[$key]);
            }
        }

        if (null === $aFolder['shared_link'])
        {
            unset($aFolder['owned_by']);
        }

        $aFolder['parent'] = array(
            "id" => $this->getParentId()
        );

        $aFolder['sync_state'] = $syncState;

        return $aFolder;
    }

    public function getBoxFolderItemsUri($limit = 100, $offset = 0)
    {
        $selfId = $this->getId();
        if (!is_numeric($selfId))
        {
            throw new BoxException("Please set the folder Id to retrieve items for this folder."
                                   . BoxException::MISSING_ID);
        }

        if (!is_numeric($limit))
        {
            throw new BoxException("Limit must be a valid integer", BoxException::INVALID_INPUT);
        }

        if (!is_numeric($offset))
        {
            throw new BoxException("Offset must be a valid integer", BoxException::INVALID_INPUT);
        }

        $uri = self::URI . "/" . $selfId . "/items" . "?limit=" . $limit . "&offset=" . $offset;

        return $uri;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        $parent = $this->getParent();

        $parentId = 0;

        if (is_object($parent))
        {
            /**
             * @var \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface $parent
             */
            $parentId = $parent->getId();
        }

        if (is_array($parent))
        {
            $parentId = $parent['id'];
            return $parentId;
        }
        return $parentId;
    }

    /**
     * convenience function
     * @return mixed
     */
    public function getItems(): mixed
    {
        return $this->getItemCollection();
    }

    public function getId(): mixed
    {
        if (null === $this->id)
        {
            $this->setId(0);
        }

        return $this->id;
    }

    public function setId($id = null)
    {
        $this->id = $id;
        return $this;
    }

    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setDescription($description = null)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setEtag($etag = null)
    {
        $this->etag = $etag;
        return $this;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function setFolderUploadEmail($folderUploadEmail = null)
    {
        $this->folderUploadEmail = $folderUploadEmail;
        return $this;
    }

    public function getFolderUploadEmail()
    {
        return $this->folderUploadEmail;
    }

    public function setHasCollaborations($hasCollaborations = null)
    {
        $this->hasCollaborations = $hasCollaborations;
        return $this;
    }

    public function getHasCollaborations()
    {
        return $this->hasCollaborations;
    }

    public function setItemCollection($itemCollection = null)
    {
        $this->itemCollection = $itemCollection;
        return $this;
    }

    public function getItemCollection()
    {
        return $this->itemCollection;
    }

    public function setItemStatus($itemStatus = null)
    {
        $this->itemStatus = $itemStatus;
        return $this;
    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    public function setModifiedAt($modifiedAt = null)
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function setModifiedBy($modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    public function setName($name = null)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setOwnedBy($ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
        return $this;
    }

    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    public function setParent($parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setPathCollection($pathCollection = null)
    {
        $this->pathCollection = $pathCollection;
        return $this;
    }

    public function getPathCollection()
    {
        return $this->pathCollection;
    }

    public function setSequenceId($sequenceId = null)
    {
        $this->sequenceId = $sequenceId;
        return $this;
    }

    public function getSequenceId()
    {
        return $this->sequenceId;
    }

    public function setSharedLink($sharedLink = null)
    {
        $this->sharedLink = $sharedLink;
        return $this;
    }

    public function getSharedLink()
    {
        return $this->sharedLink;
    }

    public function setSize($size = null)
    {
        $this->size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSyncState($syncState = null)
    {
        $this->syncState = $syncState;
        return $this;
    }

    public function getSyncState()
    {
        return $this->syncState;
    }

    public function setType($type = null)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

}
