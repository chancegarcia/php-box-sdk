<?php

namespace Box\Http\Response;

class ResponseParser
{
    /**
     * @param bool $associative  if true, then returns with keys: httpVersion, statusCode, reasonPhrase
     *
     * @return array if non-associative, return in order: httpVersion, statusCode, reasonPhrase
     */
    public static function parseHeaderStatusLine(string $statusLine = '', bool $associative = true): array
    {
        if ('' === $statusLine) {
            return $associative ? [] : ['', 0, ''];
        }

        $parts = explode(" ", $statusLine, 3);
        $httpVersion = $parts[0] ?? '';
        $statusCode = $parts[1] ?? 0;
        $reasonPhrase = $parts[2] ?? '';

        $code = filter_var($statusCode, FILTER_VALIDATE_INT);

        if (true === $associative) {
            $result = [
                'httpVersion' => $httpVersion,
                'statusCode' => $code,
                'reasonPhrase' => $reasonPhrase,
            ];
        } else {
            $result = [
                $httpVersion,
                $code,
                $reasonPhrase,
            ];
        }

        return $result;
    }

    public static function parseHeader(string $headers = '', bool $replace = false): array
    {
        $finalHeaders = [];
        $headerLines = preg_split('/\r\n|\r|\n/', $headers);
        $headerLineKey = 0;
        foreach ($headerLines as $headerLineValue) {
            $headerLineValue = trim($headerLineValue);
            if ('' === $headerLineValue) {
                continue;
            }
            // based on protocols found on https://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
            // first line is Status Line
            if (0 === $headerLineKey) {
                if (!str_starts_with($headerLineValue, 'HTTP/')) {
                    // Not a status line, maybe just headers?
                    $headerLineKey++; // skip index 0
                } else {
                    $finalHeaders[] = $headerLineValue;
                    $headerLineKey++;
                    continue;
                }
            }

            // rest of the lines are headers
            $lineParts = explode(":", $headerLineValue, 2);
            if (2 === count($lineParts)) {
                list($key, $value) = array_map("trim", $lineParts);
                if (true === $replace || !array_key_exists($key, $finalHeaders)) {
                    $finalHeaders[$key] = $value;
                } else {
                    $currentValue = $finalHeaders[$key];
                    $finalHeaders[$key] = array_merge((array)$currentValue, (array)$value);
                }
            }
            $headerLineKey++;
        }

        if (0 === count($finalHeaders) || (isset($finalHeaders[0]) && count($finalHeaders) === 1 && str_starts_with($finalHeaders[0], 'HTTP/'))) {
             // ensure at least an empty status line if none found but we want a valid array structure for ResponseHeader
            if (!isset($finalHeaders[0])) {
                array_unshift($finalHeaders, '');
            }
        }

        return $finalHeaders;
    }

    public static function parseWwwAuthenticateHeader(?string $wwwAuthenticateHeaderValue = null): array
    {
        if (!is_string($wwwAuthenticateHeaderValue) || empty($wwwAuthenticateHeaderValue)) {
            return [];
        }

        $valuePairs = array_map("trim", explode(",", $wwwAuthenticateHeaderValue));
        $parsed = [];

        foreach ($valuePairs as $valuePair) {
            $tempPair = explode("=", $valuePair);
            $tempkey = trim(array_shift($tempPair));
            $tempValue = (count($tempPair) > 1) ? implode("=", $tempPair) : array_shift($tempPair);

            if (is_string($tempValue)) {
                $tempValue = trim($tempValue, '"');
            }

            $keyParts = explode(" ", $tempkey);
            if (count($keyParts) > 1) {
                // Handle scheme (e.g. "Bearer error")
                $scheme = array_shift($keyParts);
                $key = array_shift($keyParts);
                $parsed['scheme'] = $scheme;
                $parsed[$key] = $tempValue;
            } else {
                $parsed[$tempkey] = $tempValue;
            }
        }

        return $parsed;
    }
}
