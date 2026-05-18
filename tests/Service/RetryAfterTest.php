<?php

namespace Box\Tests\Service;

use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\Service;
use PHPUnit\Framework\TestCase;

class RetryAfterTest extends TestCase
{
    public function testRetryAfterNumeric(): void
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getStatusCode')->willReturn(429);
        $response->method('hasHeader')->with('Retry-After')->willReturn(true);
        $response->method('getHeaderLine')->willReturnMap([
            ['Retry-After', '30'],
            ['WWW-Authenticate', '']
        ]);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getContent')->willReturn('');
        $response->method('getRetryAfter')->willReturn(30);

        $service = $this->getMockForAbstractClass(Service::class);

        try {
            $service->handleBoxResponse($response);
            $this->fail("Expected BoxResponseException was not thrown.");
        } catch (BoxResponseException $e) {
            $this->assertEquals('30', $e->getContext('retry_after_header'));
            $this->assertEquals(30, $e->getContext('retry_after_seconds'));
        }
    }

    public function testRetryAfterDate(): void
    {
        $retryDate = gmdate('D, d M Y H:i:s \G\M\T', time() + 60);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getStatusCode')->willReturn(429);
        $response->method('hasHeader')->with('Retry-After')->willReturn(true);
        $response->method('getHeaderLine')->willReturnMap([
            ['Retry-After', $retryDate],
            ['WWW-Authenticate', '']
        ]);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getContent')->willReturn('');
        $response->method('getRetryAfter')->willReturn(60);

        $service = $this->getMockForAbstractClass(Service::class);

        try {
            $service->handleBoxResponse($response);
            $this->fail("Expected BoxResponseException was not thrown.");
        } catch (BoxResponseException $e) {
            $this->assertEquals($retryDate, $e->getContext('retry_after_header'));
            // Should be around 60 seconds
            $seconds = $e->getContext('retry_after_seconds');
            $this->assertGreaterThanOrEqual(58, $seconds);
            $this->assertLessThanOrEqual(60, $seconds);
        }
    }
}
