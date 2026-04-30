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
}
