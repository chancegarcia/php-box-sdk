<?php

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
