<?php

namespace Box\Tests\Model\Connection;

use Box\Http\Response\BoxResponseInterface;
use Box\Connection\Connection;
use Box\Http\Transport\TransportInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;

class TransportConnectionTest extends TestCase
{
    public function testConnectionWithGuzzleTransport()
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, ['X-Test' => 'Foo'], '{"status":"ok"}')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);

        $connection = new Connection([
            'transport' => Connection::TRANSPORT_GUZZLE,
            'accessToken' => 'fake_token'
        ]);
        
        // Inject mock guzzle client via transport if we had a setter, 
        // but for now let's test if it uses the transport.
        // Better: create a GuzzleTransport with the mock client and set it.
        $transport = new \Box\Http\Transport\GuzzleTransport($client);
        $connection->setTransport($transport);

        $response = $connection->query('http://example.com/me');

        $this->assertInstanceOf(BoxResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"status":"ok"}', $response->getContent());
        $this->assertEquals('Foo', $response->getHeaderLine('X-Test'));
        $this->assertTrue($response->hasHeader('X-Test'));
    }

    public function testConnectionWithMockTransport()
    {
        $mockTransport = $this->createMock(TransportInterface::class);
        $mockResponse = $this->createMock(BoxResponseInterface::class);
        
        $mockTransport->expects($this->once())
            ->method('request')
            ->with('GET', 'http://example.com/test', $this->callback(function($options) {
                return $options['headers']['Authorization'] === 'Bearer test_token';
            }))
            ->willReturn($mockResponse);

        $connection = new Connection();
        $connection->setTransport($mockTransport);
        $connection->setAccessToken('test_token');

        $response = $connection->query('http://example.com/test');
        $this->assertSame($mockResponse, $response);
    }

    public function testBoxResponsePsr7Compatibility()
    {
        $content = '{"foo":"bar"}';
        $header = "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n";
        $response = new \Box\Http\Response\BoxResponse($content, $header);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals(['application/json'], $response->getHeader('Content-Type'));
        $this->assertEquals($content, (string)$response->getBody());
        $this->assertEquals('1.1', $response->getProtocolVersion());
    }
}
