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

namespace Box\Connection;

use Box\Http\Response\BoxResponseInterface;
use Box\Logger\LoggerAwareInterface;
use Box\Http\FileStream;

interface ConnectionInterface extends LoggerAwareInterface
{
    /**
     * GET
     * @param string $uri
     * @return BoxResponseInterface
     */
    public function query(string $uri): BoxResponseInterface;

    /**
     * POST
     *
     * @param string $uri
     * @param array|string $params will convert array to string; array will be deprecated in the future; json
     *                                  encoded string will become the only valid value
     * @param bool $nameValuePair this will be deprecated/fully removed in the future since params as a json
     *                                  encoded string will be the expected value
     *
     * @return BoxResponseInterface
     */
    public function post(string $uri, array|string $params = [], bool $nameValuePair = false): BoxResponseInterface;

    /**
     * @param string $uri
     * @param array|string $params array will be deprecated in the future;
     *                             json encoded string will become the only valid value
     *
     * @return BoxResponseInterface
     */
    public function put(string $uri, array|string $params = []): BoxResponseInterface;

    /**
     * DELETE
     *
     * @param string $uri
     * @return BoxResponseInterface
     */
    public function delete(string $uri): BoxResponseInterface;

    /**
     * @param string $file file/path to file
     * @return mixed
     */
    public function getMimeType(string $file): mixed;

    /**
     * @param string $uri
     * @param string|FileStream $file file/path to file or FileStream object
     * @param string|int $parentId
     * @return array|BoxResponseInterface
     */
    public function postFile(
        string $uri,
        string|FileStream $file,
        string|int $parentId = 0
    ): BoxResponseInterface;

    public function setAccessToken(?string $accessToken = null): void;

    public function getAccessToken(): ?string;

    public function setHeaders(array $headers = []): void;

    public function getHeaders(): array;

    public function addHeader(string $name, string $value): void;

    public function setTransportName(string $transportName): void;

    public function getTransportName(): string;
}
