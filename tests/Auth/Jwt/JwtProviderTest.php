<?php

namespace Box\Tests\Auth\Jwt;

use Box\Auth\AuthProviderInterface;
use Box\Auth\Jwt\JwtAuthConfig;
use Box\Auth\Jwt\JwtAssertionGeneratorInterface;
use Box\Auth\Jwt\JwtProvider;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxException;
use Box\Factory\TokenFactoryInterface;
use Box\Http\Response\BoxResponseInterface;
use PHPUnit\Framework\TestCase;

class JwtProviderTest extends TestCase
{
    private $connection;
    private $tokenFactory;
    private $assertionGenerator;
    private $config;
    private $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(ConnectionInterface::class);
        $this->tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $this->assertionGenerator = $this->createMock(JwtAssertionGeneratorInterface::class);
        $this->config = new JwtAuthConfig(
            clientId: 'test_client_id',
            clientSecret: 'test_client_secret',
            enterpriseId: 'test_enterprise_id',
            publicKeyId: 'test_key_id',
            privateKey: 'placeholder-not-used-in-provider-tests',
        );
        $this->provider = new JwtProvider(
            $this->connection,
            $this->tokenFactory,
            $this->config,
            $this->assertionGenerator,
        );
    }

    private function mockSuccessfulExchange(string $assertion = 'stub.jwt.assertion')
    {
        $this->assertionGenerator->method('generate')->willReturn($assertion);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->willReturn([
            'access_token' => 'access',
            'expires_in' => 3600,
            'token_type' => 'bearer'
        ]);

        $this->connection->method('post')->willReturn($response);

        $token = $this->createMock(TokenInterface::class);
        $this->tokenFactory->method('createToken')->willReturn($token);

        return $token;
    }

    public function testBuildAuthorizationUrlThrows(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('JWT authentication does not support browser-based authorization flows.');
        $this->provider->buildAuthorizationUrl();
    }

    public function testExchangeAuthorizationCodeThrows(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('JWT authentication does not use authorization codes.');
        $this->provider->exchangeAuthorizationCode('code');
    }

    public function testExchangeForEnterpriseToken(): void
    {
        $mockToken = $this->mockSuccessfulExchange();

        $this->assertionGenerator->expects($this->once())
            ->method('generate')
            ->with($this->config, $this->config->enterpriseId, 'enterprise');

        $result = $this->provider->exchangeForEnterpriseToken();
        $this->assertSame($mockToken, $result);
    }

    public function testExchangeForAppUserToken(): void
    {
        $mockToken = $this->mockSuccessfulExchange();

        $this->assertionGenerator->expects($this->once())
            ->method('generate')
            ->with($this->config, 'user-123', 'user');

        $result = $this->provider->exchangeForAppUserToken('user-123');
        $this->assertSame($mockToken, $result);
    }

    public function testRefreshTokenAfterEnterpriseExchangeReusesEnterpriseState(): void
    {
        $mockToken = $this->mockSuccessfulExchange();

        // Initial exchange
        $this->provider->exchangeForEnterpriseToken();

        // Refresh
        $this->assertionGenerator->expects($this->once())
            ->method('generate')
            ->with($this->config, $this->config->enterpriseId, 'enterprise');

        $result = $this->provider->refreshToken($mockToken);
        $this->assertSame($mockToken, $result);
    }

    public function testRefreshTokenAfterAppUserExchangeReusesAppUserState(): void
    {
        $mockToken = $this->mockSuccessfulExchange();

        // Initial exchange
        $this->provider->exchangeForAppUserToken('user-456');

        // Refresh
        $this->assertionGenerator->expects($this->once())
            ->method('generate')
            ->with($this->config, 'user-456', 'user');

        $result = $this->provider->refreshToken($mockToken);
        $this->assertSame($mockToken, $result);
    }

    public function testRefreshTokenWithNoPriorExchangeDefaultsToEnterpriseState(): void
    {
        $mockToken = $this->mockSuccessfulExchange();

        $this->assertionGenerator->expects($this->once())
            ->method('generate')
            ->with($this->config, $this->config->enterpriseId, 'enterprise');

        $result = $this->provider->refreshToken($mockToken);
        $this->assertSame($mockToken, $result);
    }

    public function testRevokeTokenPostsToCorrectUri(): void
    {
        $mockToken = $this->createMock(TokenInterface::class);
        $mockToken->method('getAccessToken')->willReturn('test-access-token');

        $this->connection->expects($this->once())
            ->method('post')
            ->with(
                AuthProviderInterface::REVOKE_URI,
                [
                    'client_id' => 'test_client_id',
                    'client_secret' => 'test_client_secret',
                    'token' => 'test-access-token',
                ]
            );

        $this->provider->revokeToken($mockToken);
    }

    public function testExchangeAssertionThrowsOnNonArrayResponse(): void
    {
        $this->assertionGenerator->method('generate')->willReturn('stub.assertion');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->willReturn(null);

        $this->connection->method('post')->willReturn($response);

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Invalid response from Box API during JWT assertion exchange');

        $this->provider->exchangeForEnterpriseToken();
    }
}
