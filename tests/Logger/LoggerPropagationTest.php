<?php

namespace Box\Tests\Logger;

use Box\Client;
use Box\Factory\BoxClientFactory;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Connection\ConnectionInterface;
use Box\Resource\Folder;
use Box\Resource\Collaboration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Box\Command\FileUploadCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class LoggerPropagationTest extends TestCase
{
    public function testLoggerPropagatesFromFactoryToClient(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $configProvider = $this->createMock(ConfigProviderInterface::class);

        $factory = new BoxClientFactory($configProvider);
        $factory->setLogger($logger);

        $client = $factory->createOAuth2Client();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame($logger, $client->getLogger());
    }

    public function testLoggerPropagatesFromClientToConnection(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $connection = $client->getConnection();

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertSame($logger, $connection->getLogger());
    }

    public function testResourcesArePassiveAndNotLoggerAware(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $folder = $client->getNewFolder(['id' => '123']);

        $this->assertInstanceOf(Folder::class, $folder);
        // Resources should no longer have loggers propagated from Client in v1
        $this->assertFalse(method_exists($folder, 'getLogger'));
        $this->assertFalse(method_exists($folder, 'setLogger'));
    }

    public function testCollaborationIsPassiveAndNotLoggerAware(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $collaboration = $client->getNewCollaboration(['id' => '123']);

        $this->assertInstanceOf(Collaboration::class, $collaboration);
        // Resources should no longer have loggers propagated from Client in v1
        $this->assertFalse(method_exists($collaboration, 'getLogger'));
        $this->assertFalse(method_exists($collaboration, 'setLogger'));
    }


    public function testCliCommandInjectsLoggerIntoFactory(): void
    {
        $factory = $this->createMock(BoxClientFactoryInterface::class);
        $loggerFactory = $this->createMock(LoggerFactory::class);
        $logger = $this->createMock(LoggerInterface::class);

        $loggerFactory->method('createLogger')->willReturn($logger);

        // Expect logger to be set on factory
        $factory->expects($this->once())
            ->method('setLogger')
            ->with($logger);

        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $outputFormatter = $this->createMock(ConsoleOutputFormatter::class);

        $command = new FileUploadCommand($factory, $configProvider, $outputFormatter, $loggerFactory);

        $application = new Application();
        $application->addCommand($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => 'dummy.txt',
        ]);
    }
}
