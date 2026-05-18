<?php

namespace Box\Tests\Connection;

use Box\Connection\Connection;
use Box\Http\Transport\GuzzleTransport;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Request;

class ConnectionErrorTest extends TestCase
{
    public function testConnectionReturnsRawResponseFor4xx()
    {
        $mock = new MockHandler([
            new GuzzleResponse(404, [], '{"code":"not_found","message":"Not Found"}')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);

        $connection = new Connection();
        $connection->setTransport(new GuzzleTransport($client));

        $response = $connection->query('http://example.com/notfound');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"code":"not_found","message":"Not Found"}', $response->getContent());
    }

    public function testConnectionReturnsRawResponseFor5xx()
    {
        $mock = new MockHandler([
            new GuzzleResponse(500, [], 'Internal Server Error')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);

        $connection = new Connection();
        $connection->setTransport(new GuzzleTransport($client));

        $response = $connection->query('http://example.com/error');

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testConnectionThrowsApiExceptionWhenRequested()
    {
        $mock = new MockHandler([
            new GuzzleResponse(404, [], '{"code":"not_found","message":"Not Found"}')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);

        $connection = new Connection();
        $connection->setTransport(new GuzzleTransport($client));

        $this->expectException(\Box\Exception\NotFoundException::class);
        $this->expectExceptionCode(404);

        $connection->request('GET', 'http://example.com/notfound', ['throw_on_error' => true]);
    }

    public function testExceptionRedactsSensitiveInformation()
    {
        $token = 'secret_access_token_123';
        $message = "Failed to use token: Bearer " . $token;

        $exception = new \Box\Exception\BoxException($message);

        $this->assertStringNotContainsString($token, $exception->getMessage());
        $this->assertStringContainsString('[REDACTED]', $exception->getMessage());
    }

    public function testExceptionRedactsContextInformation()
    {
        $exception = new \Box\Exception\BoxException("Test error");
        $exception->addContext([
            'access_token' => 'super_secret',
            'safe_key' => 'safe_value'
        ], 'test_context');

        $context = $exception->getContext('test_context');
        $this->assertEquals('[REDACTED]', $context['access_token']);
        $this->assertEquals('safe_value', $context['safe_key']);
    }

    public function testTransportExceptionWrapsGuzzleException()
    {
        $client = $this->createMock(GuzzleClient::class);
        $client->method('request')->willThrowException(new \GuzzleHttp\Exception\ConnectException(
            "Connection failed",
            new Request('GET', 'http://example.com')
        ));

        $connection = new Connection();
        $connection->setTransport(new GuzzleTransport($client));

        $this->expectException(\Box\Exception\TransportException::class);
        $this->expectExceptionMessage("Connection failed");

        $connection->query('http://example.com');
    }
}
