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
    public function getResponseHeader(): ResponseHeaderInterface;

    public function getPsrResponse(): ?PsrResponseInterface;

    public function getContent(): string;

    /**
     * @throws \JsonException
     */
    public function json(bool $assoc = true): mixed;

    public function getRetryAfter(): ?int;

    public function hasHeader(string $name): bool;

    public function getHeader(string $name): array;

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
     */
    public function isInvalid(): bool;

    /**
     * Is response informative?
     */
    public function isInformational(): bool;

    /**
     * Is response successful?
     */
    public function isSuccessful(): bool;

    /**
     * Is the response a redirect?
     */
    public function isRedirection(): bool;

    /**
     * Is there a client error?
     */
    public function isClientError(): bool;

    /**
     * Was there a server side error?
     */
    public function isServerError(): bool;

    /**
     * Is the response OK?
     */
    public function isOk(): bool;

    /**
     * Is the response forbidden?
     */
    public function isForbidden(): bool;

    /**
     * Is the response a not found error?
     */
    public function isNotFound(): bool;

    /**
     * Is the response empty?
     */
    public function isEmpty(): bool;
}
