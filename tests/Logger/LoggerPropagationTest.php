<?php

namespace Box\Tests\Logger;

use Box\Client;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Service\BoxClientFactory;
use Box\Connection\ConnectionInterface;
use Box\Folder\FolderInterface;
use Box\Collaboration\CollaborationInterface;
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

        $client = $factory->createClient();

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

    public function testLoggerPropagatesViaFactory(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $folder = $client->getNewFolder(['id' => '123']);

        $this->assertInstanceOf(FolderInterface::class, $folder);
        $this->assertSame($logger, $folder->getLogger());
    }

    public function testLoggerPropagatesToCollaboration(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $collaboration = $client->getNewCollaboration(['id' => '123']);

        $this->assertInstanceOf(CollaborationInterface::class, $collaboration);
        $this->assertSame($logger, $collaboration->getLogger());
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
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => 'dummy.txt',
        ]);
    }
}
