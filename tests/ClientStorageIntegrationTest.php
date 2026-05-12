<?php

declare(strict_types=1);

namespace Box\Tests;

use Box\Client;
use Box\Connection\Token\Token;
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\Container\TokenStorageContainer;
use Box\Connection\ConnectionInterface;
use Box\Http\Response\BoxResponseInterface;
use PHPUnit\Framework\TestCase;

class ClientStorageIntegrationTest extends TestCase
{
    private Client $client;
    private TokenStorageContainer $storage;
    private TokenStorageContext $context;

    protected function setUp(): void
    {
        $this->storage = new TokenStorageContainer();
        $this->context = new TokenStorageContext('user123', 'ent456', 'client789');
        $this->client = new Client();
    }

    public function testCanSetAndGetTokenStorage(): void
    {
        $this->client->setTokenStorage($this->storage);
        $this->assertSame($this->storage, $this->client->getTokenStorage());
    }

    public function testCanSetAndGetTokenStorageContext(): void
    {
        $this->client->setTokenStorageContext($this->context);
        $this->assertSame($this->context, $this->client->getTokenStorageContext());
    }

    public function testLoadTokenFromStorageSetsClientToken(): void
    {
        $token = new Token();
        $token->setAccessToken('loaded-token');
        $this->storage->storeToken($token, $this->context);

        $this->client->setTokenStorage($this->storage);
        $this->client->setTokenStorageContext($this->context);

        $loaded = $this->client->loadTokenFromStorage();

        $this->assertSame($token, $loaded);
        $this->assertEquals('loaded-token', $this->client->getToken()->getAccessToken());
    }

    public function testLoadTokenReturnsNullWhenNoTokenExists(): void
    {
        $this->client->setTokenStorage($this->storage);
        $this->client->setTokenStorageContext($this->context);

        $loaded = $this->client->loadTokenFromStorage();

        $this->assertNull($loaded);
    }

    public function testLoadTokenReturnsNullWhenStorageOrContextMissing(): void
    {
        $this->assertNull($this->client->loadTokenFromStorage());

        $this->client->setTokenStorage($this->storage);
        $this->assertNull($this->client->loadTokenFromStorage());

        $this->client->setTokenStorage(null);
        $this->client->setTokenStorageContext($this->context);
        $this->assertNull($this->client->loadTokenFromStorage());
    }

    public function testSaveTokenToStoragePersistsCurrentToken(): void
    {
        $token = new Token();
        $token->setAccessToken('current-token');
        $this->client->setToken($token);

        $this->client->setTokenStorage($this->storage);
        $this->client->setTokenStorageContext($this->context);

        $this->client->saveTokenToStorage();

        $stored = $this->storage->retrieveToken($this->context);
        $this->assertSame($token, $stored);
    }

    public function testSaveTokenToStorageCanUseExplicitTokenAndContext(): void
    {
        $token = new Token();
        $token->setAccessToken('explicit-token');
        $explicitContext = new TokenStorageContext('explicit-user');

        $this->client->setTokenStorage($this->storage);

        $this->client->saveTokenToStorage($token, $explicitContext);

        $stored = $this->storage->retrieveToken($explicitContext);
        $this->assertSame($token, $stored);
    }

    public function testRemoveTokenFromStorage(): void
    {
        $token = new Token();
        $this->storage->storeToken($token, $this->context);

        $this->client->setTokenStorage($this->storage);
        $this->client->setTokenStorageContext($this->context);

        $this->client->removeTokenFromStorage();

        $this->assertNull($this->storage->retrieveToken($this->context));
    }

    public function testGetAccessTokenPersistsToStorageWhenConfigured(): void
    {
        $mockResponse = $this->createMock(BoxResponseInterface::class);
        $mockResponse->method('json')->willReturn([
            'access_token' => 'new-access-token',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'refresh_token' => 'new-refresh-token'
        ]);
        $mockResponse->method('isSuccessful')->willReturn(true);

        $mockConnection = $this->createMock(ConnectionInterface::class);
        $mockConnection->method('post')->willReturn($mockResponse);

        $this->client->setConnection($mockConnection);
        $this->client->setTokenStorage($this->storage);
        $this->client->setTokenStorageContext($this->context);
        $this->client->setAuthorizationCode('some-code');

        $this->client->getAccessToken();

        $stored = $this->storage->retrieveToken($this->context);
        $this->assertNotNull($stored);
        $this->assertEquals('new-access-token', $stored->getAccessToken());
    }

    public function testRefreshTokenPersistsToStorageWhenConfigured(): void
    {
        $token = new Token();
        $token->setRefreshToken('old-refresh-token');
        $this->client->setToken($token);

        $mockResponse = $this->createMock(BoxResponseInterface::class);
        $mockResponse->method('json')->willReturn([
            'access_token' => 'refreshed-access-token',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'refresh_token' => 'refreshed-refresh-token'
        ]);
        $mockResponse->method('isSuccessful')->willReturn(true);

        $mockConnection = $this->createMock(ConnectionInterface::class);
        $mockConnection->method('post')->willReturn($mockResponse);

        $this->client->setConnection($mockConnection);
        $this->client->setTokenStorage($this->storage);
        $this->client->setTokenStorageContext($this->context);

        $this->client->refreshToken();

        $stored = $this->storage->retrieveToken($this->context);
        $this->assertNotNull($stored);
        $this->assertEquals('refreshed-access-token', $stored->getAccessToken());
    }
}
