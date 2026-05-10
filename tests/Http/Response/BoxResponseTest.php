<?php

namespace Box\Tests\Http\Response;

use Box\Http\Response\BoxResponse;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Response\Header\ResponseHeaderInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;

class BoxResponseTest extends TestCase
{
    public function testConstructFromLegacyInputs(): void
    {
        $content = '{"foo":"bar"}';
        $header = "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n";
        $response = new BoxResponse($content, $header);

        $this->assertInstanceOf(BoxResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertInstanceOf(ResponseHeaderInterface::class, $response->getResponseHeader());
    }

    public function testConstructFromPsr7Response(): void
    {
        $psrResponse = new GuzzleResponse(201, ['X-Box-Test' => 'value'], 'created');
        $response = new BoxResponse('', '', $psrResponse);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('created', $response->getContent());
        $this->assertEquals('value', $response->getHeaderLine('X-Box-Test'));
        $this->assertSame($psrResponse, $response->getPsrResponse());

        // Ensure responseHeader is synced
        $this->assertEquals(201, $response->getResponseHeader()->getStatusLine()->getStatusCode());
    }

    public function testJsonDecoding(): void
    {
        $content = '{"foo":"bar"}';
        $response = new BoxResponse($content);

        $this->assertEquals(['foo' => 'bar'], $response->json());
        $this->assertEquals((object)['foo' => 'bar'], $response->json(false));
    }

    public function testJsonDecodingEmptyBody(): void
    {
        $response = new BoxResponse('');
        $this->assertEquals([], $response->json());
    }

    public function testJsonDecodingInvalidJson(): void
    {
        $response = new BoxResponse('invalid');
        $this->assertEquals([], $response->json());
    }

    public function testRetryAfterSeconds(): void
    {
        $response = new BoxResponse('', "HTTP/1.1 429 Too Many Requests\r\nRetry-After: 60\r\n\r\n");
        $this->assertEquals(60, $response->getRetryAfter());
    }

    public function testRetryAfterDate(): void
    {
        $futureTimestamp = time() + 120;
        $futureDate = gmdate('D, d M Y H:i:s \G\M\T', $futureTimestamp);
        $response = new BoxResponse('', "HTTP/1.1 429 Too Many Requests\r\nRetry-After: $futureDate\r\n\r\n");

        $retryAfter = $response->getRetryAfter();
        // Allow 1 second difference due to timing
        $this->assertGreaterThanOrEqual(119, $retryAfter);
        $this->assertLessThanOrEqual(121, $retryAfter);
    }

    public function testRetryAfterPastDate(): void
    {
        $pastDate = date('D, d M Y H:i:s \G\M\T', time() - 120);
        $response = new BoxResponse('', "HTTP/1.1 429 Too Many Requests\r\nRetry-After: $pastDate\r\n\r\n");
        $this->assertEquals(0, $response->getRetryAfter());
    }

    public function testRetryAfterMissing(): void
    {
        $response = new BoxResponse('');
        $this->assertNull($response->getRetryAfter());
    }

    public function testHeaderBehavior(): void
    {
        $response = new BoxResponse('', "HTTP/1.1 200 OK\r\nX-Multi: value1\r\nX-Multi: value2\r\n\r\n");

        $this->assertTrue($response->hasHeader('x-multi')); // Case-insensitive
        $this->assertEquals(['value1', 'value2'], $response->getHeader('X-Multi'));
        $this->assertEquals('value1, value2', $response->getHeaderLine('X-Multi'));

        $headers = $response->getHeaders();
        $this->assertArrayHasKey('X-Multi', $headers);
    }

    public function testImmutableMutations(): void
    {
        $response = new BoxResponse('old content');
        $newResponse = $response->withHeader('X-New', 'value');

        $this->assertNotSame($response, $newResponse);
        $this->assertFalse($response->hasHeader('X-New'));
        $this->assertTrue($newResponse->hasHeader('X-New'));

        $statusResponse = $response->withStatus(404, 'Not Found');
        $this->assertNotSame($response, $statusResponse);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(404, $statusResponse->getStatusCode());
        $this->assertEquals('Not Found', $statusResponse->getReasonPhrase());
    }

    public function testSymfonyCompatibilityMethods(): void
    {
        $response = new BoxResponse('', "HTTP/1.1 200 OK\r\n\r\n");
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isClientError());

        $response = new BoxResponse('', "HTTP/1.1 404 Not Found\r\n\r\n");
        $this->assertTrue($response->isClientError());
        $this->assertTrue($response->isNotFound());
        $this->assertFalse($response->isSuccessful());

        $response = new BoxResponse('', "HTTP/1.1 500 Internal Server Error\r\n\r\n");
        $this->assertTrue($response->isServerError());

        $response = new BoxResponse('', "HTTP/1.1 204 No Content\r\n\r\n");
        $this->assertTrue($response->isEmpty());
    }
}
