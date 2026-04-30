<?php
/**
 * @package     Box
 * @subpackage  Box_Collaboration
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

namespace Box\Collaboration;

use Box\Model\Model;
use Box\Exception\BoxException;
use Box\Collaboration\CollaborationInterface;

class Collaboration extends Model implements CollaborationInterface
{
    protected mixed $id = null;
    protected mixed $type = 'collaboration';
    protected mixed $createdBy = null;
    protected mixed $createdAt = null;
    protected mixed $modifiedAt = null;
    protected mixed $expiresAt = null;
    protected mixed $status = null;
    protected mixed $accessibleBy = null;
    protected mixed $role = null;
    protected mixed $acknowledgedAt = null;
    protected mixed $item = null;

    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param string|int|null $id
     * @return void
     * @todo v1.0 strict string type
     */
    public function setId(mixed $id = null): void
    {
        $this->id = $id;
    }

    public function setAccessibleBy($accessibleBy = null)
    {
        $this->accessibleBy = $accessibleBy;

    }

    public function getAccessibleBy()
    {
        return $this->accessibleBy;
    }

    /**
     * @param \DateTimeInterface|string|null $acknowledgedAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setAcknowledgedAt($acknowledgedAt = null): void
    {
        $this->acknowledgedAt = $acknowledgedAt;
    }

    /**
     * @return \DateTimeInterface|string|null
     */
    public function getAcknowledgedAt()
    {
        return $this->acknowledgedAt;
    }

    /**
     * @param \DateTimeInterface|string|null $createdAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setCreatedAt($createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeInterface|string|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param \DateTimeInterface|string|null $expiresAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setExpiresAt($expiresAt = null): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return \DateTimeInterface|string|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setItem($item = null)
    {
        $this->item = $item;

    }

    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param \DateTimeInterface|string|null $modifiedAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setModifiedAt($modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return \DateTimeInterface|string|null
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string|null $role
     * @return void
     * @todo v1.0 Enum role
     */
    public function setRole($role = null): void
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string|null $status
     * @return void
     * @todo v1.0 Enum status
     */
    public function setStatus($status = null): void
    {
        $status = strtolower($status); // normalize
        $acceptable = array(
            'accepted',
            'pending',
            'rejected'
        );

        if (!in_array($status, $acceptable))
        {
            $err['error'] = "sdk_invalid_collaboration_status";
            $err['error_description'] = "status can only be one of the following values: " . implode(', ', $acceptable);
            $this->error($err);
        }

        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setType($type = null)
    {
        $this->type = $type;

    }

    public function getType()
    {
        return $this->type;
    }


}
