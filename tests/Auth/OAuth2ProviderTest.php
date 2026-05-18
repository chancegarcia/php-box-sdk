<?php

namespace Box\Tests\Auth;

use Box\Auth\OAuth2Provider;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Factory\TokenFactoryInterface;
use Box\Http\Response\BoxResponseInterface;
use PHPUnit\Framework\TestCase;

class OAuth2ProviderTest extends TestCase
{
    private $connection;
    private $tokenFactory;
    private $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(ConnectionInterface::class);
        $this->tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $this->provider = new OAuth2Provider(
            $this->connection,
            $this->tokenFactory,
            'test_client_id',
            'test_client_secret',
            'https://redirect.uri'
        );
    }

    public function testBuildAuthorizationUrl(): void
    {
        $url = $this->provider->buildAuthorizationUrl(['state' => 'test_state']);

        $this->assertStringStartsWith(OAuth2Provider::AUTH_URI, $url);
        $this->assertStringContainsString('client_id=test_client_id', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fredirect.uri', $url);
        $this->assertStringContainsString('state=test_state', $url);
    }

    public function testExchangeAuthorizationCode(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->willReturn([
            'access_token' => 'access',
            'refresh_token' => 'refresh',
            'expires_in' => 3600,
            'token_type' => 'bearer'
        ]);

        $this->connection->expects($this->once())
            ->method('post')
            ->with(
                OAuth2Provider::TOKEN_URI,
                [
                    'grant_type' => 'authorization_code',
                    'code' => 'test_code',
                    'client_id' => 'test_client_id',
                    'client_secret' => 'test_client_secret',
                    'redirect_uri' => 'https://redirect.uri'
                ]
            )
            ->willReturn($response);

        $token = $this->createMock(TokenInterface::class);
        $this->tokenFactory->method('createToken')->willReturn($token);

        $result = $this->provider->exchangeAuthorizationCode('test_code');
        $this->assertSame($token, $result);
    }

    public function testRefreshToken(): void
    {
        $oldToken = $this->createMock(TokenInterface::class);
        $oldToken->method('getRefreshToken')->willReturn('old_refresh');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->willReturn([
            'access_token' => 'new_access',
            'refresh_token' => 'new_refresh',
            'expires_in' => 3600,
            'token_type' => 'bearer'
        ]);

        $this->connection->expects($this->once())
            ->method('post')
            ->with(
                OAuth2Provider::TOKEN_URI,
                [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => 'old_refresh',
                    'client_id' => 'test_client_id',
                    'client_secret' => 'test_client_secret',
                    'device_id' => 'device123'
                ]
            )
            ->willReturn($response);

        $newToken = $this->createMock(TokenInterface::class);
        $this->tokenFactory->method('createToken')->willReturn($newToken);

        $result = $this->provider->refreshToken($oldToken, ['device_id' => 'device123']);
        $this->assertSame($newToken, $result);
    }

    public function testRevokeToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('access_to_revoke');

        $this->connection->expects($this->once())
            ->method('post')
            ->with(
                OAuth2Provider::REVOKE_URI,
                [
                    'client_id' => 'test_client_id',
                    'client_secret' => 'test_client_secret',
                    'token' => 'access_to_revoke'
                ]
            );

        $this->provider->revokeToken($token);
    }
}
