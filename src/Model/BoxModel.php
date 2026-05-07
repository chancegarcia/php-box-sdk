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

 public function getType(): mixed
 {
 return $this->type;
 }

 /**
 * @param mixed $type
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setType($type = null): void
 {
 $this->type = $type;
 }

 public function getId(): mixed
 {
 return $this->id;
 }

 /**
 * @param mixed $id
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setId($id = null): void
 {
 $this->id = $id;
 }

 public function getSequenceId(): mixed
 {
 return $this->sequenceId;
 }

 /**
 * @param mixed $sequenceId
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setSequenceId($sequenceId = null): void
 {
 $this->sequenceId = $sequenceId;
 }

 public function getEtag(): mixed
 {
 return $this->etag;
 }

 /**
 * @param mixed $etag
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setEtag($etag = null): void
 {
 $this->etag = $etag;
 }

 public function getName(): mixed
 {
 return $this->name;
 }

 /**
 * @param mixed $name
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setName($name = null): void
 {
 $this->name = $name;
 }

 public function getDescription(): mixed
 {
 return $this->description;
 }

 /**
 * @param mixed $description
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setDescription($description = null): void
 {
 $this->description = $description;
 }

 public function getSize(): mixed
 {
 return $this->size;
 }

 /**
 * @param mixed $size
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setSize($size = null): void
 {
 $this->size = $size;
 }

 public function getCreatedAt(): mixed
 {
 return $this->createdAt;
 }

 /**
 * @param mixed $createdAt
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setCreatedAt($createdAt = null): void
 {
 $this->createdAt = $createdAt;
 }

 public function getModifiedAt(): mixed
 {
 return $this->modifiedAt;
 }

 /**
 * @param mixed $modifiedAt
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setModifiedAt($modifiedAt = null): void
 {
 $this->modifiedAt = $modifiedAt;
 }

 public function getCreatedBy(): mixed
 {
 return $this->createdBy;
 }

 /**
 * @param mixed $createdBy
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setCreatedBy($createdBy = null): void
 {
 $this->createdBy = $createdBy;
 }

 public function getModifiedBy(): mixed
 {
 return $this->modifiedBy;
 }

 /**
 * @param mixed $modifiedBy
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setModifiedBy($modifiedBy = null): void
 {
 $this->modifiedBy = $modifiedBy;
 }

 public function getOwnedBy(): mixed
 {
 return $this->ownedBy;
 }

 /**
 * @param mixed $ownedBy
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setOwnedBy($ownedBy = null): void
 {
 $this->ownedBy = $ownedBy;
 }

 public function getSharedLink(): mixed
 {
 return $this->sharedLink;
 }

 /**
 * @param mixed $sharedLink
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setSharedLink($sharedLink = null): void
 {
 $this->sharedLink = $sharedLink;
 }

 public function getParent(): mixed
 {
 return $this->parent;
 }

 /**
 * @param mixed $parent
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setParent($parent = null): void
 {
 $this->parent = $parent;
 }

 public function getItemStatus(): mixed
 {
 return $this->itemStatus;
 }

 /**
 * @param mixed $itemStatus
 * @return void
 * @deprecated since 0 . 11 . 0, use non-fluent setter instead . 
 */
 public function setItemStatus($itemStatus = null): void
 {
 $this->itemStatus = $itemStatus;
 }
}
