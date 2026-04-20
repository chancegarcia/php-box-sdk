<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/29/15
 * Time: 3:31 PM
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

interface BoxModelInterface
{
    /**
     * @return mixed
     */
    public function getType();
    
    /**
     * @param mixed $type
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setType($type = null);
    
    /**
     * @return mixed
     */
    public function getId();
    
    /**
     * @param mixed $id
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setId($id = null);
    
    /**
     * @return mixed
     */
    public function getSequenceId();
    
    /**
     * @param mixed $sequenceId
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setSequenceId($sequenceId = null);
    
    /**
     * @return mixed
     */
    public function getEtag();
    
    /**
     * @param mixed $etag
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setEtag($etag = null);
    
    /**
     * @return mixed
     */
    public function getName();
    
    /**
     * @param mixed $name
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setName($name = null);
    
    /**
     * @return mixed
     */
    public function getDescription();
    
    /**
     * @param mixed $description
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setDescription($description = null);
    
    /**
     * @return mixed
     */
    public function getSize();
    
    /**
     * @param mixed $size
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setSize($size = null);
    
    /**
     * @return mixed
     */
    public function getCreatedAt();
    
    /**
     * @param mixed $createdAt
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setCreatedAt($createdAt = null);
    
    /**
     * @return mixed
     */
    public function getModifiedAt();
    
    /**
     * @param mixed $modifiedAt
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setModifiedAt($modifiedAt = null);
    
    /**
     * @return mixed
     */
    public function getCreatedBy();
    
    /**
     * @param mixed $createdBy
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setCreatedBy($createdBy = null);
    
    /**
     * @return mixed
     */
    public function getModifiedBy();
    
    /**
     * @param mixed $modifiedBy
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setModifiedBy($modifiedBy = null);
    
    /**
     * @return mixed
     */
    public function getOwnedBy();
    
    /**
     * @param mixed $ownedBy
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setOwnedBy($ownedBy = null);
    
    /**
     * @return mixed
     */
    public function getSharedLink();
    
    /**
     * @param mixed $sharedLink
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setSharedLink($sharedLink = null);
    
    /**
     * @return mixed
     */
    public function getParent();
    
    /**
     * @param mixed $parent
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setParent($parent = null);
    
    /**
     * @return mixed
     */
    public function getItemStatus();
    
    /**
     * @param mixed $itemStatus
     *
     * @return BoxModelInterface
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setItemStatus($itemStatus = null);
    
}