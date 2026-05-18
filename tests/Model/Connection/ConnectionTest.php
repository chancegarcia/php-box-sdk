<?php

namespace Box\Tests\Model\Connection;

use Box\Http\Response\BoxResponseInterface;
use Box\Connection\Connection;
use PHPUnit\Framework\TestCase;
use Box\Http\FileStream;

class ConnectionTest extends TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = new Connection();
    }

    public function testQuery()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['request'])
            ->getMock();
        $connection->expects($this->once())
            ->method('request')
            ->with('GET', 'http://example.com')
            ->willReturn($response);

        $result = $connection->query('http://example.com');
        $this->assertSame($response, $result);
    }

    public function testPost()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['request'])
            ->getMock();
        $connection->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', $this->callback(function ($options) {
                return $options['body'] === 'foo=bar';
            }))
            ->willReturn($response);

        $result = $connection->post('http://example.com', ['foo' => 'bar']);
        $this->assertSame($response, $result);
    }

    public function testPut()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['request'])
            ->getMock();
        $connection->expects($this->once())
            ->method('request')
            ->with('PUT', 'http://example.com', $this->callback(function ($options) {
                return $options['body'] === 'foo=bar';
            }))
            ->willReturn($response);

        $result = $connection->put('http://example.com', ['foo' => 'bar']);
        $this->assertSame($response, $result);
    }

    public function testPostFile()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['request'])
            ->getMock();

        $connection->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', $this->callback(function ($options) {
                return count($options['multipart']) === 2;
            }))
            ->willReturn($response);

        $result = $connection->postFile('http://example.com', __FILE__, 0);
        $this->assertSame($response, $result);
    }
    public function testPostFileWithParentId()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['request'])
            ->getMock();

        $parentId = '12345';
        $connection->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', $this->callback(function ($options) use ($parentId) {
                return $options['multipart'][1]['name'] === 'parent_id' && $options['multipart'][1]['contents'] === $parentId;
            }))
            ->willReturn($response);

        $result = $connection->postFile('http://example.com', __FILE__, $parentId);
        $this->assertSame($response, $result);
    }

    public function testPostFileWithFileStream()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['request'])
            ->getMock();

        $stream = FileStream::fromString("test content", "test.txt", "text/plain");

        $connection->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', $this->callback(function ($options) {
                // Check if multipart has file and parent_id
                return count($options['multipart']) === 2;
            }))
            ->willReturn($response);

        $result = $connection->postFile('http://example.com', $stream, 0);
        $this->assertSame($response, $result);
        fclose($stream->getResource());
    }
}
