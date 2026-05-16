<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/28/15
 * Time: 12:47 PM
 *
 * @package     Box
 * @subpackage  Box_Model
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
    protected $synced;

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
     * @return void
     */
    public function setSequenceId(string|int|null $sequenceId): void
    {
        $this->sequenceId = $sequenceId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType(string $type): void
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
     * @return void
     */
    public function setId(string|int|null $id): void
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
     * @param string|null $etag
     *
     * @return void
     */
    public function setEtag(?string $etag): void
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
     *
     * @return void
     */
    public function setName(?string $name): void
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
     *
     * @return void
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt): void
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
     * @return void
     */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt): void
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
     * @return void
     */
    public function setDescription(?string $description): void
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
     *
     * @return void
     */
    public function setSize(?int $size): void
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
     *
     * @return void
     */
    public function setCreatedBy(?string $createdBy): void
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
     * @return void
     */
    public function setModifiedBy(?string $modifiedBy): void
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
     * @return void
     */
    public function setOwnedBy(?string $ownedBy): void
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
     *
     * @return void
     */
    public function setSharedLink(?string $sharedLink): void
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
     * @param Folder|array|null $parent
     *
     * @return void
     */
    public function setParent(Folder|array|null $parent): void
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
     *
     * @return void
     */
    public function setItemStatus(?string $itemStatus): void
    {
        $this->itemStatus = $itemStatus;
    }

    /**
     * @return mixed
     */
    public function getSynced(): mixed
    {
        return $this->synced;
    }

    /**
     * @param mixed $synced
     *
     * @return void
     */
    public function setSynced(mixed $synced): void
    {
        $this->synced = $synced;
    }
}
