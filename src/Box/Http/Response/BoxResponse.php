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
use Symfony\Component\HttpFoundation\Response;

class BoxResponse extends Response implements BoxResponseInterface
{
    /**
     * @var ResponseHeaderInterface
     */
    protected ResponseHeaderInterface $responseHeader;

    /**
     * @throws BoxException
     */
    public function __construct(mixed $content = '', string $header = '') {
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

    /**
     * {@inheritdoc}
     */
    public function getResponseHeader(): ResponseHeaderInterface {
        return $this->responseHeader;
    }

    /**
     * {@inheritdoc}
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setResponseHeader(?ResponseHeaderInterface $responseHeader = null): BoxResponseInterface {
        $this->responseHeader = $responseHeader;

        return $this;
    }


    public function hasHeader(string $name): bool {
        $headerLines = $this->getResponseHeader()->getHeaderLines();

        $normalizedHeaderLineKeys = array_map('strtolower', array_keys($headerLines));

        return in_array(strtolower($name), $normalizedHeaderLineKeys);
    }

    public function getHeader(string $name): array {
        $headerLine = $this->getHeaderLine($name);
        if ("" === $headerLine) {
            $header = array();
        } else {
            if (!str_contains($headerLine, ",")) {
                $header = array($headerLine);
            } else {
                $header = explode(",", $headerLine);
            }
        }

        return $header;
    }

    public function getHeaderLine(string $name): string {
        if (!$this->hasHeader($name)) {
            return "";
        }

        $normalizedHeaderLines = array_change_key_case($this->getResponseHeader()->getHeaderLines());

        return $normalizedHeaderLines[strtolower($name)];
    }

    /**
     * @param string $version
     * @return $this
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setProtocolVersion(string $version): static
    {
        parent::setProtocolVersion($version);

        return $this;
    }
}