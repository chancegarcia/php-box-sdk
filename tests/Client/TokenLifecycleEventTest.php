<?php

declare(strict_types=1);

namespace Box\Tests\Client;

use Box\Auth\AuthProviderInterface;
use Box\Client;
use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Event\Auth\TokenExchanged;
use Box\Event\Auth\TokenLoadedFromStorage;
use Box\Event\Auth\TokenRefreshed;
use Box\Event\Auth\TokenRevoked;
use Box\Event\Auth\TokenSavedToStorage;
use Box\Storage\Token\TokenStorageInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class TokenLifecycleEventTest extends TestCase
{
    private function makeToken(): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('tok_abc');
        $token->method('getRefreshToken')->willReturn('ref_abc');
        $token->method('getExpiresIn')->willReturn(3600);
        return $token;
    }

    private function makeClient(AuthProviderInterface $authProvider): Client
    {
        $client = new Client();
        $client->setAuthProvider($authProvider);
        return $client;
    }

    public function testTokenExchangedFiredAfterExchangeAuthorizationCode(): void
    {
        $token = $this->makeToken();
        $authProvider = $this->createMock(AuthProviderInterface::class);
        $authProvider->method('exchangeAuthorizationCode')->willReturn($token);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TokenExchanged::class));

        $client = $this->makeClient($authProvider);
        $client->setEventDispatcher($dispatcher);
        $client->setAuthorizationCode('code123');
        $client->exchangeAuthorizationCodeForToken();
    }

    public function testTokenRefreshedFiredAfterRefreshToken(): void
    {
        $token = $this->makeToken();
        $newToken = $this->makeToken();

        $authProvider = $this->createMock(AuthProviderInterface::class);
        $authProvider->method('refreshToken')->willReturn($newToken);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TokenRefreshed::class));

        $client = $this->makeClient($authProvider);
        $client->setEventDispatcher($dispatcher);
        $client->setToken($token);
        $client->refreshToken();
    }

    public function testTokenRevokedFiredAfterDestroyToken(): void
    {
        $token = $this->makeToken();
        $authProvider = $this->createMock(AuthProviderInterface::class);
        $authProvider->expects($this->once())->method('revokeToken')->with($token);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TokenRevoked::class));

        $client = $this->makeClient($authProvider);
        $client->setEventDispatcher($dispatcher);
        $client->destroyToken($token);
    }

    public function testTokenLoadedFromStorageFiredWhenTokenFound(): void
    {
        $token = $this->makeToken();
        $context = new TokenStorageContext('user_123');

        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('retrieveToken')->willReturn($token);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TokenLoadedFromStorage::class));

        $client = new Client();
        $client->setEventDispatcher($dispatcher);
        $client->setTokenStorage($storage);
        $client->setTokenStorageContext($context);
        $client->loadTokenFromStorage();
    }

    public function testTokenLoadedFromStorageNotFiredWhenTokenNull(): void
    {
        $context = new TokenStorageContext('user_123');

        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('retrieveToken')->willReturn(null);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->never())->method('dispatch');

        $client = new Client();
        $client->setEventDispatcher($dispatcher);
        $client->setTokenStorage($storage);
        $client->setTokenStorageContext($context);
        $client->loadTokenFromStorage();
    }

    public function testTokenSavedToStorageFiredAfterSave(): void
    {
        $token = $this->makeToken();
        $context = new TokenStorageContext('user_123');

        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->expects($this->once())->method('storeToken')->with($token, $context);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TokenSavedToStorage::class));

        $client = new Client();
        $client->setEventDispatcher($dispatcher);
        $client->setToken($token);
        $client->setTokenStorage($storage);
        $client->setTokenStorageContext($context);
        $client->saveTokenToStorage();
    }

    public function testEventsNotFiredWhenNoDispatcher(): void
    {
        $token = $this->makeToken();
        $authProvider = $this->createMock(AuthProviderInterface::class);
        $authProvider->method('revokeToken');

        $client = $this->makeClient($authProvider);
        $result = $client->destroyToken($token);

        $this->assertSame(['success' => true], $result);
    }
}
