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

namespace Box\Model\Collaboration;

use Box\Model\Model;
use Box\Exception\BoxException;
use Box\Model\Collaboration\CollaborationInterface;

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

    public function setAcknowledgedAt($acknowledgedAt = null)
    {
        $this->acknowledgedAt = $acknowledgedAt;

    }

    public function getAcknowledgedAt()
    {
        return $this->acknowledgedAt;
    }

    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

    }

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

    public function setExpiresAt($expiresAt = null)
    {
        $this->expiresAt = $expiresAt;

    }

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

    public function setModifiedAt($modifiedAt = null)
    {
        $this->modifiedAt = $modifiedAt;

    }

    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function setRole($role = null)
    {
        $this->role = $role;

    }

    public function getRole()
    {
        return $this->role;
    }

    public function setStatus($status = null)
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
