<?php

declare(strict_types=1);

namespace Box\Tests\Storage\Token;

use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\Container\TokenStorageContainer;
use PHPUnit\Framework\TestCase;

class TokenStorageContractTest extends TestCase
{
    public function testContextCanonicalKey(): void
    {
        $context1 = new TokenStorageContext('user123', 'ent456', 'client789');
        $this->assertEquals('user:user123|enterprise:ent456|client:client789', $context1->getCanonicalKey());

        $context2 = new TokenStorageContext(null, null, 'client789');
        $this->assertEquals('user:none|enterprise:none|client:client789', $context2->getCanonicalKey());
    }

    public function testContextEquality(): void
    {
        $context1 = new TokenStorageContext('user123', 'ent456', 'client789');
        $context2 = new TokenStorageContext('user123', 'ent456', 'client789');
        $context3 = new TokenStorageContext('other', 'ent456', 'client789');

        $this->assertTrue($context1->equals($context2));
        $this->assertFalse($context1->equals($context3));
    }

    public function testContainerStorageBehavior(): void
    {
        $container = new TokenStorageContainer();
        $context = new TokenStorageContext('user123');
        $token = $this->createMock(TokenInterface::class);

        $this->assertNull($container->retrieveToken($context));

        $container->storeToken($token, $context);
        $this->assertSame($token, $container->retrieveToken($context));

        $newToken = $this->createMock(TokenInterface::class);
        $container->updateToken($newToken, $context);
        $this->assertSame($newToken, $container->retrieveToken($context));

        $container->removeToken($context);
        $this->assertNull($container->retrieveToken($context));
    }

    public function testMultipleContextsInContainer(): void
    {
        $container = new TokenStorageContainer();
        $context1 = new TokenStorageContext('user1');
        $context2 = new TokenStorageContext('user2');
        $token1 = $this->createMock(TokenInterface::class);
        $token2 = $this->createMock(TokenInterface::class);

        $container->storeToken($token1, $context1);
        $container->storeToken($token2, $context2);

        $this->assertSame($token1, $container->retrieveToken($context1));
        $this->assertSame($token2, $container->retrieveToken($context2));

        $container->clear();
        $this->assertNull($container->retrieveToken($context1));
        $this->assertNull($container->retrieveToken($context2));
    }

    public function testStoreTokenReplacesExistingForSameContext(): void
    {
        $container = new TokenStorageContainer();
        $context = new TokenStorageContext('user123');
        $token1 = $this->createMock(TokenInterface::class);
        $token2 = $this->createMock(TokenInterface::class);

        $container->storeToken($token1, $context);
        $this->assertSame($token1, $container->retrieveToken($context));

        $container->storeToken($token2, $context);
        $this->assertSame($token2, $container->retrieveToken($context));
    }

    public function testUpdateMissingContextStoresToken(): void
    {
        $container = new TokenStorageContainer();
        $context = new TokenStorageContext('user123');
        $token = $this->createMock(TokenInterface::class);

        $this->assertNull($container->retrieveToken($context));

        // If context is missing, update should still store it (standard behavior for many stores)
        $container->updateToken($token, $context);
        $this->assertSame($token, $container->retrieveToken($context));
    }

    public function testRemoveMissingContextIsSafe(): void
    {
        $container = new TokenStorageContainer();
        $context = new TokenStorageContext('user123');

        $this->assertNull($container->retrieveToken($context));
        $container->removeToken($context);
        $this->assertNull($container->retrieveToken($context));
    }

    public function testDistinctContextsIsolation(): void
    {
        $container = new TokenStorageContainer();

        $contexts = [
            'user-only' => new TokenStorageContext('user1'),
            'ent-only' => new TokenStorageContext(null, 'ent1'),
            'client-only' => new TokenStorageContext(null, null, 'client1'),
            'full' => new TokenStorageContext('user1', 'ent1', 'client1'),
        ];

        foreach ($contexts as $key => $context) {
            $token = $this->createMock(TokenInterface::class);
            $container->storeToken($token, $context);
            $this->assertSame($token, $container->retrieveToken($context), "Failed for $key");
        }

        // Verify removing one doesn't affect others
        $container->removeToken($contexts['user-only']);
        $this->assertNull($container->retrieveToken($contexts['user-only']));
        $this->assertNotNull($container->retrieveToken($contexts['ent-only']));
        $this->assertNotNull($container->retrieveToken($contexts['client-only']));
        $this->assertNotNull($container->retrieveToken($contexts['full']));
    }

    public function testEquivalentContextObjectsResolveToSameToken(): void
    {
        $container = new TokenStorageContainer();
        $context1 = new TokenStorageContext('user123', 'ent456', 'client789');
        $context2 = new TokenStorageContext('user123', 'ent456', 'client789');
        $token = $this->createMock(TokenInterface::class);

        $container->storeToken($token, $context1);
        $this->assertSame($token, $container->retrieveToken($context2));
    }
}
