<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 10/9/15
 * Time: 5:48 PM
 * @package     Box
 * @subpackage  Box_Item
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

namespace Box\Item\SharedLink;

use Box\Item\SharedLink\Permissions\PermissionsInterface;
use Box\Item\SharedLink\SharedLinkInterface;
use Box\Model\Model;

class SharedLink extends Model implements SharedLinkInterface
{
    protected $access;
    protected $unsharedAt;
    protected $password;
    protected $permissions;
    protected $effectiveAccess;

    /**
     * {@inheritdoc}
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string|null $access
     * @return void
     */
    public function setAccess($access = null): void
    {
        $this->access = $access;
    }

    /**
     * @return \DateTimeInterface|string|null
     */
    public function getUnsharedAt()
    {
        return $this->unsharedAt;
    }

    /**
     * @param \DateTimeInterface|string|null $unsharedAt
     * @return void
     * @todo v1.0 \DateTimeImmutable|null type
     */
    public function setUnsharedAt($unsharedAt = null): void
    {
        $this->unsharedAt = $unsharedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return void
     */
    public function setPassword($password = null): void
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param PermissionsInterface|null $permissions
     * @return void
     */
    public function setPermissions(PermissionsInterface $permissions = null): void
    {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function getEffectiveAccess()
    {
        return $this->effectiveAccess;
    }
}