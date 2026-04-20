<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/29/15
 * Time: 3:29 PM
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

namespace Box\Model;

class BoxModel implements BoxModelInterface
{
    protected $type;
    protected $id;
    protected $sequenceId;
    protected $etag;
    protected $name;
    protected $description;
    protected $size;
    protected $createdAt;
    protected $modifiedAt;
    protected $createdBy;
    protected $modifiedBy;
    protected $ownedBy;
    protected $sharedLink;
    protected $parent;
    protected $itemStatus;

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setType($type = null)
    {
        $this->type = $type;

    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setId($id = null)
    {
        $this->id = $id;

    }

    public function getSequenceId()
    {
        return $this->sequenceId;
    }

    /**
     * @param mixed $sequenceId
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setSequenceId($sequenceId = null)
    {
        $this->sequenceId = $sequenceId;

    }

    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * @param mixed $etag
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setEtag($etag = null)
    {
        $this->etag = $etag;

    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setName($name = null)
    {
        $this->name = $name;

    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

    }

    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setSize($size = null)
    {
        $this->size = $size;

    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

    }

    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedAt
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setModifiedAt($modifiedAt = null)
    {
        $this->modifiedAt = $modifiedAt;

    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

    }

    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param mixed $modifiedBy
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setModifiedBy($modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

    }

    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    /**
     * @param mixed $ownedBy
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setOwnedBy($ownedBy = null)
    {
        $this->ownedBy = $ownedBy;

    }

    public function getSharedLink()
    {
        return $this->sharedLink;
    }

    /**
     * @param mixed $sharedLink
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setSharedLink($sharedLink = null)
    {
        $this->sharedLink = $sharedLink;

    }

    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

    }

    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    /**
     * @param mixed $itemStatus
     * @return BoxModel
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setItemStatus($itemStatus = null)
    {
        $this->itemStatus = $itemStatus;

    }

}