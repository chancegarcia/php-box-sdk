<?php

namespace Box\Tests;

use Box\Client;
use Box\Http\Response\BoxResponseInterface;
use Box\Exception\BoxException;
use PHPUnit\Framework\TestCase;
use Box\Connection\Token\TokenInterface;
use Box\Connection\ConnectionInterface;
use Box\Factory\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Service\ConsoleOutputFormatter;
use Box\Service\DefaultJsonFormatter;
use Box\Logger\LoggerFactory;
use Box\Logger\ConfigNormalizer;
use Symfony\Component\Console\Application;
use Box\Command\FileUploadCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;

class ReproIssueTest extends TestCase
{
    public function testUploadFileToBoxWithEmpty401Response()
    {
        $client = new Client();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('fake_token');
        $client->setToken($token);

        $connection = $this->createMock(ConnectionInterface::class);
        $client->setConnection($connection);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn('');
        $response->method('getStatusCode')->willReturn(401);
        $response->method('isClientError')->willReturn(true);
        $response->method('isSuccessful')->willReturn(false);

        $connection->method('postFile')->willReturn($response);

        $this->expectException(BoxException::class);
        // We expect a message containing the HTTP status, not a JSON syntax error
        $this->expectExceptionMessage('Box Response was unsuccessful.');

        $client->uploadFileToBox('some/file/path');
    }

    public function testFileUploadCommandWithEmpty401Response()
    {
        $clientFactory = $this->createMock(BoxClientFactoryInterface::class);
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $outputFormatter = new ConsoleOutputFormatter(new DefaultJsonFormatter());
        $loggerFactory = new LoggerFactory(new ConfigNormalizer());

        $client = $this->createMock(Client::class);
        $connection = $this->createMock(ConnectionInterface::class);

        $clientFactory->method('createOAuth2Client')->willReturn($client);
        $client->method('getConnection')->willReturn($connection);
        $configProvider->method('getOAuth2AccessToken')->willReturn('fake_token');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn('');
        $response->method('getStatusCode')->willReturn(401);
        $response->method('isClientError')->willReturn(true);
        $response->method('isSuccessful')->willReturn(false);

        $connection->method('postFile')->willReturn($response);

        // Setup parseResponse to throw as it would in real Client
        $client->method('parseResponse')->willThrowException(new BoxException('Box API request failed with HTTP 401'));

        $testFile = sys_get_temp_dir() . '/test_upload_repro.txt';
        file_put_contents($testFile, 'test content');

        $application = new Application();
        $command = new FileUploadCommand($clientFactory, $configProvider, $outputFormatter, $loggerFactory);
        $application->addCommand($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file-path' => $testFile,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Failed to upload file: Box API request failed with HTTP 401', $output);
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        unlink($testFile);
    }
}
