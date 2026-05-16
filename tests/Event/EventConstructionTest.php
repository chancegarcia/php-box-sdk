<?php

declare(strict_types=1);

namespace Box\Tests\Event;

use Box\Connection\Token\TokenInterface;
use Box\Event\Auth\JwtTokenGenerated;
use Box\Event\Auth\TokenExchanged;
use Box\Event\Auth\TokenLoadedFromStorage;
use Box\Event\Auth\TokenRefreshed;
use Box\Event\Auth\TokenRevoked;
use Box\Event\Auth\TokenSavedToStorage;
use Box\Event\File\FileUploaded;
use Box\Event\Http\RateLimitHit;
use Box\Resource\File;
use PHPUnit\Framework\TestCase;

class EventConstructionTest extends TestCase
{
    public function testTokenExchangedHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $event = new TokenExchanged($token);
        $this->assertSame($token, $event->token);
    }

    public function testTokenRefreshedHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $event = new TokenRefreshed($token);
        $this->assertSame($token, $event->token);
    }

    public function testTokenRevokedHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $event = new TokenRevoked($token);
        $this->assertSame($token, $event->token);
    }

    public function testTokenLoadedFromStorageHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $event = new TokenLoadedFromStorage($token);
        $this->assertSame($token, $event->token);
    }

    public function testTokenSavedToStorageHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $event = new TokenSavedToStorage($token);
        $this->assertSame($token, $event->token);
    }

    public function testJwtTokenGeneratedHoldsToken(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $event = new JwtTokenGenerated($token);
        $this->assertSame($token, $event->token);
    }

    public function testFileUploadedHoldsFile(): void
    {
        $file = new File();
        $event = new FileUploaded($file);
        $this->assertSame($file, $event->file);
    }

    public function testRateLimitHitHoldsRetryAfter(): void
    {
        $event = new RateLimitHit(30);
        $this->assertSame(30, $event->retryAfter);
    }

    public function testRateLimitHitAllowsZero(): void
    {
        $event = new RateLimitHit(0);
        $this->assertSame(0, $event->retryAfter);
    }
}
