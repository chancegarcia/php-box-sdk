<?php

/**
 * Created by PhpStorm .
 * User: chance
 * Date: 9/29/15
 * Time: 3:29 PM
 * @package Box
 * @subpackage Box_Model
 * @author Chance Garcia
 * @copyright (C)Copyright 2013 Chance Garcia, chancegarcia . com
 *
 * The MIT License (MIT)
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
 * copies or substantial portions of the Software .
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT . IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE .
 *
 */

namespace Box\Model;

use Box\Folder\Folder;
use DateTimeInterface;

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

    public function getType(): string
    {
        return $this->type;
    }

 /**
 * @param string $type
 * @return void
 */
    public function setType(string $type = "file"): void
    {
        $this->type = $type;
    }

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

    public function getSequenceId(): string|int|null
    {
        return $this->sequenceId;
    }

 /**
 * @param string|int|null $sequenceId
 * @return void
 */
    public function setSequenceId(string|int|null $sequenceId = null): void
    {
        $this->sequenceId = $sequenceId;
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

 /**
 * @param string|null $etag
 * @return void
 */
    public function setEtag(?string $etag = null): void
    {
        $this->etag = $etag;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

 /**
 * @param string|null $name
 * @return void
 */
    public function setName(?string $name = null): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

 /**
 * @param string|null $description
 * @return void
 */
    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

 /**
 * @param int|null $size
 * @return void
 */
    public function setSize(?int $size = null): void
    {
        $this->size = $size;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

 /**
 * @param DateTimeInterface|string|null $createdAt
 * @return void
 */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

 /**
 * @param DateTimeInterface|string|null $modifiedAt
 * @return void
 */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

 /**
 * @param string|null $createdBy
 *
 * @return void
 */
    public function setCreatedBy(?string $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    public function getModifiedBy(): mixed
    {
        return $this->modifiedBy;
    }

 /**
 * @param string|null $modifiedBy
 *
 * @return void
 */
    public function setModifiedBy(?string $modifiedBy = null): void
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function getOwnedBy(): mixed
    {
        return $this->ownedBy;
    }

 /**
 * @param string|null $ownedBy
 *
 * @return void
 */
    public function setOwnedBy(?string $ownedBy = null): void
    {
        $this->ownedBy = $ownedBy;
    }

    public function getSharedLink(): mixed
    {
        return $this->sharedLink;
    }

 /**
 * @param string|null $sharedLink
 *
 * @return void
 */
    public function setSharedLink(?string $sharedLink = null): void
    {
        $this->sharedLink = $sharedLink;
    }

    public function getParent(): Folder|array|null
    {
        return $this->parent;
    }

 /**
 * @param Folder|array|null $parent
 *
 * @return void
 */
    public function setParent(Folder|array|null $parent = null): void
    {
        $this->parent = $parent;
    }

    public function getItemStatus(): ?string
    {
        return $this->itemStatus;
    }

 /**
 * @param string|null $itemStatus
 * @return void
 */
    public function setItemStatus(?string $itemStatus = null): void
    {
        $this->itemStatus = $itemStatus;
    }
}
