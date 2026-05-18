<?php

namespace Box\Tests\Command;

use Box\Client;
use Box\Command\AuthRefreshCommand;
use Box\Command\AuthExchangeCommand;
use Box\Connection\Token\Token;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\ConfigNormalizer;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Box\Service\DefaultJsonFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RedactionTest extends TestCase
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
        // Use real formatter to test actual redaction logic
        $this->outputFormatter = new ConsoleOutputFormatter(new DefaultJsonFormatter());
        $this->loggerFactory = new LoggerFactory(new ConfigNormalizer());
        $this->client = $this->createMock(Client::class);

        $this->clientFactory->method('createOAuth2Client')->willReturn($this->client);
    }

    public function testAuthRefreshRedaction(): void
    {
        $refreshToken = 'real_refresh_token_12345';
        $newToken = new Token();
        $newToken->setAccessToken('real_access_token_12345');
        $newToken->setRefreshToken('new_real_refresh_token_12345');

        $this->configProvider->method('getOAuth2RefreshToken')->willReturn($refreshToken);
        $this->client->method('refreshToken')->willReturn($newToken);

        $application = new Application();
        $application->addCommand(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        // Should NOT contain real tokens
        $this->assertStringNotContainsString('real_access_token_12345', $output);
        $this->assertStringNotContainsString('new_real_refresh_token_12345', $output);

        // Should contain masked tokens
        $this->assertStringContainsString('real...2345', $output);
    }

    public function testAuthExchangeRedaction(): void
    {
        $code = 'real_auth_code_12345';
        $newToken = new Token();
        $newToken->setAccessToken('real_access_token_12345');
        $newToken->setRefreshToken('new_real_refresh_token_12345');

        $this->configProvider->method('getOAuth2AuthCode')->willReturn($code);
        $this->client->method('exchangeAuthorizationCodeForToken')->willReturn($newToken);

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['code' => $code]);

        $output = $commandTester->getDisplay();

        // Should NOT contain real tokens
        $this->assertStringNotContainsString('real_access_token_12345', $output);
        $this->assertStringNotContainsString('new_real_refresh_token_12345', $output);

        // Should contain masked tokens
        $this->assertStringContainsString('real...2345', $output);
    }
}
