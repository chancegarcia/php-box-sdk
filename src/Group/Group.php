<?php
/**
 * @package     Box
 * @subpackage  Box_Group
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2014 Chance Garcia, chancegarcia.com
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

namespace Box\Group;

use Box\Model\Model;
use Box\Exception\BoxException;
use Box\Exception\GroupException;
use Box\Group\GroupInterface;

class Group extends Model implements GroupInterface
{
    protected mixed $type = 'group';
    protected mixed $id = null;
    protected mixed $name = null;
    protected mixed $createdAt = null;
    protected mixed $modifiedAt = null;

    public function getMembershipListUri(mixed $limit = 100, mixed $offset = 0): string
    {
        $selfId = $this->getId();
        if (!is_numeric($selfId))
        {
            throw new BoxException("Please set the folder Id to retrieve items for this folder.". BoxException::MISSING_ID);
        }

        if (!is_numeric($limit))
        {
            throw new BoxException("Limit must be a valid integer", BoxException::INVALID_INPUT);
        }

        if (!is_numeric($offset))
        {
            throw new BoxException("Offset must be a valid integer", BoxException::INVALID_INPUT);
        }

        $uri = self::URI . "/" . $selfId . "/memberships" . "?offset=" . $offset . "&limit=" . $limit;

        return $uri;
    }

    public function setId(mixed $id = null): void
    {
        $this->id = $id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param mixed $createdAt
     * @return \Box\Group\Group|\Box\Group\GroupInterface
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
     * @param mixed $modifiedAt
     * @return \Box\Group\Group|\Box\Group\GroupInterface
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
     * @param mixed $name
     *
     * @throws GroupException
     * @return \Box\Group\Group|\Box\Group\GroupInterface
     */
    public function setName($name = null)
    {

        if (!is_string($name))
        {
            $name = null;
        } else if (strlen($name) > 255) {
            throw new GroupException(
                    "Box only supports group names of 255 characters or less. Names that will not be supported are the name “none” or a null name.",
                GroupException::INVALID_NAME
            );
        }

        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


}
