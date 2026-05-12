<?php

declare(strict_types=1);

namespace Box\Tests\Service;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxResponseException;
use Box\Exception\TransportException;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\Service;
use PHPUnit\Framework\TestCase;

class ServiceErrorTest extends TestCase
{
    private Service $service;
    private $connection;
    private $token;

    protected function setUp(): void
    {
        $this->service = new class extends Service {
        };
        $this->connection = $this->createMock(ConnectionInterface::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->service->setConnection($this->connection);
        $this->service->setToken($this->token);
    }

    public function testBoxResponseExceptionPropagation(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getStatusCode')->willReturn(404);
        $response->method('getContent')->willReturn(json_encode(['code' => 'not_found', 'message' => 'Resource not found']));

        $this->connection->method('query')->willReturn($response);

        $this->expectException(BoxResponseException::class);
        $this->expectExceptionCode(404);

        try {
            $this->service->getFromBox('some/uri');
        } catch (BoxResponseException $e) {
            $this->assertSame($response, $e->getResponse());
            $this->assertSame('not_found', $e->getBoxCode());
            $this->assertStringContainsString('Resource not found', $e->getErrorDescription());
            throw $e;
        }
    }

    public function testRetryAfterContextInException(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getStatusCode')->willReturn(429);
        $response->method('getRetryAfter')->willReturn(60);
        $response->method('getHeaderLine')->willReturnMap([
            ['Retry-After', '60'],
            ['WWW-Authenticate', ''],
        ]);

        $this->connection->method('query')->willReturn($response);

        try {
            $this->service->getFromBox('some/uri');
        } catch (BoxResponseException $e) {
            $this->assertSame(60, $e->getContext('retry_after_seconds'));
            $this->assertSame('60', $e->getContext('retry_after_header'));
            return;
        }

        $this->fail('BoxResponseException was not thrown');
    }

    public function testTransportExceptionPropagation(): void
    {
        $this->connection->method('query')->willThrowException(new TransportException('Network error'));

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Network error');

        $this->service->getFromBox('some/uri');
    }

    public function test401ThrowsExceptionWithoutRetry(): void
    {
        $response401 = $this->createMock(BoxResponseInterface::class);
        $response401->method('isSuccessful')->willReturn(false);
        $response401->method('getStatusCode')->willReturn(401);
        $response401->method('getContent')->willReturn(json_encode(['code' => 'unauthorized']));

        $this->connection->expects($this->once())
            ->method('query')
            ->willReturn($response401);

        $this->expectException(BoxResponseException::class);
        $this->expectExceptionCode(401);

        $this->service->getFromBox('some/uri', 'decoded');
    }
}
