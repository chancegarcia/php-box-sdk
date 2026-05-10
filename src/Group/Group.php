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

use Box\Mapper\Hydrator;
use Box\Exception\BoxException;
use Box\Exception\GroupException;
use Box\Group\GroupInterface;
use DateTimeInterface;

class Group implements GroupInterface
{
    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            (new Hydrator())->hydrate($this, $options);
        }
    }
    protected string $type = 'group';
    protected string|int|null $id = null;
    protected ?string $name = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;

    public function getMembershipListUri(int|string $limit = 100, int|string $offset = 0): string
    {
        $selfId = $this->getId();
        if ($selfId === null) {
            throw new BoxException(
                "Please set the folder Id to retrieve items for this folder." . BoxException::MISSING_ID
            );
        }

        $uri = self::URI . "/" . $selfId . "/memberships" . "?offset=" . $offset . "&limit=" . $limit;

        return $uri;
    }

    /**
     * @param string|int|null $id
     * @return void
     */
    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    /**
     * @param DateTimeInterface|string|null $createdAt
     * @return void
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface|string|null $modifiedAt
     * @return void
     */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    /**
     * @param string|null $name
     *
     * @throws GroupException
     * @return void
     */
    public function setName(?string $name = null): void
    {
        if ($name !== null && strlen($name) > 255) {
            throw new GroupException(
                "Box only supports group names of 255 characters or less. " .
                "Names that will not be supported are the name “none” or a null name.",
                GroupException::INVALID_NAME
            );
        }

        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
