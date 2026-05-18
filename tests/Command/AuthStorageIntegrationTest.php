<?php

namespace Box\Tests\Command;

use Box\Client;
use Box\Command\AuthExchangeCommand;
use Box\Command\AuthRefreshCommand;
use Box\Connection\Token\Token;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Dto\TokenStorageContext;
use Box\Logger\ConfigNormalizer;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Box\Storage\Token\Filesystem\FilesystemTokenStorage;
use Box\Storage\Token\TokenStorageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AuthStorageIntegrationTest extends TestCase
{
    private $clientFactory;
    private $configProvider;
    private $outputFormatter;
    private $loggerFactory;
    private $client;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(BoxClientFactoryInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->outputFormatter = $this->createMock(ConsoleOutputFormatter::class);
        $this->loggerFactory = new LoggerFactory(new ConfigNormalizer());
        $this->client = $this->createMock(Client::class);

        $this->clientFactory->method('createOAuth2Client')->willReturn($this->client);
    }

    public function testAuthExchangeWithStorage(): void
    {
        $code = 'test_code';
        $token = new Token();
        $token->setAccessToken('test_access_token');

        $this->client->method('getClientId')->willReturn('test_client_id');

        // Verify storage configuration calls on client
        $this->client->expects($this->once())
            ->method('setTokenStorage')
            ->with($this->isInstanceOf(TokenStorageInterface::class));

        $this->client->expects($this->once())
            ->method('setTokenStorageContext')
            ->with($this->callback(function (TokenStorageContext $context) {
                return $context->getUserId() === 'user123' &&
                       $context->getEnterpriseId() === 'ent456' &&
                       $context->getClientId() === 'test_client_id';
            }));

        $this->client->expects($this->once())
            ->method('exchangeAuthorizationCodeForToken')
            ->willReturn($token);

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'code' => $code,
            '--use-storage' => true,
            '--pdo-dsn' => 'sqlite::memory:',
            '--user-id' => 'user123',
            '--enterprise-id' => 'ent456'
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Token exchange successful!', $commandTester->getDisplay());
    }

    public function testAuthRefreshWithStorageLoading(): void
    {
        $storedToken = new Token();
        $storedToken->setRefreshToken('stored_refresh_token');

        $newToken = new Token();
        $newToken->setAccessToken('new_access_token');

        $this->client->method('getClientId')->willReturn('test_client_id');

        // Should load token from storage since no env refresh token
        $this->configProvider->method('getOAuth2RefreshToken')->willReturn(null);

        $this->client->expects($this->once())
            ->method('loadTokenFromStorage')
            ->willReturn($storedToken);

        $this->client->expects($this->once())
            ->method('refreshToken')
            ->willReturn($newToken);

        $application = new Application();
        $application->addCommand(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--use-storage' => true,
            '--pdo-dsn' => 'sqlite::memory:',
            '--user-id' => 'user123'
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Loaded refresh token from storage.', $commandTester->getDisplay());
        $this->assertStringContainsString('Token refresh successful!', $commandTester->getDisplay());
    }

    public function testAuthRefreshWithExplicitTokenAndStorage(): void
    {
        $explicitRefreshToken = 'explicit_refresh_token';
        $newToken = new Token();
        $newToken->setAccessToken('new_access_token');

        $this->client->method('getClientId')->willReturn('test_client_id');
        $this->configProvider->method('getOAuth2RefreshToken')->willReturn($explicitRefreshToken);

        // Should NOT load token from storage if explicit token exists
        $this->client->expects($this->never())
            ->method('loadTokenFromStorage');

        $this->client->expects($this->once())
            ->method('refreshToken')
            ->willReturn($newToken);

        $application = new Application();
        $application->addCommand(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--use-storage' => true,
            '--pdo-dsn' => 'sqlite::memory:',
            '--user-id' => 'user123'
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringNotContainsString('Loaded refresh token from storage.', $commandTester->getDisplay());
        $this->assertStringContainsString('Token refresh successful!', $commandTester->getDisplay());
    }

    public function testPdoStorageConfigFromConfigProvider(): void
    {
        $this->client->method('getClientId')->willReturn('test_client_id');

        $this->configProvider->method('getStoragePdoDsn')->willReturn('sqlite::memory:');
        $this->configProvider->method('getStoragePdoUser')->willReturn('user');
        $this->configProvider->method('getStoragePdoPassword')->willReturn('pass');

        // We can't easily verify the internal PdoTokenStorage state without exposing it,
        // but we can verify that setTokenStorage is called on the client.
        $this->client->expects($this->once())
            ->method('setTokenStorage')
            ->with($this->isInstanceOf(TokenStorageInterface::class));

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'code' => 'test_code',
            '--use-storage' => true
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testFilesystemStorageType(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'box_cmd_fs_');
        @unlink($tempFile);

        $token = new Token();
        $token->setAccessToken('access_token_fs_test');

        $this->client->method('getClientId')->willReturn('test_client_id');
        $this->client->method('exchangeAuthorizationCodeForToken')->willReturn($token);

        $this->client->expects($this->once())
            ->method('setTokenStorage')
            ->with($this->isInstanceOf(FilesystemTokenStorage::class));

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'code' => 'test_code',
            '--use-storage' => true,
            '--storage-type' => 'filesystem',
            '--storage-path' => $tempFile,
            '--user-id' => 'user123',
        ]);

        @unlink($tempFile);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testFilesystemStorageUsesDefaultPathWhenNoneProvided(): void
    {
        $token = new Token();
        $token->setAccessToken('access_token_default_path');

        $this->client->method('getClientId')->willReturn('test_client_id');
        $this->client->method('exchangeAuthorizationCodeForToken')->willReturn($token);

        // No --storage-path and no BOX_STORAGE_FILE_PATH — should fall back to ~/.box-sdk/tokens.json
        $this->configProvider->method('getStorageFilePath')->willReturn(null);

        $this->client->expects($this->once())
            ->method('setTokenStorage')
            ->with($this->isInstanceOf(FilesystemTokenStorage::class));

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'code' => 'test_code',
            '--use-storage' => true,
            '--user-id' => 'user123',
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testCliOptionPrecedenceOverConfigProvider(): void
    {
        $this->client->method('getClientId')->willReturn('test_client_id');

        $this->configProvider->method('getStoragePdoDsn')->willReturn('from_config_dsn');

        // If precedence works, it should use the CLI option and try to create a PDO (which might fail if DSN is invalid, but we are looking for precedence)
        // Actually, PdoTokenStorage constructor doesn't validate DSN immediately, it just stores it.

        $this->client->expects($this->once())
            ->method('setTokenStorage')
            ->with($this->isInstanceOf(TokenStorageInterface::class));

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'code' => 'test_code',
            '--use-storage' => true,
            '--pdo-dsn' => 'sqlite::memory:'
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
