<?php

declare(strict_types=1);

namespace Box\Tests\Storage\Pdo;

use Box\Connection\Token\Token;
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\Pdo\TokenStorage;
use PHPUnit\Framework\TestCase;
use PDO;

class TokenStorageTest extends TestCase
{
    private ?PDO $pdo = null;
    private ?TokenStorage $storage = null;
    private string $tableName = 'test_tokens';

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->createSchema();

        $this->storage = new TokenStorage(null, null, null, [], $this->pdo);
        $this->storage->setTokenTableName($this->tableName);
    }

    private function createSchema(): void
    {
        $sql = sprintf(
            "CREATE TABLE %s (
                user_id TEXT,
                enterprise_id TEXT,
                client_id TEXT,
                access_token TEXT,
                refresh_token TEXT,
                grant_type TEXT,
                expires_in INTEGER,
                token_type TEXT
            )",
            $this->tableName
        );
        $this->pdo->exec($sql);
    }

    public function testRetrieveMissingTokenReturnsNull(): void
    {
        $context = new TokenStorageContext('user123');
        $this->assertNull($this->storage->retrieveToken($context));
    }

    public function testStoreAndRetrieveToken(): void
    {
        $context = new TokenStorageContext('user1', 'ent1', 'client1');
        $token = new Token([
            'access_token' => 'access-123',
            'refresh_token' => 'refresh-456',
            'expires_in' => 3600,
            'token_type' => 'bearer'
        ]);

        $this->storage->storeToken($token, $context);

        $retrieved = $this->storage->retrieveToken($context);

        $this->assertNotNull($retrieved);
        $this->assertEquals('access-123', $retrieved->getAccessToken());
        $this->assertEquals('refresh-456', $retrieved->getRefreshToken());
        $this->assertEquals(3600, $retrieved->getExpiresIn());
        $this->assertEquals('bearer', $retrieved->getTokenType());
    }

    public function testStoreAnotherTokenForSameContextReplacesActiveToken(): void
    {
        $context = new TokenStorageContext('user1');
        $token1 = new Token(['access_token' => 'token-1']);
        $token2 = new Token(['access_token' => 'token-2']);

        $this->storage->storeToken($token1, $context);
        $this->storage->storeToken($token2, $context);

        $retrieved = $this->storage->retrieveToken($context);
        $this->assertEquals('token-2', $retrieved->getAccessToken());

        // Verify only one row exists
        $stmt = $this->pdo->query(sprintf("SELECT COUNT(*) FROM %s", $this->tableName));
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testIsolationAcrossDifferentContexts(): void
    {
        $context1 = new TokenStorageContext('user1');
        $context2 = new TokenStorageContext('user2');

        $token1 = new Token(['access_token' => 'token-1']);
        $token2 = new Token(['access_token' => 'token-2']);

        $this->storage->storeToken($token1, $context1);
        $this->storage->storeToken($token2, $context2);

        $this->assertEquals('token-1', $this->storage->retrieveToken($context1)->getAccessToken());
        $this->assertEquals('token-2', $this->storage->retrieveToken($context2)->getAccessToken());
    }

    public function testEquivalentContextObjectsResolveToSameRow(): void
    {
        $context1 = new TokenStorageContext('user1', 'ent1', 'client1');
        $context2 = new TokenStorageContext('user1', 'ent1', 'client1');

        $token = new Token(['access_token' => 'token-abc']);
        $this->storage->storeToken($token, $context1);

        $retrieved = $this->storage->retrieveToken($context2);
        $this->assertNotNull($retrieved);
        $this->assertEquals('token-abc', $retrieved->getAccessToken());
    }

    public function testRemoveToken(): void
    {
        $context = new TokenStorageContext('user1');
        $token = new Token(['access_token' => 'token-1']);

        $this->storage->storeToken($token, $context);
        $this->assertNotNull($this->storage->retrieveToken($context));

        $this->storage->removeToken($context);
        $this->assertNull($this->storage->retrieveToken($context));
    }

    public function testRemoveMissingContextIsSafe(): void
    {
        $context = new TokenStorageContext('non-existent');
        $this->storage->removeToken($context);
        $this->assertTrue(true); // Should not throw exception
    }

    public function testClearStorage(): void
    {
        $this->storage->storeToken(new Token(['access_token' => 't1']), new TokenStorageContext('u1'));
        $this->storage->storeToken(new Token(['access_token' => 't2']), new TokenStorageContext('u2'));

        $this->storage->clear();

        $this->assertNull($this->storage->retrieveToken(new TokenStorageContext('u1')));
        $this->assertNull($this->storage->retrieveToken(new TokenStorageContext('u2')));
    }

    public function testNullableContextFields(): void
    {
        // Only enterpriseId
        $context1 = new TokenStorageContext(null, 'ent1', null);
        $token1 = new Token(['access_token' => 'token-ent']);
        $this->storage->storeToken($token1, $context1);

        // Only clientId
        $context2 = new TokenStorageContext(null, null, 'client1');
        $token2 = new Token(['access_token' => 'token-client']);
        $this->storage->storeToken($token2, $context2);

        $this->assertEquals('token-ent', $this->storage->retrieveToken($context1)->getAccessToken());
        $this->assertEquals('token-client', $this->storage->retrieveToken($context2)->getAccessToken());

        // Verify no collision with full null context (if supported, though usually at least one field should be set)
        $context3 = new TokenStorageContext(null, null, null);
        $token3 = new Token(['access_token' => 'token-null']);
        $this->storage->storeToken($token3, $context3);

        $this->assertEquals('token-null', $this->storage->retrieveToken($context3)->getAccessToken());
    }
}
