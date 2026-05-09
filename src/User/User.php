<?php

/**
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

namespace Box\User;

use Box\Model\Model;
use DateTimeInterface;

/**
 * @deprecated Use Box\Resource\User instead. This class will be removed in v1.0.
 */
class User extends Model
{
    protected mixed $type = "user";
    protected mixed $id = null;
    protected mixed $name = null;
    protected mixed $login = null;
    protected mixed $createdAt = null;
    protected mixed $modifiedAt = null;
    protected mixed $role = null; // admin, coadmin, user only
    protected mixed $language = null;
    protected mixed $spaceAmount = null;
    protected mixed $spaceUsed = null;
    protected mixed $maxUploadSize = null;
    protected mixed $trackingCodes = null;
    protected mixed $canSeeManagedUsers = null;
    protected mixed $isSyncEnabled = null;
    protected mixed $status = null;
    protected mixed $jobTitle = null;
    protected mixed $phone = null;
    protected mixed $address = null;
    protected mixed $avatarUrl = null;
    protected mixed $isExemptFromDeviceLimits = null;
    protected mixed $isExemptFromLoginVerification = null;
    protected mixed $enterprise = null;
    protected ?string $timezone = null;
    protected ?bool $isExternalCollabRestricted = null;

    /**
     * @param string|int|null $id
     * @return void
     * @todo v1.0 strict string type
     */
    public function setId(mixed $id = null): void
    {
        $this->id = $id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setAddress($address = null)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAvatarUrl($avatarUrl = null)
    {
        $this->avatarUrl = $avatarUrl;
    }

    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    public function setCanSeeManagedUsers($canSeeManagedUsers = null)
    {
        $this->canSeeManagedUsers = $canSeeManagedUsers;
    }

    public function getCanSeeManagedUsers()
    {
        return $this->canSeeManagedUsers;
    }

    /**
     * @param DateTimeInterface|string|null $createdAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setCreatedAt($createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param object|array|null $enterprise
     * @return void
     */
    public function setEnterprise($enterprise = null): void
    {
        if (is_array($enterprise)) {
            // @todo v1.0 remove array support
        }

        $this->enterprise = $enterprise;
    }

    public function getEnterprise()
    {
        return $this->enterprise;
    }

    public function setIsExemptFromDeviceLimits($isExemptFromDeviceLimits = null)
    {
        $this->isExemptFromDeviceLimits = $isExemptFromDeviceLimits;
    }

    public function getIsExemptFromDeviceLimits()
    {
        return $this->isExemptFromDeviceLimits;
    }

    public function setIsExemptFromLoginVerification($isExemptFromLoginVerification = null)
    {
        $this->isExemptFromLoginVerification = $isExemptFromLoginVerification;
    }

    public function getIsExemptFromLoginVerification()
    {
        return $this->isExemptFromLoginVerification;
    }

    public function setIsSyncEnabled($isSyncEnabled = null)
    {
        $this->isSyncEnabled = $isSyncEnabled;
    }

    public function getIsSyncEnabled()
    {
        return $this->isSyncEnabled;
    }

    public function setJobTitle($jobTitle = null)
    {
        $this->jobTitle = $jobTitle;
    }

    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    public function setLanguage($language = null)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string|null $login
     * @return void
     */
    public function setLogin($login = null): void
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param float|int|null $maxUploadSize
     * @return void
     */
    public function setMaxUploadSize($maxUploadSize = null): void
    {
        $this->maxUploadSize = $maxUploadSize;
    }

    public function getMaxUploadSize()
    {
        return $this->maxUploadSize;
    }

    /**
     * @param DateTimeInterface|string|null $modifiedAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setModifiedAt($modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName($name = null): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPhone($phone = null)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setRole($role = null)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setSpaceAmount($spaceAmount = null)
    {
        $this->spaceAmount = $spaceAmount;
    }

    public function getSpaceAmount()
    {
        return $this->spaceAmount;
    }

    public function setSpaceUsed($spaceUsed = null)
    {
        $this->spaceUsed = $spaceUsed;
    }

    public function getSpaceUsed()
    {
        return $this->spaceUsed;
    }

    /**
     * @param string|null $status
     * @return void
     * @todo v1.0 Enum status
     */
    public function setStatus($status = null): void
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setTrackingCodes($trackingCodes = null)
    {
        $this->trackingCodes = $trackingCodes;
    }

    public function getTrackingCodes()
    {
        return $this->trackingCodes;
    }

    public function setType($type = null)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setIsExternalCollabRestricted(?bool $isExternalCollabRestricted): void
    {
        $this->isExternalCollabRestricted = $isExternalCollabRestricted;
    }

    public function getIsExternalCollabRestricted(): ?bool
    {
        return $this->isExternalCollabRestricted;
    }
}
