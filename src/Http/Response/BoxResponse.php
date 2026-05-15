<?php

/**
 * @package     Box
 * @subpackage  Box_Http_Response
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

use Box\Exception\BoxException;
use Box\Http\Response\Header\ResponseHeader;
use Box\Http\Response\Header\ResponseHeaderInterface;
use Box\Http\Response\Header\StatusLineInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;

/**
 */
class BoxResponse implements BoxResponseInterface
{
    protected ResponseHeaderInterface $responseHeader;
    protected PsrResponseInterface $psrResponse;

    /**
     * @throws BoxException
     */
    public function __construct(mixed $content = '', string $header = '', ?PsrResponseInterface $psrResponse = null)
    {
        if (null !== $psrResponse) {
            $this->psrResponse = $psrResponse;
        } else {
            $this->responseHeader = new ResponseHeader($header);
            $statusLine = $this->responseHeader->getStatusLine();
            $status = ($statusLine instanceof StatusLineInterface) ? $statusLine->getStatusCode() : 200;
            $headers = $this->responseHeader->getHeaderLines();
            $version = ($statusLine instanceof StatusLineInterface) ? $statusLine->getHttpVersionNumber() : '1.1';

            $this->psrResponse = new GuzzleResponse($status, $headers, $content, $version);
        }

        // Re-sync responseHeader from psrResponse if it was passed in or just created
        if (!isset($this->responseHeader)) {
            $this->syncResponseHeader();
        }
    }

    private function syncResponseHeader(): void
    {
        $statusLine = sprintf(
            "HTTP/%s %s %s",
            $this->psrResponse->getProtocolVersion(),
            $this->psrResponse->getStatusCode(),
            $this->psrResponse->getReasonPhrase()
        );

        $headerString = $statusLine . "\r\n";
        foreach ($this->psrResponse->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headerString .= "$name: $value\r\n";
            }
        }
        $headerString .= "\r\n";
        $this->responseHeader = new ResponseHeader($headerString);
    }

    public function getPsrResponse(): ?PsrResponseInterface
    {
        return $this->psrResponse;
    }

    public function getResponseHeader(): ResponseHeaderInterface
    {
        return $this->responseHeader;
    }

    public function getContent(): string
    {
        return (string) $this->psrResponse->getBody();
    }

    public function json(bool $assoc = true): mixed
    {
        $content = $this->getContent();
        if ('' === $content) {
            return $assoc ? [] : new stdClass();
        }

        $decoded = json_decode($content, $assoc);

        if (null === $decoded && JSON_ERROR_NONE !== json_last_error()) {
            return $assoc ? [] : new stdClass();
        }

        return $decoded;
    }

    public function getRetryAfter(): ?int
    {
        if (!$this->hasHeader('Retry-After')) {
            return null;
        }

        $value = $this->getHeaderLine('Retry-After');

        if (preg_match('/^\d+$/', $value)) {
            return (int) $value;
        }

        $timestamp = strtotime($value);
        if (false === $timestamp) {
            return null;
        }

        $now = time();
        $diff = $timestamp - $now;

        return max(0, $diff);
    }

    public function getProtocolVersion(): string
    {
        return $this->psrResponse->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): static
    {
        $new = clone $this;
        $new->psrResponse = $this->psrResponse->withProtocolVersion($version);
        $new->syncResponseHeader();
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->psrResponse->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->psrResponse->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->psrResponse->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->psrResponse->getHeaderLine($name);
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return static
     */
    public function withHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->psrResponse = $this->psrResponse->withHeader($name, $value);
        $new->syncResponseHeader();
        return $new;
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return static
     */
    public function withAddedHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->psrResponse = $this->psrResponse->withAddedHeader($name, $value);
        $new->syncResponseHeader();
        return $new;
    }

    public function withoutHeader(string $name): static
    {
        $new = clone $this;
        $new->psrResponse = $this->psrResponse->withoutHeader($name);
        $new->syncResponseHeader();
        return $new;
    }

    public function getBody(): StreamInterface
    {
        return $this->psrResponse->getBody();
    }

    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->psrResponse = $this->psrResponse->withBody($body);
        return $new;
    }

    public function getStatusCode(): int
    {
        return $this->psrResponse->getStatusCode();
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $new = clone $this;
        $new->psrResponse = $this->psrResponse->withStatus($code, $reasonPhrase);
        $new->syncResponseHeader();
        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->psrResponse->getReasonPhrase();
    }

    // Compatibility methods for Symfony-like behavior

    public function isInvalid(): bool
    {
        return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
    }

    public function isInformational(): bool
    {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }

    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    public function isRedirection(): bool
    {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }

    public function isClientError(): bool
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    public function isServerError(): bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    public function isOk(): bool
    {
        return 200 === $this->getStatusCode();
    }

    public function isForbidden(): bool
    {
        return 403 === $this->getStatusCode();
    }

    public function isNotFound(): bool
    {
        return 404 === $this->getStatusCode();
    }

    public function isEmpty(): bool
    {
        return in_array($this->getStatusCode(), [204, 304]);
    }

    public function setProtocolVersion(string $version): static
    {
        $this->psrResponse = $this->psrResponse->withProtocolVersion($version);
        $this->syncResponseHeader();
        return $this;
    }
}
