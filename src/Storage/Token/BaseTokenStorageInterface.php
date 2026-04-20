<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/22/15
 * Time: 2:14 PM
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
 *
 */

namespace Box\Storage\Token;

use Box\Model\Connection\Token\TokenInterface;

interface BaseTokenStorageInterface
{
    /**
     * add/insert/store token to storage
     *
     * @param \Box\Model\Connection\Token\TokenInterface $token
     *
     * @return bool return value is success of storage
     */
    public function storeToken(TokenInterface $token);

    /**
     * @param \Box\Model\Connection\Token\TokenInterface $token
     * @param mixed $tokenUpdateClause update context such as a where clause
     *
     * @return bool return value is success of storage
     */
    public function updateToken(TokenInterface $token, $tokenUpdateClause = null);

    /**
     * @param mixed $retrievalWhereClause retrieval context such as a where clause
     *
     * @return TokenInterface
     */
    public function retrieveToken($retrievalWhereClause = null);

    /**
     * @return TokenInterface
     */
    public function getPreviousToken();

    /**
     * store previous token for usage
     *
     * @param TokenInterface|null $previousToken
     *
     * @return BaseTokenStorageInterface
     */
    public function setPreviousToken(TokenInterface $previousToken = null);

    /**
     * remove token from storage
     *
     * @param \Box\Model\Connection\Token\TokenInterface $token
     * @param null $tokenContext
     *
     */
    public function removeToken(TokenInterface $token, $tokenContext = null);
}