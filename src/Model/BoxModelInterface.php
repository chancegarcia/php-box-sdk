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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
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
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setSize(?int $size = null): void;

 /**
 * @return \DateTimeInterface|string|null
 */
 public function getCreatedAt(): \DateTimeInterface|string|null;

 /**
 * @param \DateTimeInterface|string|null $createdAt
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setCreatedAt(\DateTimeInterface|string|null $createdAt = null): void;

 /**
 * @return \DateTimeInterface|string|null
 */
 public function getModifiedAt(): \DateTimeInterface|string|null;

 /**
 * @param \DateTimeInterface|string|null $modifiedAt
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setModifiedAt(\DateTimeInterface|string|null $modifiedAt = null): void;

 /**
 * @return \Box\User\User|array|null
 */
 public function getCreatedBy(): mixed;

 /**
 * @param \Box\User\User|array|null $createdBy
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setCreatedBy(mixed $createdBy = null): void;

 /**
 * @return \Box\User\User|array|null
 */
 public function getModifiedBy(): mixed;

 /**
 * @param \Box\User\User|array|null $modifiedBy
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setModifiedBy(mixed $modifiedBy = null): void;

 /**
 * @return \Box\User\User|array|null
 */
 public function getOwnedBy(): mixed;

 /**
 * @param \Box\User\User|array|null $ownedBy
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setOwnedBy(mixed $ownedBy = null): void;

 /**
 * @return \Box\Item\SharedLink\SharedLink|array|null
 */
 public function getSharedLink(): mixed;

 /**
 * @param \Box\Item\SharedLink\SharedLink|array|null $sharedLink
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setSharedLink(mixed $sharedLink = null): void;

 /**
 * @return \Box\Folder\Folder|array|null
 */
 public function getParent(): mixed;

 /**
 * @param \Box\Folder\Folder|array|null $parent
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setParent(mixed $parent = null): void;

 /**
 * @return string|null
 */
 public function getItemStatus(): ?string;

 /**
 * @param string|null $itemStatus
 *
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setItemStatus(?string $itemStatus = null): void;
}
