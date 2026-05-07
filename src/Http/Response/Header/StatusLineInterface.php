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

namespace Box\Http\Response\Header;

interface StatusLineInterface
{
    /**
     * @return string
     */
    public function getHttpVersion(): string;

    /**
     * @param string|null $httpVersion
     * @return void
     */
    public function setHttpVersion(?string $httpVersion = null): void;

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param int|null $statusCode
     * @return void
     */
    public function setStatusCode(?int $statusCode = null): void;

    /**
     * @return string
     */
    public function getReasonPhrase(): string;

    /**
     * @param string|null $reasonPhrase
     * @return void
     */
    public function setReasonPhrase(?string $reasonPhrase = null): void;

    /**
     * @return string
     */
    public function getHttpVersionPrefix(): string;

    /**
     * @param string|null $httpVersionPrefix
     * @return void
     */
    public function setHttpVersionPrefix(?string $httpVersionPrefix = null): void;

    /**
     * @return string
     */
    public function getHttpVersionNumber(): string;

    /**
     * @param string|null $httpVersionNumber
     * @return void
     */
    public function setHttpVersionNumber(?string $httpVersionNumber = null): void;
}
