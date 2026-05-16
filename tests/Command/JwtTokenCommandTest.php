<?php

namespace Box\Tests\Command;

use Box\Auth\Jwt\JwtProviderInterface;
use Box\Client;
use Box\Command\JwtTokenCommand;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Box\Connection\Token\TokenInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class JwtTokenCommandTest extends TestCase
{
    private $clientFactory;
    private $configProvider;
    private $outputFormatter;
    private $loggerFactory;
    private $client;
    private $jwtProvider;
    private $token;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(BoxClientFactoryInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->outputFormatter = $this->createMock(ConsoleOutputFormatter::class);
        $this->loggerFactory = $this->createMock(LoggerFactory::class);
        $this->client = $this->createMock(Client::class);
        $this->jwtProvider = $this->createMock(JwtProviderInterface::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->loggerFactory->method('createLogger')->willReturn($this->createMock(LoggerInterface::class));
        $this->clientFactory->method('createJwtClient')->willReturn($this->client);
        $this->client->method('getAuthProvider')->willReturn($this->jwtProvider);
        $this->token->method('toArray')->willReturn(['access_token' => 'test-token']);

        // Setup common config provider mock returns
        $this->configProvider->method('getJwtClientId')->willReturn('client-id');
        $this->configProvider->method('getJwtClientSecret')->willReturn('client-secret');
        $this->configProvider->method('getJwtEnterpriseId')->willReturn('enterprise-id');
        $this->configProvider->method('getJwtPublicKeyId')->willReturn('key-id');
        $this->configProvider->method('getJwtPrivateKey')->willReturn('private-key');
    }

    public function testExecuteExchangesForEnterpriseToken(): void
    {
        $this->jwtProvider->expects($this->once())
            ->method('exchangeForEnterpriseToken')
            ->willReturn($this->token);

        $application = new Application();
        $command = new JwtTokenCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory);
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteExchangesForAppUserToken(): void
    {
        $this->jwtProvider->expects($this->once())
            ->method('exchangeForAppUserToken')
            ->with('user-123')
            ->willReturn($this->token);

        $application = new Application();
        $command = new JwtTokenCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory);
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['--user-id' => 'user-123']);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
