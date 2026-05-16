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

namespace Box\Http\Response\Header;

use Box\Http\Response\ResponseParser;

class StatusLine implements StatusLineInterface
{
    protected $httpVersion = "HTTP/1.1";
    protected $httpVersionPrefix = "HTTP/";
    protected $httpVersionNumber = "1.1";
    protected $statusCode = 200;
    protected $reasonPhrase = "OK";

    public function __construct($sStatusLine = '')
    {
        if (!is_string($sStatusLine)) {
            throw new \InvalidArgumentException("string value expected for parsing. given: " . gettype($sStatusLine));
        }

        if (!empty($sStatusLine)) {
            list($httpVersion, $statusCode, $reasonPhrase) = ResponseParser::parseHeaderStatusLine($sStatusLine, false);

            list($httpVersionPrefix, $httpVersionNumber) = explode("/", $httpVersion);
            $code = filter_var($statusCode, FILTER_VALIDATE_INT);

            $this->httpVersion = $httpVersion;
            $this->httpVersionPrefix = $httpVersionPrefix . "/";
            $this->httpVersionNumber = $httpVersionNumber;
            $this->statusCode = $code;
            $this->reasonPhrase = $reasonPhrase;
        }
    }

    public function getHttpVersionPrefix(): string
    {
        return $this->httpVersionPrefix;
    }

    public function setHttpVersionPrefix(?string $httpVersionPrefix = null): void
    {
        $this->httpVersionPrefix = $httpVersionPrefix;
    }

    public function getHttpVersionNumber(): string
    {
        return $this->httpVersionNumber;
    }

    public function setHttpVersionNumber(?string $httpVersionNumber = null): void
    {
        $this->httpVersionNumber = $httpVersionNumber;
    }

    public function getHttpVersion(): string
    {
        return $this->httpVersion;
    }

    public function setHttpVersion(?string $httpVersion = null): void
    {
        $this->httpVersion = $httpVersion;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode = null): void
    {
        $this->statusCode = $statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function setReasonPhrase(?string $reasonPhrase = null): void
    {
        $this->reasonPhrase = $reasonPhrase;
    }
}
