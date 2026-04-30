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

use Box\Exception\BoxException;
use Box\Model\Model;
use Box\File\FileInterface;

class File extends Model implements FileInterface
{
    protected mixed $id = null;
    protected mixed $type = "file";
    protected mixed $sequenceId = null;
    protected mixed $etag = null;
    protected mixed $sha1 = null;
    protected mixed $name = null;
    protected mixed $description = null;
    protected mixed $size = null;
    protected mixed $pathCollection = null;
    protected mixed $createdAt = null;
    protected mixed $modifiedAt = null;
    protected mixed $trashedAt = null;
    protected mixed $purgedAt = null;
    protected mixed $contentCreatedAt = null;
    protected mixed $contentModifiedAt = null;
    protected mixed $createdBy = null;
    protected mixed $modifiedBy = null;
    protected mixed $ownedBy = null;
    protected mixed $sharedLink = null;
    protected mixed $parent = null;
    protected mixed $itemStatus = null;

    // the following will not appear in default file requests and must be explicitly asked for using the fields parameter.
    protected mixed $versionNumber = null;
    protected mixed $commentCount = null;
    protected mixed $permissions = null;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setId(mixed $id = null): void
    {
        if (!is_numeric($id))
        {
            $id = null;
        }

        $this->id = $id;

    }

    /**
     * @param mixed $commentCount
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setCommentCount($commentCount = null)
    {
        $this->commentCount = $commentCount;

    }

    /**
     * @return mixed
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * @param mixed $contentCreatedAt
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setContentCreatedAt($contentCreatedAt = null)
    {
        $this->contentCreatedAt = $contentCreatedAt;

    }

    /**
     * @return mixed
     */
    public function getContentCreatedAt()
    {
        return $this->contentCreatedAt;
    }

    /**
     * @param mixed $contentModifiedAt
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setContentModifiedAt($contentModifiedAt = null)
    {
        $this->contentModifiedAt = $contentModifiedAt;

    }

    /**
     * @return mixed
     */
    public function getContentModifiedAt()
    {
        return $this->contentModifiedAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdBy
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $description
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $etag
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setEtag($etag = null)
    {
        $this->etag = $etag;

    }

    /**
     * @return mixed
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * @param mixed $itemStatus
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setItemStatus($itemStatus = null)
    {
        $this->itemStatus = $itemStatus;

    }

    /**
     * @return mixed
     */
    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    /**
     * @param mixed $modifiedAt
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setModifiedAt($modifiedAt = null)
    {
        $this->modifiedAt = $modifiedAt;

    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedBy
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setModifiedBy($modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

    }

    /**
     * @return mixed
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param mixed $name
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setName($name = null)
    {
        $this->name = $name;

    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $ownedBy
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setOwnedBy($ownedBy = null)
    {
        $this->ownedBy = $ownedBy;

    }

    /**
     * @return mixed
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    /**
     * @param mixed $parent
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $pathCollection
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setPathCollection($pathCollection = null)
    {
        $this->pathCollection = $pathCollection;

    }

    /**
     * @return mixed
     */
    public function getPathCollection()
    {
        return $this->pathCollection;
    }

    /**
     * @param mixed $permissions
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setPermissions($permissions = null)
    {
        $this->permissions = $permissions;

    }

    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param mixed $purgedAt
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setPurgedAt($purgedAt = null)
    {
        $this->purgedAt = $purgedAt;

    }

    /**
     * @return mixed
     */
    public function getPurgedAt()
    {
        return $this->purgedAt;
    }

    /**
     * @param mixed $sequenceId
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setSequenceId($sequenceId = null)
    {
        $this->sequenceId = $sequenceId;

    }

    /**
     * @return mixed
     */
    public function getSequenceId()
    {
        return $this->sequenceId;
    }

    /**
     * @param mixed $sha1
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setSha1($sha1 = null)
    {
        $this->sha1 = $sha1;

    }

    /**
     * @return mixed
     */
    public function getSha1()
    {
        return $this->sha1;
    }

    /**
     * @param mixed $sharedLink
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setSharedLink(mixed $sharedLink = null): void
    {
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
     * @param mixed $size
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setSize($size = null)
    {
        $this->size = $size;

    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $trashedAt
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setTrashedAt($trashedAt = null)
    {
        $this->trashedAt = $trashedAt;

    }

    /**
     * @return mixed
     */
    public function getTrashedAt()
    {
        return $this->trashedAt;
    }

    /**
     * @param mixed $type
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setType($type = null)
    {
        $this->type = $type;

    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $versionNumber
     *
     * @return \Box\File\File|\Box\File\FileInterface
     */
    public function setVersionNumber($versionNumber = null)
    {
        $this->versionNumber = $versionNumber;

    }

    /**
     * @return mixed
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

}
