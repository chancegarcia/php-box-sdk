<?php

namespace Box\Tests\Command;

use Box\Client;
use Box\Command\AuthRefreshCommand;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\ConfigNormalizer;
use Box\Logger\LoggerFactory;
use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;
use Box\Service\ConsoleOutputFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use InvalidArgumentException;
use Box\Connection\Token\TokenInterface;

class TransportOptionTest extends TestCase
{
    private $clientFactory;
    private $configProvider;
    private $outputFormatter;
    private $loggerFactory;
    private $client;
    private $connection;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(BoxClientFactoryInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->outputFormatter = $this->createMock(ConsoleOutputFormatter::class);
        $this->loggerFactory = new LoggerFactory(new ConfigNormalizer());

        $this->client = $this->createMock(Client::class);
        $this->connection = $this->createMock(ConnectionInterface::class);

        $this->clientFactory->method('createClient')->willReturn($this->client);
        $this->client->method('getConnection')->willReturn($this->connection);
    }

    public function testTransportOptionIsAvailable(): void
    {
        $application = new Application();
        $application->add(new AuthRefreshCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:auth:refresh-token');
        $this->assertTrue($command->getDefinition()->hasOption('transport'));
    }

    public function testApplyCurlTransport(): void
    {
        $this->configProvider->method('getRefreshToken')->willReturn('some-token');

        $token = $this->createMock(TokenInterface::class);
        $token->method('toArray')->willReturn([]);
        $this->client->method('refreshToken')->willReturn($token);

        $this->connection->expects($this->once())
            ->method('setTransportName')
            ->with(Connection::TRANSPORT_CURL);

        $application = new Application();
        $application->add(new AuthRefreshCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--transport' => 'curl']);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testApplyGuzzleTransport(): void
    {
        $this->configProvider->method('getRefreshToken')->willReturn('some-token');

        $token = $this->createMock(TokenInterface::class);
        $token->method('toArray')->willReturn([]);
        $this->client->method('refreshToken')->willReturn($token);

        $this->connection->expects($this->once())
            ->method('setTransportName')
            ->with(Connection::TRANSPORT_GUZZLE);

        $application = new Application();
        $application->add(new AuthRefreshCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--transport' => 'guzzle']);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testInvalidTransportFails(): void
    {
        $application = new Application();
        $application->add(new AuthRefreshCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transport "foo". Allowed transports: curl, guzzle.');

        $commandTester->execute(['--transport' => 'foo']);
    }

    public function testNoTransportDoesNotSetAnything(): void
    {
        $this->configProvider->method('getRefreshToken')->willReturn('some-token');

        $token = $this->createMock(TokenInterface::class);
        $token->method('toArray')->willReturn([]);
        $this->client->method('refreshToken')->willReturn($token);

        $this->connection->expects($this->never())
            ->method('setTransportName');

        $application = new Application();
        $application->add(new AuthRefreshCommand(
            $this->clientFactory,
            $this->configProvider,
            $this->outputFormatter,
            $this->loggerFactory
        ));

        $command = $application->find('box:auth:refresh-token');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
