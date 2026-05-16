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

interface StatusLineInterface
{
    public function getHttpVersion(): string;

    public function setHttpVersion(?string $httpVersion = null): void;

    public function getStatusCode(): int;

    public function setStatusCode(?int $statusCode = null): void;

    public function getReasonPhrase(): string;

    public function setReasonPhrase(?string $reasonPhrase = null): void;

    public function getHttpVersionPrefix(): string;

    public function setHttpVersionPrefix(?string $httpVersionPrefix = null): void;

    public function getHttpVersionNumber(): string;

    public function setHttpVersionNumber(?string $httpVersionNumber = null): void;
}
