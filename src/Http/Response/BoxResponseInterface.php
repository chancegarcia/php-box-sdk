<?php

/**
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2016 Chance Garcia, chancegarcia.com
 *
 *    This program is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 2 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 */

namespace Box\Http\Response;

use Box\Http\Response\Header\ResponseHeaderInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface BoxResponseInterface extends PsrResponseInterface
{
    /**
     * @return ResponseHeaderInterface
     */
    public function getResponseHeader(): ResponseHeaderInterface;

    /**
     * @return PsrResponseInterface|null
     */
    public function getPsrResponse(): ?PsrResponseInterface;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @param bool $assoc
     *
     * @throws \JsonException
     * @return mixed
     */
    public function json(bool $assoc = true): mixed;

    /**
     * @return int|null
     */
    public function getRetryAfter(): ?int;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader(string $name): bool;

    /**
     * @param string $name
     *
     * @return array
     */
    public function getHeader(string $name): array;

    /**
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine(string $name): string;

    // making interface entries for httpfoundation Response class that we extend and use

    /**
     * Retrieves the status code for the current web response.
     *
     * @return int Status code
     */
    public function getStatusCode(): int;

    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     *
     * @param string $version The HTTP protocol version
     *
     * @return static
     */
    public function setProtocolVersion(string $version): static;

    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
    /**
     * Is response invalid?
     *
     * @return bool
     */
    public function isInvalid(): bool;

    /**
     * Is response informative?
     *
     * @return bool
     */
    public function isInformational(): bool;

    /**
     * Is response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * Is the response a redirect?
     *
     * @return bool
     */
    public function isRedirection(): bool;

    /**
     * Is there a client error?
     *
     * @return bool
     */
    public function isClientError(): bool;

    /**
     * Was there a server side error?
     *
     * @return bool
     */
    public function isServerError(): bool;

    /**
     * Is the response OK?
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * Is the response forbidden?
     *
     * @return bool
     */
    public function isForbidden(): bool;

    /**
     * Is the response a not found error?
     *
     * @return bool
     */
    public function isNotFound(): bool;

    /**
     * Is the response empty?
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
