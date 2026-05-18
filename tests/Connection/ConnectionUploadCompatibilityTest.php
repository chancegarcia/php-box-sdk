<?php

declare(strict_types=1);

namespace Box\Tests\Connection;

use Box\Connection\Connection;
use Box\Exception\BoxException;
use Box\Http\FileStream;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Transport\TransportInterface;
use PHPUnit\Framework\TestCase;

class ConnectionUploadCompatibilityTest extends TestCase
{
    private function createMockResponse(): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        return $response;
    }

    public function testPostFileWithInvalidParentIdThrowsException(): void
    {
        $connection = new Connection();

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Invalid parent ID');

        $connection->postFile('http://example.com', __FILE__, '');
    }

    public function testPostFileWithEmptyFilePathThrowsException(): void
    {
        $connection = new Connection();

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('File path cannot be empty');

        $connection->postFile('http://example.com', '', 0);
    }

    public function testPostFileWithNonExistentFileThrowsException(): void
    {
        $connection = new Connection();

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('File does not exist');

        $connection->postFile('http://example.com', '/non/existent/path/to/file', 0);
    }

    public function testPostFileWithGuzzleTransportPassesResource(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $response = $this->createMockResponse();

        $connection = new Connection();
        $connection->setTransportName(Connection::TRANSPORT_GUZZLE);
        $connection->setTransport($transport);

        $transport->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://example.com',
                $this->callback(function ($options) {
                    $multipart = $options['multipart'];
                    $filePart = $multipart[0];
                    $parentPart = $multipart[1];

                    return $filePart['name'] === 'file' &&
                           is_resource($filePart['contents']) &&
                           $filePart['filename'] === basename(__FILE__) &&
                           $parentPart['name'] === 'parent_id' &&
                           $parentPart['contents'] === '0';
                })
            )
            ->willReturn($response);

        $result = $connection->postFile('http://example.com', __FILE__, 0);
        $this->assertSame($response, $result);
    }

    public function testPostFileWithFileStreamAndGuzzleTransport(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $response = $this->createMockResponse();

        $connection = new Connection();
        $connection->setTransportName(Connection::TRANSPORT_GUZZLE);
        $connection->setTransport($transport);

        $content = "test content";
        $filename = "test.txt";
        $stream = FileStream::fromString($content, $filename, "text/plain");

        $transport->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://example.com',
                $this->callback(function ($options) use ($filename) {
                    $multipart = $options['multipart'];
                    $filePart = $multipart[0];

                    return $filePart['name'] === 'file' &&
                           is_resource($filePart['contents']) &&
                           $filePart['filename'] === $filename;
                })
            )
            ->willReturn($response);

        $result = $connection->postFile('http://example.com', $stream, 0);
        $this->assertSame($response, $result);

        fclose($stream->getResource());
    }
}
