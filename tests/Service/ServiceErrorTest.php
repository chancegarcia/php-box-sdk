<?php

declare(strict_types=1);

namespace Box\Tests\Service;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxException;
use Box\Exception\BoxResponseException;
use Box\Exception\TransportException;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\Service;
use Box\Storage\Token\TokenStorageInterface;
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

        $this->service->setAuthorizedConnection($this->connection);
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

    public function test401RefreshRetryFlow(): void
    {
        $response401 = $this->createMock(BoxResponseInterface::class);
        $response401->method('isSuccessful')->willReturn(false);
        $response401->method('getStatusCode')->willReturn(401);

        $response200 = $this->createMock(BoxResponseInterface::class);
        $response200->method('isSuccessful')->willReturn(true);
        $response200->method('getContent')->willReturn(json_encode(['id' => '123']));
        $response200->method('json')->willReturn(['id' => '123']);

        $this->connection->expects($this->exactly(2))
            ->method('query')
            ->willReturnOnConsecutiveCalls($response401, $response200);

        $newToken = $this->createMock(TokenInterface::class);

        // Mock refreshToken by creating a partial mock of Service
        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['refreshToken'])
            ->getMock();
        $service->setAuthorizedConnection($this->connection);
        $service->setToken($this->token);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $service->setTokenStorage($tokenStorage);

        $service->expects($this->once())
            ->method('refreshToken')
            ->willReturn($newToken);

        $tokenStorage->expects($this->once())
            ->method('updateToken')
            ->with($newToken, null);

        $result = $service->getFromBox('some/uri', 'decoded');
        $this->assertEquals(['id' => '123'], $result);
        $this->assertSame($newToken, $service->getToken());
    }

    public function test401RefreshRetryFlowForSendUpdateToBox(): void
    {
        $response401 = $this->createMock(BoxResponseInterface::class);
        $response401->method('isSuccessful')->willReturn(false);
        $response401->method('getStatusCode')->willReturn(401);
        $response401->method('getHeaderLine')->with('WWW-Authenticate')->willReturn('');

        $response200 = $this->createMock(BoxResponseInterface::class);
        $response200->method('isSuccessful')->willReturn(true);
        $response200->method('getContent')->willReturn(json_encode(['id' => '123']));
        $response200->method('json')->willReturn(['id' => '123']);

        $this->connection->expects($this->exactly(2))
            ->method('put')
            ->willReturnOnConsecutiveCalls($response401, $response200);

        $newToken = $this->createMock(TokenInterface::class);

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['refreshToken'])
            ->getMock();
        $service->setAuthorizedConnection($this->connection);
        $service->setToken($this->token);
        $service->setTokenStorage($this->createMock(TokenStorageInterface::class));

        $service->expects($this->once())
            ->method('refreshToken')
            ->willReturn($newToken);

        $result = $service->sendUpdateToBox('some/uri', ['name' => 'new name'], 'decoded');
        $this->assertEquals(['id' => '123'], $result);
        $this->assertSame($newToken, $service->getToken());
    }

    public function testRefreshFailurePreservesContext(): void
    {
        $response401 = $this->createMock(BoxResponseInterface::class);
        $response401->method('isSuccessful')->willReturn(false);
        $response401->method('getStatusCode')->willReturn(401);
        $response401->method('getContent')->willReturn(json_encode(['code' => 'unauthorized']));

        $this->connection->method('query')->willReturn($response401);

        $service = $this->getMockBuilder(Service::class)
            ->onlyMethods(['refreshToken'])
            ->getMock();
        $service->setAuthorizedConnection($this->connection);
        $service->setToken($this->token);
        $service->setTokenStorage($this->createMock(TokenStorageInterface::class));

        $refreshException = new BoxException('Refresh failed');
        $service->method('refreshToken')->willThrowException($refreshException);

        try {
            $service->getFromBox('some/uri');
        } catch (BoxException $e) {
            $this->assertStringContainsString('encountered exception during refresh token attempt: Refresh failed', $e->getMessage());
            $this->assertContains($refreshException, $e->getContext());
            // It should also contain the original BoxResponseException
            $foundBRE = false;
            foreach ($e->getContext() as $ctx) {
                if ($ctx instanceof BoxResponseException && $ctx->getCode() === 401) {
                    $foundBRE = true;
                    break;
                }
            }
            $this->assertTrue($foundBRE, 'Original BoxResponseException not found in context');
            return;
        }

        $this->fail('BoxException was not thrown');
    }
}
