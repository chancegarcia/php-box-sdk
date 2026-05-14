<?php

namespace Box\Tests\Service;

use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\Service;
use PHPUnit\Framework\TestCase;
use stdClass;

class ServiceResponseHandlingTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = $this->getMockForAbstractClass(Service::class);
    }

    public function testHandleBoxResponseDecoded(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $content = '{"id": "123", "name": "test"}';
        $response->method('getContent')->willReturn($content);
        $response->method('json')->willReturnCallback(function ($assoc) use ($content) {
            return json_decode($content, $assoc);
        });

        $result = $this->service->handleBoxResponse($response, 'decoded');

        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertEquals('123', $result->id);
        $this->assertEquals('test', $result->name);
    }

    public function testHandleBoxResponseFlat(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $content = '{"id": "123", "name": "test"}';
        $response->method('getContent')->willReturn($content);
        $response->method('json')->willReturnCallback(function ($assoc) use ($content) {
            return json_decode($content, $assoc);
        });

        $result = $this->service->handleBoxResponse($response, 'flat');

        $this->assertIsArray($result);
        $this->assertEquals('123', $result['id']);
        $this->assertEquals('test', $result['name']);
    }

    public function testHandleBoxResponseOriginal(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $json = '{"id": "123", "name": "test"}';
        $response->method('getContent')->willReturn($json);
        $response->method('json')->willReturnCallback(function ($assoc) use ($json) {
            return json_decode($json, $assoc);
        });

        $result = $this->service->handleBoxResponse($response, 'original');

        $this->assertEquals($json, $result);
    }

    public function testHandleBoxResponseUnsuccessful(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getStatusCode')->willReturn(404);
        $response->method('getContent')->willReturn('{"error": "not_found", "error_description": "Resource not found"}');

        $this->expectException(BoxResponseException::class);
        $this->expectExceptionCode(404);

        $this->service->handleBoxResponse($response);
    }

    public function testHandleBoxResponseInvalidJson(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('getContent')->willReturn('invalid json');

        $this->expectException(\Box\Exception\BoxException::class);
        $this->expectExceptionMessage('sdk_json_decode');

        $this->service->handleBoxResponse($response);
    }

    public function testHandleBoxResponseEmptyBody(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('getContent')->willReturn('');
        $response->method('json')->willReturn(null);

        $result = $this->service->handleBoxResponse($response, 'decoded');
        $this->assertNull($result);
    }

    public function testHandleBoxResponseInvalidResponseObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of type ?' . BoxResponseInterface::class);

        $this->service->handleBoxResponse(new stdClass());
    }

    public function testHandleBoxResponseExplicitNull(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('expecting instance of ' . BoxResponseInterface::class);

        $this->service->handleBoxResponse(null);
    }
}
