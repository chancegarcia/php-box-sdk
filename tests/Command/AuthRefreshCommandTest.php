<?php

namespace Box\Tests\Command;

use Box\Command\AuthRefreshCommand;
use Box\Contract\BoxClientFactoryInterface;
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

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(BoxClientFactoryInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->outputFormatter = $this->createMock(ConsoleOutputFormatter::class);
        $this->loggerFactory = new LoggerFactory(new ConfigNormalizer());
    }

    public function testRefreshTokenOptionIsNotAvailable(): void
    {
        $application = new Application();
        $application->add(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $this->assertFalse($command->getDefinition()->hasOption('refresh-token'));
    }

    public function testExecuteFailsWhenRefreshTokenIsMissingInConfig(): void
    {
        $this->configProvider->method('getRefreshToken')->willReturn(null);

        $application = new Application();
        $application->add(new AuthRefreshCommand($this->clientFactory, $this->configProvider, $this->outputFormatter, $this->loggerFactory));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Refresh token is required. Set BOX_REFRESH_TOKEN env.', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
