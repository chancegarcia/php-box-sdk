<?php

namespace Box\Tests\Command;

use Box\Client;
use Box\Command\AuthExchangeCommand;
use Box\Connection\Token\Token;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\ConfigNormalizer;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AuthExchangeCommandTest extends TestCase
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

    public function testExecuteSuccessfulWithArgument(): void
    {
        $code = 'test_code';
        $token = new Token();
        $token->setAccessToken('test_access_token');

        $this->client->expects($this->once())
            ->method('exchangeAuthorizationCodeForToken')
            ->willReturn($token);

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['code' => $code]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Token exchange successful!', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteSuccessfulWithEnv(): void
    {
        $code = 'env_code';
        $token = new Token();
        $token->setAccessToken('test_access_token');

        $this->configProvider->method('getOAuth2AuthCode')->willReturn($code);

        $this->client->expects($this->once())
            ->method('exchangeAuthorizationCodeForToken')
            ->willReturn($token);

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Token exchange successful!', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteFailsWhenCodeIsMissing(): void
    {
        $this->configProvider->method('getOAuth2AuthCode')->willReturn(null);

        $application = new Application();
        $application->addCommand(new AuthExchangeCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:exchange-code');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Authorization code is required', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
