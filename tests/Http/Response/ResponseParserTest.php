<?php

namespace Box\Tests\Http\Response;

use Box\Http\Response\ResponseParser;
use PHPUnit\Framework\TestCase;

class ResponseParserTest extends TestCase
{
    public function testParseHeaderWithRawHeaders(): void
    {
        $expected = [
            0 => 'HTTP/1.1 401 Unauthorized',
            'Server' => 'ATS',
            'Date' => 'Mon, 25 Jan 2016 06:20:58 GMT',
            'Content-Length' => '0',
            'WWW-Authenticate' => 'Bearer realm="Service", error="invalid_token", error_description="The access token provided is invalid."',
            'Vary' => 'Accept-Encoding',
            'Age' => '0',
            'Connection' => 'keep-alive',
        ];

        $rawHeadersNormalized = implode("\n", [
            'HTTP/1.1 401 Unauthorized',
            'Server: ATS',
            'Date: Mon, 25 Jan 2016 06:20:58 GMT',
            'Content-Length: 0',
            'WWW-Authenticate: Bearer realm="Service", error="invalid_token", error_description="The access token provided is invalid."',
            'Vary: Accept-Encoding',
            'Age: 0',
            'Connection: keep-alive'
        ]);

        $result = ResponseParser::parseHeader($rawHeadersNormalized);

        $this->assertEquals($expected, $result);
    }

    public function testParseWwwAuthenticateHeader(): void
    {
        $headerValue = 'Bearer realm="Service", error="invalid_token", error_description="The access token provided is invalid."';

        $expected = [
            'scheme' => 'Bearer',
            'realm' => 'Service',
            'error' => 'invalid_token',
            'error_description' => 'The access token provided is invalid.',
        ];

        $result = ResponseParser::parseWwwAuthenticateHeader($headerValue);

        $this->assertEquals($expected, $result);
    }
}
