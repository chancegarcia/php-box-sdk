<?php

namespace Box\Tests\Exception;

use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use PHPUnit\Framework\TestCase;

class BoxResponseExceptionTest extends TestCase
{
    public function testExceptionParsesJsonBody(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'code' => 'item_name_in_use',
            'message' => 'An item with the same name already exists.',
            'context_info' => ['conflicts' => ['id' => '12345']]
        ]));
        $response->method('getHeaderLine')->willReturnMap([
            ['WWW-Authenticate', ''],
            ['Retry-After', '']
        ]);

        $e = new BoxResponseException("Error", 409, null, $response);
        $e->addContext(['conflicts' => ['id' => '12345']], 'item_context');

        $this->assertEquals('item_name_in_use', $e->getBoxCode());
        $this->assertStringContainsString('An item with the same name already exists.', $e->getErrorDescription());
        $this->assertEquals(['conflicts' => ['id' => '12345']], $e->getContext('item_context'));
    }

    public function testExceptionCombinesWwwAuthAndJsonBody(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'message' => 'Invalid token'
        ]));
        $response->method('getHeaderLine')->willReturnMap([
            ['WWW-Authenticate', 'Bearer error="invalid_token", error_description="The access token expired"'],
            ['Retry-After', '']
        ]);

        $e = new BoxResponseException("Error", 401, null, $response);

        $this->assertEquals('invalid_token', $e->getBoxCode());
        $this->assertStringContainsString('The access token expired', $e->getErrorDescription());
        $this->assertStringContainsString('Invalid token', $e->getErrorDescription());
    }
    public function testExceptionSanitizesSecretsInDescription(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'message' => 'Invalid token: Bearer abcdef1234567890'
        ]));
        $response->method('getHeaderLine')->willReturnMap([
            ['WWW-Authenticate', ''],
            ['Retry-After', '']
        ]);

        $e = new BoxResponseException("Error", 401, null, $response);

        $this->assertStringNotContainsString('abcdef1234567890', $e->getErrorDescription());
        $this->assertStringContainsString('abcd...7890', $e->getErrorDescription());
    }

    public function testExceptionSanitizesSecretsInContext(): void
    {
        $e = new BoxResponseException("Error", 400);
        $e->addContext(['access_token' => 'secret123', 'foo' => 'bar'], 'tokens');

        $context = $e->getContext('tokens');
        $this->assertEquals('********', $context['access_token']);
        $this->assertEquals('bar', $context['foo']);
    }
}
