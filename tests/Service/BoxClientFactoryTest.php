<?php

namespace Box\Tests\Service;

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
        $configProvider->method('getClientId')->willReturn('test-client-id');
        $configProvider->method('getClientSecret')->willReturn('test-client-secret');
        $configProvider->method('getRedirectUri')->willReturn('https://redirect');
        $configProvider->method('getState')->willReturn('test-state');
        $configProvider->method('getAuthCode')->willReturn('test-auth-code');

        $factory = new BoxClientFactory($configProvider);
        $client = $factory->createClient();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('test-client-id', $client->getClientId());
        $this->assertEquals('test-client-secret', $client->getClientSecret());
        $this->assertEquals('https://redirect', $client->getRedirectUri());
        $this->assertEquals('test-auth-code', $client->getAuthorizationCode());
        $this->assertEquals('test-state', $client->getState());
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
}
