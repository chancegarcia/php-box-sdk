<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/22/15
 * Time: 2:14 PM
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

namespace Box\Storage\Token;

use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;

interface TokenStorageInterface
{
    /**
     * Store token to storage.
     * Implementation should handle one-active-token-per-context behavior.
     */
    public function storeToken(TokenInterface $token, TokenStorageContext $context): void;

    /**
     * Update token in storage for a given context.
     */
    public function updateToken(TokenInterface $token, TokenStorageContext $context): void;

    /**
     * Retrieve token from storage for a given context.
     *
     * @return TokenInterface|null Returns null if no token is found for the context.
     */
    public function retrieveToken(TokenStorageContext $context): ?TokenInterface;

    /**
     * Remove token from storage for a given context.
     */
    public function removeToken(TokenStorageContext $context): void;

    /**
     * Clear all tokens from storage.
     * Optional implementation for clearing the entire storage backend.
     */
    public function clear(): void;
}
