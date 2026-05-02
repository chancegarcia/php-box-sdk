<?php

namespace Box\Tests\Connection\Token;

use Box\Connection\Token\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testIsExpiredExists(): void
    {
        $token = new Token();
        $this->assertTrue(method_exists($token, 'isExpired'), 'Method isExpired should exist');
    }

    public function testExpirationLogic(): void
    {
        $token = new Token();

        // No expires_in or received_at should not be expired
        $this->assertFalse($token->isExpired());

        // Set expires_in, received_at should be auto-set
        $token->setExpiresIn(3600);
        $this->assertNotNull($token->getReceivedAt());
        $this->assertFalse($token->isExpired());
    }

    public function testIsExpiredReturnsTrueWhenExpired(): void
    {
        $token = new class extends Token {
            public function forceReceivedAt(int $time): void
            {
                $reflection = new \ReflectionProperty(parent::class, 'receivedAt');
                $reflection->setValue($this, $time);
            }
        };

        $token->setExpiresIn(3600);
        $token->forceReceivedAt(time() - 3601);
        $this->assertTrue($token->isExpired());
    }

    public function testIsExpiredReturnsFalseWhenNotExpired(): void
    {
        $token = new class extends Token {
            public function forceReceivedAt(int $time): void
            {
                $reflection = new \ReflectionProperty(parent::class, 'receivedAt');
                $reflection->setValue($this, $time);
            }
        };

        $token->setExpiresIn(3600);
        $token->forceReceivedAt(time() - 3599);
        $this->assertFalse($token->isExpired());
    }

    public function testSetExpiresInUpdatesReceivedAt(): void
    {
        $token = new Token();
        $token->setExpiresIn(3600);
        $firstReceivedAt = $token->getReceivedAt();

        sleep(1);

        $token->setExpiresIn(3600);
        $secondReceivedAt = $token->getReceivedAt();

        $this->assertEquals($firstReceivedAt, $secondReceivedAt);
    }
}
