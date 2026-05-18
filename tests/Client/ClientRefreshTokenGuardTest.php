<?php

declare(strict_types=1);

namespace Box\Tests\Client;

use Box\Auth\Jwt\JwtProviderInterface;
use Box\Auth\OAuth2ProviderInterface;
use Box\Client;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxException;
use PHPUnit\Framework\TestCase;

class ClientRefreshTokenGuardTest extends TestCase
{
    public function testRefreshTokenThrowsWhenOAuth2AndNoRefreshToken(): void
    {
        $authProvider = $this->createMock(OAuth2ProviderInterface::class);
        $client = new Client();
        $client->setAuthProvider($authProvider);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getRefreshToken')->willReturn(null);
        $client->setToken($token);

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('no refresh token available');

        $client->refreshToken();
    }

    public function testRefreshTokenDoesNotThrowWhenJwtAndNoRefreshToken(): void
    {
        $authProvider = $this->createMock(JwtProviderInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRefreshToken')->willReturn(null);

        $refreshedToken = $this->createMock(TokenInterface::class);

        $authProvider->expects($this->once())
            ->method('refreshToken')
            ->with($token)
            ->willReturn($refreshedToken);

        $client = new Client();
        $client->setAuthProvider($authProvider);
        $client->setToken($token);

        $result = $client->refreshToken();

        $this->assertSame($refreshedToken, $result);
    }
}
