<?php

namespace Box\Tests\Http\Transport;

use Box\Http\Transport\GuzzleTransport;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;
use Box\Http\Response\BoxResponseInterface;

class GuzzleTransportTest extends TestCase
{
    public function testRequestWrapsResponse(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(201, ['X-Test' => 'Bar'], '{"id":"123"}', '1.1', 'Created')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);

        $transport = new GuzzleTransport($client);
        $response = $transport->request('POST', 'http://example.com/api', [
            'headers' => ['X-Req' => 'Foo'],
            'body' => '{"name":"test"}'
        ]);

        $this->assertInstanceOf(BoxResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('{"id":"123"}', $response->getContent());
        $this->assertEquals('Bar', $response->getHeaderLine('X-Test'));
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertEquals('Created', $response->getReasonPhrase());
    }

    public function testOptionsArePassedToGuzzle(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(200)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = $this->getMockBuilder(GuzzleClient::class)
            ->setConstructorArgs([['handler' => $handlerStack]])
            ->onlyMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with('GET', 'http://example.com', $this->callback(function ($options) {
                return isset($options['query']) && $options['query']['foo'] === 'bar'
                    && isset($options['verify']) && $options['verify'] === false;
            }))
            ->willReturn(new GuzzleResponse(200));

        $transport = new GuzzleTransport($client);
        $transport->request('GET', 'http://example.com', [
            'query' => ['foo' => 'bar'],
            'verify' => false
        ]);
    }
}
