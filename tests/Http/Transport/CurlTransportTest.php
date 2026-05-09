<?php

namespace Box\Tests\Http\Transport;

use Box\Connection\ConnectionInterface;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Transport\CurlTransport;
use PHPUnit\Framework\TestCase;

class CurlTransportTest extends TestCase
{
    public function testRequestMapsOptionsAndCallsConnection(): void
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $ch = curl_init();
        $response = $this->createMock(BoxResponseInterface::class);

        $connection->expects($this->once())
            ->method('initCurl')
            ->willReturn($ch);

        $connection->expects($this->once())
            ->method('initAdditionalCurlOpts')
            ->willReturnArgument(0);

        $connection->expects($this->once())
            ->method('getCurlData')
            ->with($ch)
            ->willReturn($response);

        $transport = new CurlTransport($connection);

        $options = [
            'query' => ['a' => 'b'],
            'headers' => [
                'X-Foo' => 'Bar',
                'X-Multi' => ['v1', 'v2']
            ],
            'body' => '{"foo":"bar"}'
        ];

        $result = $transport->request('PUT', 'http://example.com/test', $options);

        $this->assertSame($response, $result);

        // We can't easily verify curl_setopt calls on $ch without a proxy or extension like uopz/runkit
        // but we've verified the flow and that it uses Connection methods.
    }

    public function testMultipartMapping(): void
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $ch = curl_init();
        $response = $this->createMock(BoxResponseInterface::class);

        $connection->method('initCurl')->willReturn($ch);
        $connection->method('initAdditionalCurlOpts')->willReturnArgument(0);
        $connection->method('getCurlData')->willReturn($response);

        $transport = new CurlTransport($connection);

        $options = [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => 'data',
                    'filename' => 'test.txt'
                ],
                [
                    'name' => 'parent_id',
                    'contents' => '0'
                ]
            ]
        ];

        $result = $transport->request('POST', 'http://example.com/upload', $options);
        $this->assertSame($response, $result);
    }
}
