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

interface ResponseHeaderInterface
{
    /**
     * @return StatusLineInterface|null
     */
    public function getStatusLine(): ?StatusLineInterface;

    /**
     * @param StatusLineInterface|null $statusLine
     *
     */
    public function setStatusLine(?StatusLineInterface $statusLine = null): ResponseHeaderInterface;

    /**
     * @return array
     */
    public function getHeaderLines(): array;

    /**
     * @param array|null $headerLines
     *
     */
    public function setHeaderLines(?array $headerLines = null): ResponseHeaderInterface;
}
