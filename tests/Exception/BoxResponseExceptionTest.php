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
        $response->method('getHeaderLine')->with('WWW-Authenticate')->willReturn('');
        
        $e = new BoxResponseException("Error", 409, null, $response);
        
        $this->assertEquals('item_name_in_use', $e->getBoxCode());
        $this->assertStringContainsString('An item with the same name already exists.', $e->getErrorDescription());
        $this->assertEquals(['conflicts' => ['id' => '12345']], $e->getContext());
    }

    public function testExceptionCombinesWwwAuthAndJsonBody(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'message' => 'Invalid token'
        ]));
        $response->method('getHeaderLine')->with('WWW-Authenticate')->willReturn('Bearer error="invalid_token", error_description="The access token expired"');
        
        $e = new BoxResponseException("Error", 401, null, $response);
        
        $this->assertEquals('invalid_token', $e->getBoxCode());
        $this->assertStringContainsString('The access token expired', $e->getErrorDescription());
        $this->assertStringContainsString('Invalid token', $e->getErrorDescription());
    }
}
