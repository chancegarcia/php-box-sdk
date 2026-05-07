<?php

/**
 * Created by PhpStorm .
 * User: chance
 * Date: 9/29/15
 * Time: 3:31 PM
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

use DateTimeInterface;
use Box\User\User;
use Box\Item\SharedLink\SharedLink;
use Box\Folder\Folder;

interface BoxModelInterface
{
 /**
 * @return string
 */
    public function getType(): string;

 /**
 * @param string $type
 *
 * @return void
 */
    public function setType(string $type = "file"): void;

 /**
 * @return string|int|null
 */
    public function getId(): string|int|null;

 /**
 * @param string|int|null $id
 *
 * @return void
 */
    public function setId(string|int|null $id = null): void;

 /**
 * @return string|int|null
 */
    public function getSequenceId(): string|int|null;

 /**
 * @param string|int|null $sequenceId
 *
 * @return void
 */
    public function setSequenceId(string|int|null $sequenceId = null): void;

 /**
 * @return string|null
 */
    public function getEtag(): ?string;

 /**
 * @param string|null $etag
 *
 * @return void
 */
    public function setEtag(?string $etag = null): void;

 /**
 * @return string|null
 */
    public function getName(): ?string;

 /**
 * @param string|null $name
 *
 * @return void
 */
    public function setName(?string $name = null): void;

 /**
 * @return string|null
 */
    public function getDescription(): ?string;

 /**
 * @param string|null $description
 *
 * @return void
 */
    public function setDescription(?string $description = null): void;

 /**
 * @return int|null
 */
    public function getSize(): ?int;

 /**
 * @param int|null $size
 *
 * @return void
 */
    public function setSize(?int $size = null): void;

 /**
 * @return DateTimeInterface|string|null
 */
    public function getCreatedAt(): DateTimeInterface|string|null;

 /**
 * @param DateTimeInterface|string|null $createdAt
 *
 * @return void
 */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void;

 /**
 * @return DateTimeInterface|string|null
 */
    public function getModifiedAt(): DateTimeInterface|string|null;

 /**
 * @param DateTimeInterface|string|null $modifiedAt
 *
 * @return void
 */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void;

 /**
 * @return User|array|null
 */
    public function getCreatedBy(): mixed;

 /**
 * @param string|null $createdBy
 *
 * @return void
 */
    public function setCreatedBy(?string $createdBy = null): void;

 /**
 * @return User|array|null
 */
    public function getModifiedBy(): ?string;

 /**
 * @param string|null $modifiedBy
 *
 * @return void
 */
    public function setModifiedBy(?string $modifiedBy = null): void;

 /**
 * @return User|array|null
 */
    public function getOwnedBy(): mixed;

 /**
 * @param string|null $ownedBy
 *
 * @return void
 */
    public function setOwnedBy(?string $ownedBy = null): void;

 /**
 * @return SharedLink|array|null
 */
    public function getSharedLink(): mixed;

 /**
 * @param string|null $sharedLink
 *
 * @return void
 */
    public function setSharedLink(?string $sharedLink = null): void;

 /**
 * @return Folder|array|null
 */
    public function getParent(): Folder|array|null;

    /**
     * @param Folder|array|null $parent
     *
     * @return void
     */
    public function setParent(Folder|array|null $parent = null): void;

 /**
 * @return string|null
 */
    public function getItemStatus(): ?string;

 /**
 * @param string|null $itemStatus
 *
 * @return void
 */
    public function setItemStatus(?string $itemStatus = null): void;
}
