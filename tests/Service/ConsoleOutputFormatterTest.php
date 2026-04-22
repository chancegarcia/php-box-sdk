<?php

namespace Box\Tests\Service;

use Box\Service\ConsoleOutputFormatter;
use PHPUnit\Framework\TestCase;

class ConsoleOutputFormatterTest extends TestCase
{
    public function testMaskSensitiveData(): void
    {
        $jsonFormatter = $this->createMock(\Box\Contract\JsonFormatterInterface::class);
        $formatter = new ConsoleOutputFormatter($jsonFormatter);
        $data = [
            'access_token' => 'sensitive_token_12345678',
            'normal_field' => 'normal_value',
            'nested' => [
                'refresh_token' => 'refresh_token_987654321',
            ]
        ];

        $masked = $formatter->maskSensitiveData($data);

        $this->assertNotEquals('sensitive_token_12345678', $masked['access_token']);
        $this->assertStringContainsString('...', $masked['access_token']);
        $this->assertEquals('normal_value', $masked['normal_field']);
        $this->assertStringContainsString('...', $masked['nested']['refresh_token']);
    }

    public function testMaskShortString(): void
    {
        $jsonFormatter = $this->createMock(\Box\Contract\JsonFormatterInterface::class);
        $formatter = new ConsoleOutputFormatter($jsonFormatter);
        $data = ['access_token' => 'short'];
        $masked = $formatter->maskSensitiveData($data);
        $this->assertEquals('********', $masked['access_token']);
    }
}
