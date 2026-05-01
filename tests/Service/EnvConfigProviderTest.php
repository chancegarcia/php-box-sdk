<?php

namespace Box\Tests\Service;

use Box\Service\EnvConfigProvider;
use PHPUnit\Framework\TestCase;

class EnvConfigProviderTest extends TestCase
{
    private array $backupEnv;
    private array $backupServer;

    protected function setUp(): void
    {
        $this->backupEnv = $_ENV;
        $this->backupServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->backupEnv;
        $_SERVER = $this->backupServer;
    }

    public function testGetRequiredEnvThrowsExceptionWhenMissing(): void
    {
        unset($_ENV['BOX_CLIENT_ID'], $_SERVER['BOX_CLIENT_ID']);
        $provider = new EnvConfigProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "BOX_CLIENT_ID" is required');
        $provider->getClientId();
    }

    public function testGetClientIdFromEnv(): void
    {
        $_ENV['BOX_CLIENT_ID'] = 'env_id';
        $provider = new EnvConfigProvider();
        $this->assertEquals('env_id', $provider->getClientId());
    }

    public function testGetClientSecretFromEnv(): void
    {
        $_ENV['BOX_CLIENT_SECRET'] = 'env_secret';
        $provider = new EnvConfigProvider();
        $this->assertEquals('env_secret', $provider->getClientSecret());
    }

    public function testGetClientSecretThrowsExceptionWhenMissing(): void
    {
        unset($_ENV['BOX_CLIENT_SECRET'], $_SERVER['BOX_CLIENT_SECRET']);
        $provider = new EnvConfigProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "BOX_CLIENT_SECRET" is required');
        $provider->getClientSecret();
    }

    public function testGetRedirectUriFromEnv(): void
    {
        $_ENV['BOX_REDIRECT_URI'] = 'https://redirect.example.com';
        $provider = new EnvConfigProvider();
        $this->assertEquals('https://redirect.example.com', $provider->getRedirectUri());
    }

    public function testGetAccessTokenFromEnv(): void
    {
        $_ENV['BOX_ACCESS_TOKEN'] = 'test_token';
        $provider = new EnvConfigProvider();
        $this->assertEquals('test_token', $provider->getAccessToken());
    }

    public function testGetAccessTokenReturnsNullWhenMissing(): void
    {
        unset($_ENV['BOX_ACCESS_TOKEN'], $_SERVER['BOX_ACCESS_TOKEN']);
        $provider = new EnvConfigProvider();
        $this->assertNull($provider->getAccessToken());
    }
}
