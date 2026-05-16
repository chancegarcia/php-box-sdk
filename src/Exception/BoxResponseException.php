<?php

/**
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013-2016 Chance Garcia, chancegarcia.com
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2016 Chance Garcia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Box\Exception;

use Box\Http\Response\BoxResponseInterface;
use Box\Http\Response\ResponseParser;
use Exception;

class BoxResponseException extends BoxException
{
    /**
     * create constants based on the possible returns for oauth
     * https://developers.box.com/oauth/
     */

    protected ?BoxResponseInterface $response = null;

    /**
     * @param string $message
     * @param int|string $code
     * @param Exception|null $previous
     * @param BoxResponseInterface|null $response
     */
    public function __construct(string $message = "", int|string $code = 0, ?Exception $previous = null, ?BoxResponseInterface $response = null)
    {
        parent::__construct($message, $code, $previous);

        if ($response instanceof BoxResponseInterface) {
            $this->response = $response;

            $wwwAuthenticationHeaderLine = $response->getHeaderLine('WWW-Authenticate');
            $parsedLine = ResponseParser::parseWwwAuthenticateHeader($wwwAuthenticationHeaderLine);

            if (array_key_exists('error', $parsedLine)) {
                $this->error = $this->boxCode = $this->sanitize($parsedLine['error']);
            }

            if (array_key_exists('error_description', $parsedLine)) {
                $this->errorDescription = $this->sanitize($parsedLine['error_description']);
            }

            // attempt to parse response body for error details
            $content = $response->getContent();
            if (!empty($content)) {
                try {
                    $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    $decoded = null;
                }
                if (is_array($decoded)) {
                    if (isset($decoded['code'])) {
                        $this->boxCode = $this->sanitize($decoded['code']);
                    }
                    if (isset($decoded['message'])) {
                        $sanitizedMessage = $this->sanitize($decoded['message']);
                        $this->errorDescription = $this->errorDescription ? $this->errorDescription . " | " . $sanitizedMessage : $sanitizedMessage;
                    }
                    if (isset($decoded['context_info'])) {
                        $this->addContext($decoded['context_info']);
                    }
                }
            }
        }
    }

    /**
     * @return null|BoxResponseInterface
     */
    public function getResponse(): ?BoxResponseInterface
    {
        return $this->response;
    }

    /**
     * @param BoxResponseInterface|null $response
     *
     * @return void
     */
    public function setResponse(?BoxResponseInterface $response = null): void
    {
        $this->response = $response;
    }
}
