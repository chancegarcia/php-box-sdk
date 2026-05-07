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

use Box\Folder\Folder;
use Box\Model\Model;
use DateTimeInterface;

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
     * @return string|int|null
     */
    public function getSequenceId(): string|int|null
    {
        return $this->sequenceId;
    }

    /**
     * @param string|int|null $sequenceId
     *
     * @return SourceInterface
     */
    public function setSequenceId(string|int|null $sequenceId = null): void
    {
        $this->sequenceId = $sequenceId;
    }

    /**
     * @return mixed
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     */
    public function setType(string $type = "file"): void
    {
        $this->type = $type;
    }

    /**
     * @return int|string|null
     */
    public function getId(): int|string|null
    {
        return $this->id;
    }

    /**
     * @param string|int|null $id
     *
     */
    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getEtag(): ?string
    {
        return $this->etag;
    }

    /**
     * @param mixed $etag
     *
     */
    public function setEtag(?string $etag = null): void
    {
        $this->etag = $etag;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name = null): void
    {
        $this->name = $name;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface|string|null $createdAt
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTimeInterface|string|null $modifiedAt
     *
     * @return SourceInterface
     */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     */
    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     */
    public function setSize(?int $size = null): void
    {
        $this->size = $size;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * @param string|null $createdBy
     */
    public function setCreatedBy(?string $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return string|null
     */
    public function getModifiedBy(): ?string
    {
        return $this->modifiedBy;
    }

    /**
     * @param string|null $modifiedBy
     *
     */
    public function setModifiedBy(?string $modifiedBy = null): void
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @return string|null
     */
    public function getOwnedBy(): ?string
    {
        return $this->ownedBy;
    }

    /**
     * @param string|null $ownedBy
     *
     * @return SourceInterface
     */
    public function setOwnedBy(?string $ownedBy = null): void
    {
        $this->ownedBy = $ownedBy;
    }

    /**
     * @return string|null
     */
    public function getSharedLink(): ?string
    {
        return $this->sharedLink;
    }

    /**
     * @param string|null $sharedLink
     */
    public function setSharedLink(?string $sharedLink = null): void
    {
        $this->sharedLink = $sharedLink;
    }

    /**
     * @return Folder|array|null
     */
    public function getParent(): Folder|array|null
    {
        return $this->parent;
    }

    /**
     * @param string|null $parent
     */
    public function setParent(Folder|array|null $parent = null): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string|null
     */
    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

    /**
     * @param string|null $itemStatus
     */
    public function setItemStatus(?string $itemStatus = null): void
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
