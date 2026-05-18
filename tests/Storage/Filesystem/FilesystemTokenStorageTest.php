<?php

declare(strict_types=1);

namespace Box\Tests\Storage\Filesystem;

use Box\Connection\Token\Token;
use Box\Dto\TokenStorageContext;
use Box\Exception\TokenStorageException;
use Box\Storage\Token\Filesystem\FilesystemTokenStorage;
use PHPUnit\Framework\TestCase;

class FilesystemTokenStorageTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'box_fs_token_');
        // Remove so storage starts fresh (no pre-existing file)
        @unlink($this->tempFile);
    }

    protected function tearDown(): void
    {
        @unlink($this->tempFile);
    }

    private function makeToken(string $access, string $refresh = 'refresh_placeholder'): Token
    {
        $token = new Token();
        $token->setAccessToken($access);
        $token->setRefreshToken($refresh);
        $token->setGrantType('authorization_code');
        return $token;
    }

    public function testStoreAndRetrieveToken(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_abc', null, 'client_xyz');
        $token = $this->makeToken('access_token_abc123', 'refresh_token_def456');

        $storage->storeToken($token, $context);

        $retrieved = $storage->retrieveToken($context);
        $this->assertNotNull($retrieved);
        $this->assertEquals('access_token_abc123', $retrieved->getAccessToken());
        $this->assertEquals('refresh_token_def456', $retrieved->getRefreshToken());
    }

    public function testStoreOverwritesExistingContextToken(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_abc');
        $tokenA = $this->makeToken('access_first');
        $tokenB = $this->makeToken('access_second');

        $storage->storeToken($tokenA, $context);
        $storage->storeToken($tokenB, $context);

        $retrieved = $storage->retrieveToken($context);
        $this->assertNotNull($retrieved);
        $this->assertEquals('access_second', $retrieved->getAccessToken());
    }

    public function testRetrieveReturnsNullForMissingContext(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_nonexistent');

        $this->assertNull($storage->retrieveToken($context));
    }

    public function testUpdateTokenUpserts(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_new');
        $token = $this->makeToken('access_upsert_test');

        // No prior entry — updateToken should store it
        $storage->updateToken($token, $context);

        $retrieved = $storage->retrieveToken($context);
        $this->assertNotNull($retrieved);
        $this->assertEquals('access_upsert_test', $retrieved->getAccessToken());
    }

    public function testRemoveToken(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_remove_me');
        $token = $this->makeToken('access_to_remove');

        $storage->storeToken($token, $context);
        $this->assertNotNull($storage->retrieveToken($context));

        $storage->removeToken($context);
        $this->assertNull($storage->retrieveToken($context));
    }

    public function testRemoveMissingContextIsSafe(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_never_stored');

        // Should not throw
        $storage->removeToken($context);
        $this->assertNull($storage->retrieveToken($context));
    }

    public function testClearEmptiesAllTokens(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $ctx1 = new TokenStorageContext('user_one');
        $ctx2 = new TokenStorageContext('user_two');

        $storage->storeToken($this->makeToken('access_one'), $ctx1);
        $storage->storeToken($this->makeToken('access_two'), $ctx2);

        $storage->clear();

        $this->assertNull($storage->retrieveToken($ctx1));
        $this->assertNull($storage->retrieveToken($ctx2));
    }

    public function testMultipleContextsIsolated(): void
    {
        $storage = new FilesystemTokenStorage($this->tempFile);
        $ctx1 = new TokenStorageContext('user_alpha', null, 'client_one');
        $ctx2 = new TokenStorageContext('user_beta', null, 'client_two');

        $storage->storeToken($this->makeToken('access_alpha'), $ctx1);
        $storage->storeToken($this->makeToken('access_beta'), $ctx2);

        $this->assertEquals('access_alpha', $storage->retrieveToken($ctx1)->getAccessToken());
        $this->assertEquals('access_beta', $storage->retrieveToken($ctx2)->getAccessToken());
    }

    public function testInvalidJsonThrowsException(): void
    {
        file_put_contents($this->tempFile, '{not valid json{{');

        $storage = new FilesystemTokenStorage($this->tempFile);
        $context = new TokenStorageContext('user_any');

        $this->expectException(TokenStorageException::class);
        $storage->retrieveToken($context);
    }

    public function testPersistenceAcrossInstances(): void
    {
        $ctx = new TokenStorageContext('user_persist', 'ent_persist', 'client_persist');
        $token = $this->makeToken('access_persistent_token');

        $instance1 = new FilesystemTokenStorage($this->tempFile);
        $instance1->storeToken($token, $ctx);

        $instance2 = new FilesystemTokenStorage($this->tempFile);
        $retrieved = $instance2->retrieveToken($ctx);

        $this->assertNotNull($retrieved);
        $this->assertEquals('access_persistent_token', $retrieved->getAccessToken());
    }
}
