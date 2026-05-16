<?php

/**
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

namespace Box\Connection;

use Box\Http\Response\BoxResponseInterface;
use Box\Logger\LoggerAwareInterface;
use Box\Http\FileStream;

interface ConnectionInterface extends LoggerAwareInterface
{
    /**
     * Send a raw request with full control over method, URI, and options.
     * Use when per-request headers or a non-JSON body are required.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, …)
     * @param array $options Guzzle-compatible options: 'body', 'headers', 'multipart', etc.
     */
    public function request(string $method, string $uri, array $options = []): BoxResponseInterface;

    /**
     * GET
     */
    public function query(string $uri): BoxResponseInterface;

    /**
     * POST
     *
     * @param array|string $params array values are form-encoded; pass a JSON string for JSON bodies
     */
    public function post(string $uri, array|string $params = []): BoxResponseInterface;

    /**
     * @param array|string $params array values are form-encoded; pass a JSON string for JSON bodies
     */
    public function put(string $uri, array|string $params = []): BoxResponseInterface;

    /**
     * DELETE
     */
    public function delete(string $uri): BoxResponseInterface;

    /**
     * @param string $file file/path to file
     */
    public function getMimeType(string $file): mixed;

    /**
     * @param string|FileStream $file file/path to file or FileStream object
     *
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
