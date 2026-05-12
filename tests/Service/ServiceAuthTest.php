<?php

declare(strict_types=1);

namespace Box\Tests\Service;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\Token;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\Service;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ServiceAuthTest extends TestCase
{
    private Service $service;
    private $connection;
    private $logger;

    protected function setUp(): void
    {
        $this->service = new Service();
        $this->connection = $this->createMock(ConnectionInterface::class);
        $this->service->setConnection($this->connection);

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service->setLogger($this->logger);
    }

    public function testRefreshTokenSuccess(): void
    {
        $token = new Token();
        $token->setRefreshToken('old_refresh_token');
        $this->service->setToken($token);
        $this->service->setClientId('test_client_id');
        $this->service->setClientSecret('test_client_secret');

        $responseData = [
            'access_token' => 'new_access_token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'refresh_token' => 'new_refresh_token'
        ];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($responseData));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn($responseData);

        $this->connection->expects($this->once())
            ->method('post')
            ->with(Service::TOKEN_URI, $this->callback(function ($params) {
                return $params['refresh_token'] === 'old_refresh_token' &&
                       $params['client_id'] === 'test_client_id' &&
                       $params['client_secret'] === 'test_client_secret' &&
                       $params['grant_type'] === 'refresh_token';
            }))
            ->willReturn($response);

        $refreshedToken = $this->service->refreshToken();

        $this->assertSame($token, $refreshedToken);
        $this->assertEquals('new_access_token', $token->getAccessToken());
        $this->assertEquals('new_refresh_token', $token->getRefreshToken());
    }

    public function testRefreshTokenRedactsSecrets(): void
    {
        $token = new Token();
        $token->setRefreshToken('sensitive_refresh_token');
        $this->service->setToken($token);
        $this->service->setClientId('client_id');
        $this->service->setClientSecret('sensitive_secret');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'access_token' => 'sensitive_access_token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'refresh_token' => 'new_sensitive_refresh_token'
        ]));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn([
            'access_token' => 'sensitive_access_token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'refresh_token' => 'new_sensitive_refresh_token'
        ]);

        $this->connection->method('post')->willReturn($response);

        // We expect debug logs NOT to contain the sensitive info
        $this->logger->expects($this->atLeastOnce())
            ->method('debug')
            ->with($this->callback(function ($message) {
                // If it's the raw refresh return log, it should contain [REDACTED]
                if (str_contains($message, 'raw refresh return')) {
                    return str_contains($message, '[REDACTED]');
                }
                // For other logs, they should not contain sensitive data
                return !str_contains($message, 'sensitive_refresh_token') &&
                       !str_contains($message, 'sensitive_secret') &&
                       !str_contains($message, 'sensitive_access_token');
            }));

        $this->service->refreshToken();
    }

    public function testDestroyTokenSuccess(): void
    {
        $token = new Token();
        $token->setAccessToken('token_to_destroy');
        $this->service->setClientId('test_client_id');
        $this->service->setClientSecret('test_client_secret');

        $responseData = ['status' => 'success'];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($responseData));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn((object)$responseData);

        $this->connection->expects($this->once())
            ->method('post')
            ->with(Service::REVOKE_URI, $this->callback(function ($params) {
                return $params['token'] === 'token_to_destroy' &&
                       $params['client_id'] === 'test_client_id' &&
                       $params['client_secret'] === 'test_client_secret';
            }))
            ->willReturn($response);

        $result = $this->service->destroyToken($token);

        // Current implementation returns decoded object/array based on default return type
        $this->assertEquals((object)$responseData, $result);
    }
}
