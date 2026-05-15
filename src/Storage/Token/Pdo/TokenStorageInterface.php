<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/22/15
 * Time: 2:17 PM
 *
 * @package     Box
 * @subpackage  Box_Storage
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

namespace Box\Storage\Token\Pdo;

use Box\Storage\Token\TokenStorageInterface as BaseInterface;
use PDO;

interface TokenStorageInterface extends BaseInterface
{
    public function getDsn(): ?string;

    public function setDsn(?string $dsn = null): void;

    public function getUsername(): ?string;

    public function setUsername(?string $username = null): void;

    public function getPassword(): ?string;

    public function setPassword(?string $password = null): void;

    public function getOptions(): array;

    public function setOptions(?array $options = null): void;

    public function getPdo(): ?PDO;

    public function setPdo(?PDO $pdo = null): void;

    public function getTokenTableName(): string;

    public function setTokenTableName(?string $tokenTableName = null): void;

    public function getTokenMap(): array;

    public function setTokenMap(?array $tokenMap = null): void;
}
