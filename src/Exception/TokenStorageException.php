<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/24/15
 * Time: 4:52 PM
 *
 * @package     Box
 * @subpackage  Box_Exception
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

namespace Box\Exception;

use Box\Dto\TokenStorageContext;
use Box\Storage\Token\TokenStorageInterface;

class TokenStorageException extends \Exception
{
    protected ?TokenStorageInterface $tokenStorage = null;
    protected ?TokenStorageContext $tokenStorageContext = null;

    /**
     * @return TokenStorageInterface|null
     */
    public function getTokenStorage(): ?TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    /**
     * @param TokenStorageInterface|null $tokenStorage
     */
    public function setTokenStorage(?TokenStorageInterface $tokenStorage = null): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return TokenStorageContext|null
     */
    public function getTokenStorageContext(): ?TokenStorageContext
    {
        return $this->tokenStorageContext;
    }

    /**
     * @param TokenStorageContext|null $tokenStorageContext
     */
    public function setTokenStorageContext(?TokenStorageContext $tokenStorageContext = null): void
    {
        $this->tokenStorageContext = $tokenStorageContext;
    }
}
