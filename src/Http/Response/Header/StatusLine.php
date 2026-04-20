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

use Box\Http\Response\ResponseParser;

class StatusLine implements StatusLineInterface
{
    protected $httpVersion = "HTTP/1.1";
    protected $httpVersionPrefix = "HTTP/";
    protected $httpVersionNumber = "1.1";
    protected $statusCode = 200;
    protected $reasonPhrase = "OK";

    public function __construct($sStatusLine = '') {
        if (!is_string($sStatusLine)) {
            throw new \InvalidArgumentException("string value expected for parsing. given: ".gettype($sStatusLine));
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

    /**
     * {@inheritdoc}
     */
    public function getHttpVersionPrefix() {
        return $this->httpVersionPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function setHttpVersionPrefix($httpVersionPrefix = null) {
        $this->httpVersionPrefix = $httpVersionPrefix;

    }

    /**
     * {@inheritdoc}
     */
    public function getHttpVersionNumber() {
        return $this->httpVersionNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setHttpVersionNumber($httpVersionNumber = null) {
        $this->httpVersionNumber = $httpVersionNumber;

    }

    /**
     * {@inheritdoc}
     */
    public function getHttpVersion() {
        return $this->httpVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setHttpVersion($httpVersion = null) {
        $this->httpVersion = $httpVersion;

    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusCode($statusCode = null) {
        $this->statusCode = $statusCode;

    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase() {
        return $this->reasonPhrase;
    }

    /**
     * @param int $reasonPhrase
     * @return StatusLineInterface
     */
    public function setReasonPhrase($reasonPhrase = null) {
        $this->reasonPhrase = $reasonPhrase;

    }

}