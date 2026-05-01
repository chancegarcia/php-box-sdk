<?php

namespace Box\Tests;

use Box\Client;
use Box\Http\Response\BoxResponseInterface;
use Box\Connection\Connection;
use Box\Connection\Token\Token;
use Box\Exception\BoxException;
use PHPUnit\Framework\TestCase;

class ReproIssueTest extends TestCase
{
    public function testUploadFileToBoxWithEmpty401Response()
    {
        $client = new Client();

        $token = new Token();
        $token->setAccessToken('fake_token');
        $client->setToken($token);

        $connection = $this->createMock(Connection::class);
        $client->setConnection($connection);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn('');
        $response->method('getStatusCode')->willReturn(401);
        $response->method('isClientError')->willReturn(true);
        $response->method('isSuccessful')->willReturn(false);

        $connection->method('postFile')->willReturn($response);

        $this->expectException(BoxException::class);
        // We expect a message containing the HTTP status, not a JSON syntax error
        $this->expectExceptionMessageMatches('/Box API (request|upload) failed with HTTP 401/i');

        $client->uploadFileToBox('some/file/path');
    }

    public function testFileUploadCommandWithEmpty401Response()
    {
        $clientFactory = $this->createMock(\Box\Contract\BoxClientFactoryInterface::class);
        $configProvider = $this->createMock(\Box\Contract\ConfigProviderInterface::class);
        $outputFormatter = new \Box\Service\ConsoleOutputFormatter(new \Box\Service\DefaultJsonFormatter());
        $loggerFactory = new \Box\Logger\LoggerFactory(new \Box\Logger\ConfigNormalizer());

        $client = $this->createMock(Client::class);
        $connection = $this->createMock(Connection::class);

        $clientFactory->method('createClient')->willReturn($client);
        $client->method('getConnection')->willReturn($connection);
        $configProvider->method('getAccessToken')->willReturn('fake_token');

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

        $application = new \Symfony\Component\Console\Application();
        $command = new \Box\Command\FileUploadCommand($clientFactory, $configProvider, $outputFormatter, $loggerFactory);
        $application->add($command);

        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
        $commandTester->execute([
            'file-path' => $testFile,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Failed to upload file: Box API request failed with HTTP 401', $output);
        $this->assertEquals(\Symfony\Component\Console\Command\Command::FAILURE, $commandTester->getStatusCode());

        unlink($testFile);
    }
}
