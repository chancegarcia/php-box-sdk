<?php

namespace Box\Tests\Command;

use Box\Command\FileUploadCommand;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Client;
use Box\Logger\ConfigNormalizer;
use Box\Logger\LoggerFactory;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\ConsoleOutputFormatter;
use Box\Service\DefaultJsonFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;
use Box\Connection\ConnectionInterface;

class FileUploadCommandTest extends TestCase
{
    private $clientFactory;
    private $configProvider;
    private $outputFormatter;
    private $loggerFactory;
    private $client;
    private $connection;
    private $testFile;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(BoxClientFactoryInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->outputFormatter = new ConsoleOutputFormatter(new DefaultJsonFormatter());
        $this->loggerFactory = new LoggerFactory(new ConfigNormalizer());
        $this->client = $this->createMock(Client::class);
        $this->connection = $this->createMock(ConnectionInterface::class);

        $this->clientFactory->method('createOAuth2Client')->willReturn($this->client);
        $this->client->method('getConnection')->willReturn($this->connection);

        $this->testFile = sys_get_temp_dir() . '/test_upload.txt';
        file_put_contents($this->testFile, 'test content');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testUploadFailsWhenTokenIsMissing(): void
    {
        $this->configProvider->method('getOAuth2AccessToken')->willReturn(null);

        $application = new Application();
        $application->add(new FileUploadCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:file:upload');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => $this->testFile,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('BOX_ACCESS_TOKEN is required for upload.', $output);
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        // Verify no upload was attempted
        $this->connection->expects($this->never())->method('postFile');
    }

    public function testUploadFailsWhenTokenIsEmpty(): void
    {
        $this->configProvider->method('getOAuth2AccessToken')->willReturn('');

        $application = new Application();
        $application->add(new FileUploadCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:file:upload');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => $this->testFile,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('BOX_ACCESS_TOKEN is required for upload.', $output);
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        $this->connection->expects($this->never())->method('postFile');
    }

    public function testUploadFailsWhenTokenIsWhitespace(): void
    {
        $this->configProvider->method('getOAuth2AccessToken')->willReturn('   ');

        $application = new Application();
        $application->add(new FileUploadCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:file:upload');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => $this->testFile,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('BOX_ACCESS_TOKEN is required for upload.', $output);
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        $this->connection->expects($this->never())->method('postFile');
    }

    public function testUploadSucceedsWhenTokenIsPresent(): void
    {
        $this->configProvider->method('getOAuth2AccessToken')->willReturn('valid_token');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'entries' => [['id' => '12345', 'name' => 'test.txt']]
        ]));

        $this->connection->expects($this->once())
            ->method('postFile')
            ->willReturn($response);

        $application = new Application();
        $application->add(new FileUploadCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:file:upload');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => $this->testFile,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('File uploaded successfully!', $output);
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
