<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 2:58 PM
 *
 * @package     Box
 * @subpackage  Box_Model
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

namespace Box\Service;

use Box\Http\Response\BoxResponseInterface;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use BadMethodCallException;

interface ServiceInterface
{
    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * @param ConnectionInterface|null $connection
     *
     * @return void
     */
    public function setConnection($connection = null);

    /**
     * @return TokenInterface
     */
    public function getToken();

    /**
     * @param TokenInterface|null $token
     *
     * @return void
     */
    public function setToken($token = null);

    /**
     * @param BoxResponseInterface|null $response
     * @param string $returnType 'decoded', 'flat', 'array', or 'original'
     *
     * @throws \Box\Exception\BoxException
     * @throws BadMethodCallException
     * @return mixed
     */
    public function handleBoxResponse(?BoxResponseInterface $response = null, $returnType = 'decoded');
}
