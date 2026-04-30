<?php
/**
 * @package     Box
 * @subpackage  Box_Connection
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

namespace Box\Connection\Token;
use Box\Connection\Response;
use Box\Model\Model;

class Token extends Model implements TokenInterface
{
    protected mixed $accessToken = null;
    protected mixed $refreshToken = null;
    protected mixed $grantType = "authorization_code";
    protected mixed $expiresIn = null;
    protected mixed $tokenType = null;
    protected array $restrictedTo = [];

    /**
     * @param mixed $expiresIn
     */
    public function setExpiresIn(mixed $expiresIn = null): void
    {
        $this->expiresIn = $expiresIn;
    }

    public function getExpiresIn(): mixed
    {
        return $this->expiresIn;
    }

    /**
     * @param mixed $tokenType
     */
    public function setTokenType(mixed $tokenType = null): void
    {
        $this->tokenType = $tokenType;
    }

    public function getTokenType(): mixed
    {
        return $this->tokenType;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken(mixed $accessToken = null): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): mixed
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $grantType
     */
    public function setGrantType(mixed $grantType = null): void
    {
        $this->grantType = $grantType;
    }

    public function getGrantType(): mixed
    {
        return $this->grantType;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken(mixed $refreshToken = null): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): mixed
    {
        return $this->refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRestrictedTo(): array
    {
        return $this->restrictedTo;
    }

    /**
     * {@inheritdoc}
     * @param array|null $restrictedTo
     */
    public function setRestrictedTo(?array $restrictedTo = null): void
    {
        $this->restrictedTo = $restrictedTo ?? [];
    }

    // all parameters must be url encoded

}
