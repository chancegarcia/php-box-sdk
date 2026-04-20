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
    public function getHttpVersion();

    /**
     * @param string $httpVersion
     * @return StatusLineInterface
     */
    public function setHttpVersion($httpVersion = null);

    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @param int $statusCode
     * @return StatusLineInterface
     */
    public function setStatusCode($statusCode = null);

    /**
     * @return string
     */
    public function getReasonPhrase();

    /**
     * @param int $reasonPhrase
     * @return StatusLineInterface
     */
    public function setReasonPhrase($reasonPhrase = null);

    /**
     * @return string
     */
    public function getHttpVersionPrefix();

    /**
     * @param string $httpVersionPrefix
     * @return StatusLineInterface
     */
    public function setHttpVersionPrefix($httpVersionPrefix = null);

    /**
     * @return string
     */
    public function getHttpVersionNumber();

    /**
     * @param string $httpVersionNumber
     * @return StatusLineInterface
     */
    public function setHttpVersionNumber($httpVersionNumber = null);
}