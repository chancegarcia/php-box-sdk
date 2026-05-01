<?php

namespace Box\Tests\Connection\Token;

use Box\Connection\Token\Token;
use Box\Mapper\Hydrator;
use PHPUnit\Framework\TestCase;

class TokenHydrationTest extends TestCase
{
    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    public function testHydrationOfReceivedAt()
    {
        $data = [
            'access_token' => 'abc',
            'expires_in' => 3600,
            'received_at' => 1234567890
        ];

        $token = new Token();

        $this->hydrator->hydrate($token, $data);

        $this->assertEquals('abc', $token->getAccessToken());
        $this->assertEquals(3600, $token->getExpiresIn());
        $this->assertEquals(1234567890, $token->getReceivedAt());
    }

    public function testHydrationWithoutReceivedAtDefaultsToTime()
    {
        $startTime = time();
        $data = [
            'access_token' => 'abc',
            'expires_in' => 3600
        ];

        $token = new Token();
        $this->hydrator->hydrate($token, $data);
        $endTime = time();

        $this->assertGreaterThanOrEqual($startTime, $token->getReceivedAt());
        $this->assertLessThanOrEqual($endTime, $token->getReceivedAt());
    }

    public function testIsExpiredWithPersistedData()
    {
        $hydrator = new Hydrator();

        // Expired token
        $data = [
            'expires_in' => 3600,
            'received_at' => time() - 3601
        ];
        $token = new Token();
        $hydrator->hydrate($token, $data);
        $this->assertTrue($token->isExpired());

        // Not expired token
        $data = [
            'expires_in' => 3600,
            'received_at' => time() - 3599
        ];
        $token = new Token();
        $hydrator->hydrate($token, $data);
        $this->assertFalse($token->isExpired());
    }
}
