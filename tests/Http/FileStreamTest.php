<?php

namespace Box\Tests\Http;

use Box\Http\FileStream;
use PHPUnit\Framework\TestCase;

class FileStreamTest extends TestCase
{
    public function testFromPath()
    {
        $path = __FILE__;
        $stream = FileStream::fromPath($path);

        $this->assertTrue(is_resource($stream->getResource()));
        $this->assertEquals(basename($path), $stream->getFilename());
        $this->assertNull($stream->getMimeType());

        fclose($stream->getResource());
    }

    public function testFromString()
    {
        $content = "hello world";
        $filename = "test.txt";
        $mimeType = "text/plain";
        $stream = FileStream::fromString($content, $filename, $mimeType);

        $this->assertTrue(is_resource($stream->getResource()));
        $this->assertEquals($filename, $stream->getFilename());
        $this->assertEquals($mimeType, $stream->getMimeType());
        $this->assertEquals($content, stream_get_contents($stream->getResource()));

        fclose($stream->getResource());
    }

    public function testConstructorThrowsOnInvalidResource()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FileStream('not a resource', 'test.txt');
    }

    public function testGetSizeReturnsContentLength(): void
    {
        $content = 'hello world';
        $stream = FileStream::fromString($content, 'test.txt');

        $this->assertSame(strlen($content), $stream->getSize());

        fclose($stream->getResource());
    }

    public function testReadChunkReadsRequestedBytes(): void
    {
        $stream = FileStream::fromString('hello world', 'test.txt');

        $this->assertSame('hello', $stream->readChunk(5));
        $this->assertSame(' worl', $stream->readChunk(5));

        fclose($stream->getResource());
    }

    public function testIsEofReturnsTrueAfterFullRead(): void
    {
        $stream = FileStream::fromString('hi', 'test.txt');

        $this->assertFalse($stream->isEof());
        $stream->readChunk(100);
        $this->assertTrue($stream->isEof());

        fclose($stream->getResource());
    }
}
