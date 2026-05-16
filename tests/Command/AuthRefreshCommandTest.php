<?php

namespace Box\Tests\Command;

use Box\Client;
use Box\Command\AuthRefreshCommand;
use Box\Connection\Token\Token;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\ConfigNormalizer;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AuthRefreshCommandTest extends TestCase
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

    public function testRefreshTokenOptionIsNotAvailable(): void
    {
        $application = new Application();
        $application->add(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $this->assertFalse($command->getDefinition()->hasOption('refresh-token'));
    }

    public function testExecuteSuccessful(): void
    {
        $refreshToken = 'test_refresh_token';
        $newToken = new Token();
        $newToken->setAccessToken('new_access_token');

        $this->configProvider->method('getOAuth2RefreshToken')->willReturn($refreshToken);

        $this->client->expects($this->once())
            ->method('setToken')
            ->with($this->callback(function ($token) use ($refreshToken) {
                return $token instanceof Token && $token->getRefreshToken() === $refreshToken;
            }));

        $this->client->expects($this->once())
            ->method('refreshToken')
            ->willReturn($newToken);

        $application = new Application();
        $application->add(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Token refresh successful!', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteFailsWhenRefreshTokenIsMissingInConfig(): void
    {
        $this->configProvider->method('getOAuth2RefreshToken')->willReturn(null);

        $application = new Application();
        $application->add(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Refresh token is required.', $output);
        $this->assertStringContainsString('BOX_REFRESH_TOKEN env', $output);
        $this->assertStringContainsString('enable storage', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
