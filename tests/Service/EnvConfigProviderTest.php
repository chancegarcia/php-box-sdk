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
        unset($_ENV['BOX_OAUTH_CLIENT_ID'], $_SERVER['BOX_OAUTH_CLIENT_ID']);
        $provider = new EnvConfigProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "BOX_OAUTH_CLIENT_ID" is required');
        $provider->getClientId();
    }

    public function testGetClientIdFromEnv(): void
    {
        $_ENV['BOX_OAUTH_CLIENT_ID'] = 'env_id';
        $provider = new EnvConfigProvider();
        $this->assertEquals('env_id', $provider->getClientId());
    }

    public function testGetClientSecretFromEnv(): void
    {
        $_ENV['BOX_OAUTH_CLIENT_SECRET'] = 'env_secret';
        $provider = new EnvConfigProvider();
        $this->assertEquals('env_secret', $provider->getClientSecret());
    }

    public function testGetClientSecretThrowsExceptionWhenMissing(): void
    {
        unset($_ENV['BOX_OAUTH_CLIENT_SECRET'], $_SERVER['BOX_OAUTH_CLIENT_SECRET']);
        $provider = new EnvConfigProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "BOX_OAUTH_CLIENT_SECRET" is required');
        $provider->getClientSecret();
    }

    public function testGetRedirectUriFromEnv(): void
    {
        $_ENV['BOX_OAUTH_REDIRECT_URI'] = 'https://redirect.example.com';
        $provider = new EnvConfigProvider();
        $this->assertEquals('https://redirect.example.com', $provider->getRedirectUri());
    }

    public function testGetAccessTokenFromEnv(): void
    {
        $_ENV['BOX_OAUTH_ACCESS_TOKEN'] = 'test_token';
        $provider = new EnvConfigProvider();
        $this->assertEquals('test_token', $provider->getAccessToken());
    }

    public function testGetAccessTokenReturnsNullWhenMissing(): void
    {
        unset($_ENV['BOX_OAUTH_ACCESS_TOKEN'], $_SERVER['BOX_OAUTH_ACCESS_TOKEN']);
        $provider = new EnvConfigProvider();
        $this->assertNull($provider->getAccessToken());
    }

    public function testGetAuthModeDefaultsToOauth2(): void
    {
        unset($_ENV['BOX_AUTH_MODE'], $_SERVER['BOX_AUTH_MODE']);
        $provider = new EnvConfigProvider();
        $this->assertEquals('oauth2', $provider->getAuthMode());
    }

    public function testGetAuthModeReturnsJwt(): void
    {
        $_ENV['BOX_AUTH_MODE'] = 'jwt';
        $provider = new EnvConfigProvider();
        $this->assertEquals('jwt', $provider->getAuthMode());
    }

    public function testGetJwtPrivateKeyReadsFile(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'box_jwt_test');
        file_put_contents($tempFile, 'test_private_key_content');
        $_ENV['BOX_JWT_PRIVATE_KEY_PATH'] = $tempFile;

        $provider = new EnvConfigProvider();
        try {
            $this->assertEquals('test_private_key_content', $provider->getJwtPrivateKey());
        } finally {
            unlink($tempFile);
        }
    }

    public function testGetJwtPrivateKeyThrowsOnMissingFile(): void
    {
        $_ENV['BOX_JWT_PRIVATE_KEY_PATH'] = '/non/existent/path';
        $provider = new EnvConfigProvider();

        $this->expectException(\Box\Exception\BoxException::class);
        $this->expectExceptionMessage('JWT private key file "/non/existent/path" does not exist or is not readable');
        $provider->getJwtPrivateKey();
    }

    public function testGetJwtPrivateKeyPassphraseReturnsNullWhenUnset(): void
    {
        unset($_ENV['BOX_JWT_PRIVATE_KEY_PASSPHRASE'], $_SERVER['BOX_JWT_PRIVATE_KEY_PASSPHRASE']);
        $provider = new EnvConfigProvider();
        $this->assertNull($provider->getJwtPrivateKeyPassphrase());
    }
}
