<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/29/15
 * Time: 3:34 PM
 * @package     Box
 * @subpackage  Box_Model
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

namespace Box\Event\Collection\Entry\Source;

use Box\Model\Model;

class EntrySource extends Model implements SourceInterface
{
    protected $type;
    protected $id;
    protected $etag;
    protected $name;
    protected $createdAt;
    protected $modifiedAt;
    protected $description;
    protected $size;
    protected $createdBy;
    protected $modifiedBy;
    protected $ownedBy;
    protected $sharedLink;
    protected $parent;
    protected $itemStatus;
    protected $synced;
    protected $sequenceId;

    /**
     * @return mixed
     */
    public function getSequenceId()
    {
        return $this->sequenceId;
    }

    /**
     * @param mixed $sequenceId
     *
     * @return SourceInterface
     */
    public function setSequenceId($sequenceId = null)
    {
        $this->sequenceId = $sequenceId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return SourceInterface
     */
    public function setType($type = null)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return SourceInterface
     */
    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * @param mixed $etag
     *
     * @return SourceInterface
     */
    public function setEtag($etag = null)
    {
        $this->etag = $etag;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return SourceInterface
     */
    public function setName($name = null)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return SourceInterface
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedAt
     *
     * @return SourceInterface
     */
    public function setModifiedAt($modifiedAt = null)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return SourceInterface
     */
    public function setDescription($description = null)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     *
     * @return SourceInterface
     */
    public function setSize($size = null)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     *
     * @return SourceInterface
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param mixed $modifiedBy
     *
     * @return SourceInterface
     */
    public function setModifiedBy($modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @return mixed
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    /**
     * @param mixed $ownedBy
     *
     * @return SourceInterface
     */
    public function setOwnedBy($ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
    }

    /**
     * @return mixed
     */
    public function getSharedLink()
    {
        return $this->sharedLink;
    }

    /**
     * @param mixed $sharedLink
     *
     * @return SourceInterface
     */
    public function setSharedLink($sharedLink = null)
    {
        $this->sharedLink = $sharedLink;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     *
     * @return SourceInterface
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    /**
     * @param mixed $itemStatus
     *
     * @return SourceInterface
     */
    public function setItemStatus($itemStatus = null)
    {
        $this->itemStatus = $itemStatus;
    }

    /**
     * @return mixed
     */
    public function getSynced()
    {
        return $this->synced;
    }

    /**
     * @param mixed $synced
     *
     * @return SourceInterface
     */
    public function setSynced($synced = null)
    {
        $this->synced = $synced;
    }
}
