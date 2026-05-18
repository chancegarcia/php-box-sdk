<?php

declare(strict_types=1);

namespace Box\Tests\Auth\Jwt;

use Box\Auth\Jwt\JwtAssertionGeneratorInterface;
use Box\Auth\Jwt\JwtAuthConfig;
use Box\Auth\Jwt\JwtProvider;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Event\Auth\JwtTokenGenerated;
use Box\Factory\TokenFactoryInterface;
use Box\Http\Response\BoxResponseInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class JwtTokenGeneratedEventTest extends TestCase
{
    private function makeConfig(): JwtAuthConfig
    {
        return new JwtAuthConfig(
            clientId: 'client_id',
            clientSecret: 'client_secret',
            enterpriseId: 'enterprise_id',
            publicKeyId: 'key_id',
            privateKey: '---FAKE-PEM---',
            privateKeyPassphrase: '',
        );
    }

    private function makeProvider(
        ConnectionInterface $connection,
        TokenFactoryInterface $tokenFactory,
    ): JwtProvider {
        $config = $this->makeConfig();
        $assertionGenerator = $this->createMock(JwtAssertionGeneratorInterface::class);
        $assertionGenerator->method('generate')->willReturn('fake.jwt.assertion');

        return new JwtProvider($connection, $tokenFactory, $config, $assertionGenerator);
    }

    private function makeTokenResponse(): BoxResponseInterface
    {
        $data = ['access_token' => 'tok123', 'expires_in' => 3600, 'token_type' => 'bearer'];
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object)$data);
        return $response;
    }

    public function testExchangeForEnterpriseTokenDispatchesJwtTokenGenerated(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('post')->willReturn($this->makeTokenResponse());

        $tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $tokenFactory->method('createToken')->willReturn($token);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(JwtTokenGenerated::class));

        $provider = $this->makeProvider($connection, $tokenFactory);
        $provider->setEventDispatcher($dispatcher);
        $provider->exchangeForEnterpriseToken();
    }

    public function testJwtTokenGeneratedEventHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('post')->willReturn($this->makeTokenResponse());

        $tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $tokenFactory->method('createToken')->willReturn($token);

        $capturedEvent = null;
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnCallback(function (object $event) use (&$capturedEvent) {
            $capturedEvent = $event;
            return $event;
        });

        $provider = $this->makeProvider($connection, $tokenFactory);
        $provider->setEventDispatcher($dispatcher);
        $provider->exchangeForEnterpriseToken();

        $this->assertInstanceOf(JwtTokenGenerated::class, $capturedEvent);
        $this->assertSame($token, $capturedEvent->token);
    }

    public function testNoDispatchWhenDispatcherNotSet(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('post')->willReturn($this->makeTokenResponse());

        $tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $tokenFactory->method('createToken')->willReturn($token);

        $provider = $this->makeProvider($connection, $tokenFactory);
        $result = $provider->exchangeForEnterpriseToken();

        $this->assertSame($token, $result);
    }
}
