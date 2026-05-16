<?php

/**
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

namespace Box\Resource;

use Box\Enum\CollaborationRole;
use Box\Enum\CollaborationStatus;
use DateTimeInterface;

class Collaboration
{
    protected string|int|null $id = null;
    protected string $type = 'collaboration';
    // mixed: hydrator may deliver a User or Group as array or object depending on API response shape
    protected mixed $createdBy = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;
    protected DateTimeInterface|string|null $expiresAt = null;
    protected ?CollaborationStatus $status = null;
    // mixed: accessible_by can be a User or Group; hydrator may deliver an array or object
    protected mixed $accessibleBy = null;
    protected ?CollaborationRole $role = null;
    protected DateTimeInterface|string|null $acknowledgedAt = null;
    // mixed: item can be a File or Folder; hydrator may deliver an array or object
    protected mixed $item = null;

    public function getId(): string|int|null
    {
        return $this->id;
    }

    /**
     * @param string|int|null $id
     *
     * @return void
     */
    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    public function setAccessibleBy(mixed $accessibleBy = null): void
    {
        $this->accessibleBy = $accessibleBy;
    }

    public function getAccessibleBy(): mixed
    {
        return $this->accessibleBy;
    }

    /**
     * @param DateTimeInterface|string|null $acknowledgedAt
     *
     * @return void
     */
    public function setAcknowledgedAt(DateTimeInterface|string|null $acknowledgedAt = null): void
    {
        $this->acknowledgedAt = $acknowledgedAt;
    }

    public function getAcknowledgedAt(): DateTimeInterface|string|null
    {
        return $this->acknowledgedAt;
    }

    /**
     * @param DateTimeInterface|string|null $createdAt
     *
     * @return void
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    public function setCreatedBy(mixed $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    /**
     * @param DateTimeInterface|string|null $expiresAt
     *
     * @return void
     */
    public function setExpiresAt(DateTimeInterface|string|null $expiresAt = null): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getExpiresAt(): DateTimeInterface|string|null
    {
        return $this->expiresAt;
    }

    public function setItem(mixed $item = null): void
    {
        $this->item = $item;
    }

    public function getItem(): mixed
    {
        return $this->item;
    }

    /**
     * @param DateTimeInterface|string|null $modifiedAt
     *
     * @return void
     */
    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    public function setRole(?CollaborationRole $role = null): void
    {
        $this->role = $role;
    }

    public function getRole(): ?CollaborationRole
    {
        return $this->role;
    }

    public function setStatus(?CollaborationStatus $status = null): void
    {
        $this->status = $status;
    }

    public function getStatus(): ?CollaborationStatus
    {
        return $this->status;
    }

    public function setType(string $type = 'collaboration'): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
