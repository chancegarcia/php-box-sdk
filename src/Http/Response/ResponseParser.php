<?php
/**
 * @package
 * @subpackage
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
 *
 */

namespace Box\Http\Response;

use Box\Http\Response\Header\StatusLineInterface;
use Box\Exception\BoxException;

class ResponseParser
{
    /**
     * @param string $sStatusLine
     * @param bool $associative  if true, then returns with keys: httpVersion, statusCode, reasonPhrase
     * @return array if non-associative, return in order: httpVersion, statusCode, reasonPhrase
     * @throws BoxException
     */
    public static function parseHeaderStatusLine($sStatusLine = '', $associative = true) {

        if (!is_string($sStatusLine)) {
            throw new \InvalidArgumentException("string value expected for parsing. given: ".gettype($sStatusLine));
        }

        list($httpVersion, $statusCode, $reasonPhrase) = explode(" ", $sStatusLine);
        $code = filter_var($statusCode, FILTER_VALIDATE_INT);

        if (true === $associative) {
            $statusLine = array(
                'httpVersion' => $httpVersion,
                'statusCode' => $code,
                'reasonPhrase' => $reasonPhrase,
            );
        } else {
            $statusLine = array(
                $httpVersion,
                $code,
                $reasonPhrase,
            );
        }

        return $statusLine;
    }

    /**
     * @param string $sHeaders
     * @param bool|true $replace
     * @return array
     */
    public static function parseHeader($sHeaders = '', $replace = true) {
        if (!is_string($sHeaders)) {
            throw new \InvalidArgumentException("string value expected for parsing. given: ".gettype($sHeaders));
        }

        $finalHeaders = array();
        $aHeaders = explode(PHP_EOL, $sHeaders);;
        foreach ($aHeaders as $headerLineKey => $headerLineValue) {
            // based on protocols found on https://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
            // first line is Status Line
            if (0 === $headerLineKey) {
                $finalHeaders[] = $headerLineValue;
            } else {
                // rest of the lines are headers
                $aLine = explode(":", $headerLineValue);
                if (2 === count($aLine)) {
                    list($key, $value) = array_map("trim", $aLine);
                    if (true === $replace || !array_key_exists($key, $finalHeaders)) {
                        $finalHeaders[$key] = $value;
                    } else {
                        $finalHeaders[$key] = array_merge((array)$finalHeaders[$key], (array)$value);
                    }
                }
            }
        }

        return $finalHeaders;
    }

    public static function parseWwwAuthenticateHeader($wwwAuthenticateHeaderValue = null) {
        if (!is_string($wwwAuthenticateHeaderValue)) {
            // maybe make more verbose in the future by switching on gettype()==object to get class?
            throw new \InvalidArgumentException("string value expected for parsing. given: ".gettype($wwwAuthenticateHeaderValue));
        }

        if (empty($wwwAuthenticateHeaderValue)) {
            return array();
        }
        $valuePairs = array_map("trim", explode(",", $wwwAuthenticateHeaderValue));
        $keys = array();
        $values = array();

        foreach ($valuePairs as $valuePair) {
            $tempPair = explode("=", $valuePair);
            $tempkey = array_shift($tempPair);
            $aKey = explode(" ", $tempkey);
            $tempValue = (count($tempPair) > 1) ? $tempPair : array_shift($tempPair);
            if (count($aKey) > 1) {
                $key = array_shift($aKey);
                $value = array(array_shift($aKey) => $tempValue);
            } else {
                $key = $tempkey;
                $value = $tempValue;
            }

            $keys[] = $key;
            $values[] = $value;
        }

        return array_combine($keys, $values);
    }
}