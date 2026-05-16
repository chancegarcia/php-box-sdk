<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 10/9/15
 * Time: 5:48 PM
 *
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

use Box\Enum\SharedLinkAccess;
use Box\Resource\SharedLink\Permissions\Permissions;
use DateTimeInterface;

class SharedLink
{
    protected ?SharedLinkAccess $access = null;
    protected DateTimeInterface|string|null $unsharedAt = null;
    protected ?string $password = null;
    protected ?Permissions $permissions = null;
    protected ?string $effectiveAccess = null;

    public function getAccess(): ?SharedLinkAccess
    {
        return $this->access;
    }

    public function setAccess(?SharedLinkAccess $access = null): void
    {
        $this->access = $access;
    }

    public function getUnsharedAt(): DateTimeInterface|string|null
    {
        return $this->unsharedAt;
    }

    public function setUnsharedAt(DateTimeInterface|string|null $unsharedAt = null): void
    {
        $this->unsharedAt = $unsharedAt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password = null): void
    {
        $this->password = $password;
    }

    public function getPermissions(): ?Permissions
    {
        return $this->permissions;
    }

    public function setPermissions(?Permissions $permissions = null): void
    {
        $this->permissions = $permissions;
    }

    public function getEffectiveAccess(): ?string
    {
        return $this->effectiveAccess;
    }

    public function setEffectiveAccess(?string $effectiveAccess = null): void
    {
        $this->effectiveAccess = $effectiveAccess;
    }

    public function toArray(): array
    {
        return [
            'access' => $this->access?->value,
            'unshared_at' => $this->unsharedAt,
            'password' => $this->password,
        ];
    }
}
