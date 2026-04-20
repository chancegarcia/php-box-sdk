<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/24/15
 * Time: 4:52 PM
 * @package     Box
 * @subpackage  Box_Exception
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

namespace Box\Exception;

use Box\Model\Connection\Token\TokenInterface;
use Box\Storage\Token\BaseTokenStorageInterface;

class TokenStorageException extends \Exception
{
    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var TokenInterface
     */
    protected $previousToken;

    /**
     * @var BaseTokenStorageInterface
     */
    protected $tokenStorage;
    protected $tokenStorageContext;
    protected $callingClass;

    /**
     * @return TokenInterface
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param TokenInterface $token
     *
     * @return TokenStorageException
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;

    }

    /**
     * @return TokenInterface
     */
    public function getPreviousToken()
    {
        return $this->previousToken;
    }

    /**
     * @param TokenInterface $previousToken
     *
     * @return TokenStorageException
     */
    public function setPreviousToken(TokenInterface $previousToken = null)
    {
        $this->previousToken = $previousToken;

    }

    /**
     * @return BaseTokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @param BaseTokenStorageInterface $tokenStorage
     *
     * @return TokenStorageException
     */
    public function setTokenStorage($tokenStorage = null)
    {
        $this->tokenStorage = $tokenStorage;

    }

    /**
     * @return mixed
     */
    public function getTokenStorageContext()
    {
        return $this->tokenStorageContext;
    }

    /**
     * @param mixed $tokenStorageContext
     *
     * @return TokenStorageException
     */
    public function setTokenStorageContext($tokenStorageContext = null)
    {
        $this->tokenStorageContext = $tokenStorageContext;

    }

    /**
     * @return mixed
     */
    public function getCallingClass()
    {
        return $this->callingClass;
    }

    /**
     * @param mixed $callingClass
     *
     * @return TokenStorageException
     */
    public function setCallingClass($callingClass = null)
    {
        $this->callingClass = $callingClass;

    }
}