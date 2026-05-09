<?php

namespace Box\Tests\Http\Util;

use Box\Http\Util\Redactor;
use PHPUnit\Framework\TestCase;

class RedactorTest extends TestCase
{
    private Redactor $redactor;

    protected function setUp(): void
    {
        $this->redactor = new Redactor();
    }

    public function testRedactHeaders(): void
    {
        $headers = [
            'Authorization' => ['Bearer secret-token'],
            'X-Custom' => ['normal-value'],
            'Set-Cookie' => ['session=secret; path=/'],
        ];

        $redacted = $this->redactor->redactHeaders($headers);

        $this->assertEquals(['[REDACTED]'], $redacted['Authorization']);
        $this->assertEquals(['normal-value'], $redacted['X-Custom']);
        $this->assertEquals(['session=[REDACTED]; path=/'], $redacted['Set-Cookie']);
    }

    public function testRedactArray(): void
    {
        $data = [
            'access_token' => 'secret',
            'refresh_token' => 'secret',
            'client_id' => 'public',
            'nested' => [
                'code' => 'auth-code',
                'other' => 'safe'
            ],
            'safe_key' => 'safe_value'
        ];

        $redacted = $this->redactor->redactArray($data);

        $this->assertEquals('[REDACTED]', $redacted['access_token']);
        $this->assertEquals('[REDACTED]', $redacted['refresh_token']);
        $this->assertEquals('[REDACTED]', $redacted['client_id']);
        $this->assertEquals('[REDACTED]', $redacted['nested']['code']);
        $this->assertEquals('safe', $redacted['nested']['other']);
        $this->assertEquals('safe_value', $redacted['safe_key']);
    }

    public function testRedactString(): void
    {
        $this->assertEquals('Bearer [REDACTED]', $this->redactor->redactString('Bearer my-token'));
        $this->assertEquals('access_token=[REDACTED]', $this->redactor->redactString('access_token=my-token'));
        $this->assertEquals('"access_token": "[REDACTED]"', $this->redactor->redactString('"access_token": "my-token"'));
        $this->assertEquals('safe string', $this->redactor->redactString('safe string'));
    }

    public function testRedactArrayWithNonStringValues(): void
    {
        $data = [
            'access_token' => 123,
            'nested' => [
                'code' => null,
                'bool' => true
            ]
        ];

        $redacted = $this->redactor->redactArray($data);

        $this->assertEquals('[REDACTED]', $redacted['access_token']);
        $this->assertEquals('[REDACTED]', $redacted['nested']['code']);
        $this->assertTrue($redacted['nested']['bool']);
    }
}
