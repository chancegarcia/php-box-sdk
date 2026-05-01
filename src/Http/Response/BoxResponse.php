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
 *
 */

namespace Box\Http\Response;

use Box\Exception\BoxException;
use Box\Http\Response\Header\ResponseHeader;
use Box\Http\Response\Header\ResponseHeaderInterface;
use Box\Http\Response\Header\StatusLineInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @todo v1: Deprecate BoxResponse and move toward PSR-7 ResponseInterface directly
 * @todo v1: Replace getContent() with getBody()
 * @todo v1: Replace getResponseHeader() with PSR-7 header methods
 * @todo v1: Remove inheritance from Symfony HttpFoundation Response if no longer needed
 */
class BoxResponse extends Response implements BoxResponseInterface
{
    /**
     * @var ResponseHeaderInterface
     */
    protected ResponseHeaderInterface $responseHeader;

    /**
     * @var PsrResponseInterface|null
     */
    protected ?PsrResponseInterface $psrResponse = null;

    /**
     * @throws BoxException
     */
    public function __construct(mixed $content = '', string $header = '', ?PsrResponseInterface $psrResponse = null)
    {
        $this->psrResponse = $psrResponse;
        $this->responseHeader = new ResponseHeader($header);

        $statusLine = $this->responseHeader->getStatusLine();
        // 200 is parent default
        $status = ($statusLine instanceof StatusLineInterface) ? $statusLine->getStatusCode() : 200;
        $headers = $this->responseHeader->getHeaderLines();
        $content = $content ?: '';

        parent::__construct($content, $status, $headers);

        if ($statusLine instanceof StatusLineInterface) {
            $this->setProtocolVersion($statusLine->getHttpVersionNumber());
        }
    }

    public function getPsrResponse(): ?PsrResponseInterface
    {
        return $this->psrResponse;
    }

    public function getProtocolVersion(): string
    {
        return parent::getProtocolVersion();
    }

    public function withProtocolVersion(string $version): static
    {
        $new = clone $this;
        $new->setProtocolVersion($version);
        return $new;
    }

    public function getHeaders(): array
    {
        $headers = $this->headers->all();
        // PSR-7 headers are array of strings
        foreach ($headers as $name => $values) {
            if (!is_array($values)) {
                $headers[$name] = [$values];
            }
        }
        return $headers;
    }

    public function getHeader(string $name): array
    {
        $values = $this->headers->all(strtolower($name));
        return is_array($values) ? $values : [$values];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headers->set($name, $value);
        return $new;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headers->set($name, $value, false);
        return $new;
    }

    public function withoutHeader(string $name): static
    {
        $new = clone $this;
        $new->headers->remove($name);
        return $new;
    }

    public function getBody(): StreamInterface
    {
        return Utils::streamFor($this->getContent());
    }

    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->setContent((string)$body);
        return $new;
    }

    public function getReasonPhrase(): string
    {
        return Response::$statusTexts[$this->getStatusCode()] ?? '';
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $new = clone $this;
        $new->setStatusCode($code);
        // Symfony Response handles reason phrase internally if we don't set it explicitly
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseHeader(): ResponseHeaderInterface
    {
        return $this->responseHeader;
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }
}
