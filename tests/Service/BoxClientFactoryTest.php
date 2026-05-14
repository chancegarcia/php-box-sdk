<?php

namespace Box\Tests\Service;

use Box\Auth\Jwt\JwtAuthConfig;
use Box\Auth\Jwt\JwtProviderInterface;
use Box\Auth\OAuth2ProviderInterface;
use Box\Client;
use Box\Contract\ConfigProviderInterface;
use Box\Service\BoxClientFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BoxClientFactoryTest extends TestCase
{
    public function testCreateClientWithGenericConfigProvider(): void
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider->method('getOAuth2ClientId')->willReturn('test-client-id');
        $configProvider->method('getOAuth2ClientSecret')->willReturn('test-client-secret');
        $configProvider->method('getOAuth2RedirectUri')->willReturn('https://redirect');
        $configProvider->method('getOAuth2State')->willReturn('test-state');
        $configProvider->method('getOAuth2AuthCode')->willReturn('test-auth-code');

        $factory = new BoxClientFactory($configProvider);
        $client = $factory->createClient();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('test-client-id', $client->getClientId());
        $this->assertEquals('test-client-secret', $client->getClientSecret());
        $this->assertEquals('test-auth-code', $client->getAuthorizationCode());
    }

    public function testCreateClientWithLogger(): void
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $factory = new BoxClientFactory($configProvider);
        $factory->setLogger($logger);
        $client = $factory->createClient();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateJwtClientReturnsClientWithJwtProvider(): void
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $factory = new BoxClientFactory($configProvider);

        $config = new JwtAuthConfig(
            clientId: 'test_client_id',
            clientSecret: 'test_client_secret',
            enterpriseId: 'test_enterprise_id',
            publicKeyId: 'test_key_id',
            privateKey: 'placeholder-private-key',
        );

        $client = $factory->createJwtClient($config);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(JwtProviderInterface::class, $client->getAuthProvider());
    }

    public function testCreateClientForCurrentModeReturnsOAuth2ClientByDefault(): void
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider->method('getAuthMode')->willReturn('oauth2');
        $configProvider->method('getOAuth2ClientId')->willReturn('test-id');
        $configProvider->method('getOAuth2ClientSecret')->willReturn('test-secret');

        $factory = new BoxClientFactory($configProvider);
        $client = $factory->createClientForCurrentMode();

        $this->assertInstanceOf(OAuth2ProviderInterface::class, $client->getAuthProvider());
    }

    public function testCreateClientForCurrentModeReturnsJwtClientWhenModeIsJwt(): void
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider->method('getAuthMode')->willReturn('jwt');
        $configProvider->method('getJwtClientId')->willReturn('test-jwt-id');
        $configProvider->method('getJwtClientSecret')->willReturn('test-jwt-secret');
        $configProvider->method('getJwtEnterpriseId')->willReturn('test-enterprise-id');
        $configProvider->method('getJwtPublicKeyId')->willReturn('test-key-id');
        $configProvider->method('getJwtPrivateKey')->willReturn('placeholder-pem');
        $configProvider->method('getJwtPrivateKeyPassphrase')->willReturn('pass');

        $factory = new BoxClientFactory($configProvider);
        $client = $factory->createClientForCurrentMode();

        $this->assertInstanceOf(JwtProviderInterface::class, $client->getAuthProvider());
    }
}
