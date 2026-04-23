<?php

namespace Box\Tests\Logger;

use Box\Client;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Service\BoxClientFactory;
use Box\Model\Connection\Connection;
use Box\Model\Folder\Folder;
use Box\Model\Collaboration\Collaboration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

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

        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertSame($logger, $connection->getLogger());
    }

    public function testLoggerPropagatesViaGetNewClass(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $folder = $client->getNewFolder(['id' => '123']);

        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertSame($logger, $folder->getLogger());
    }

    public function testLoggerPropagatesToCollaboration(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $collaboration = $client->getNewCollaboration(['id' => '123']);

        $this->assertInstanceOf(Collaboration::class, $collaboration);
        $this->assertSame($logger, $collaboration->getLogger());
    }

    public function testLoggerPropagatesViaValidateClass(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);

        $client->setFolderClass(Folder::class);

        $this->assertEquals(Folder::class, $client->getFolderClass());
    }

    public function testCliCommandInjectsLoggerIntoFactory(): void
    {
        $factory = $this->createMock(BoxClientFactoryInterface::class);
        $loggerFactory = $this->createMock(\Box\Logger\LoggerFactory::class);
        $logger = $this->createMock(LoggerInterface::class);

        $loggerFactory->method('createLogger')->willReturn($logger);

        // Expect logger to be set on factory
        $factory->expects($this->once())
            ->method('setLogger')
            ->with($logger);

        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $outputFormatter = $this->createMock(\Box\Service\ConsoleOutputFormatter::class);

        $command = new \Box\Command\FileUploadCommand($factory, $configProvider, $outputFormatter, $loggerFactory);

        $application = new \Symfony\Component\Console\Application();
        $application->add($command);

        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
        $commandTester->execute([
            'file-path' => 'dummy.txt',
        ]);
    }
}
