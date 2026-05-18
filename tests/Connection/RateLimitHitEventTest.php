<?php

declare(strict_types=1);

namespace Box\Tests\Connection;

use Box\Connection\Connection;
use Box\Event\Http\RateLimitHit;
use Box\Exception\RateLimitException;
use Box\Http\Transport\GuzzleTransport;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class RateLimitHitEventTest extends TestCase
{
    private function makeConnection(int $status, array $headers = []): Connection
    {
        $mock = new MockHandler([new GuzzleResponse($status, $headers, '{"error":"rate_limit"}')]);
        $transport = new GuzzleTransport(new GuzzleClient(['handler' => HandlerStack::create($mock)]));
        $connection = new Connection();
        $connection->setTransport($transport);
        return $connection;
    }

    public function testRateLimitHitDispatchedOn429WithThrowOnError(): void
    {
        $connection = $this->makeConnection(429, ['Retry-After' => '60']);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RateLimitHit::class));

        $connection->setEventDispatcher($dispatcher);

        $this->expectException(RateLimitException::class);
        $connection->request('GET', 'https://api.box.com/2.0/test', ['throw_on_error' => true]);
    }

    public function testRateLimitHitCarriesRetryAfterValue(): void
    {
        $connection = $this->makeConnection(429, ['Retry-After' => '45']);

        $capturedEvent = null;
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnCallback(function (object $event) use (&$capturedEvent) {
            $capturedEvent = $event;
            return $event;
        });

        $connection->setEventDispatcher($dispatcher);

        try {
            $connection->request('GET', 'https://api.box.com/2.0/test', ['throw_on_error' => true]);
        } catch (RateLimitException) {
        }

        $this->assertInstanceOf(RateLimitHit::class, $capturedEvent);
        $this->assertSame(45, $capturedEvent->retryAfter);
    }

    public function testRateLimitHitNotDispatchedWithoutThrowOnError(): void
    {
        $connection = $this->makeConnection(429, ['Retry-After' => '60']);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->never())->method('dispatch');

        $connection->setEventDispatcher($dispatcher);
        $connection->request('GET', 'https://api.box.com/2.0/test');
    }

    public function testRateLimitHitNotDispatchedWithoutDispatcher(): void
    {
        $connection = $this->makeConnection(429);

        $this->expectException(RateLimitException::class);
        $connection->request('GET', 'https://api.box.com/2.0/test', ['throw_on_error' => true]);
    }
}
