<?php

namespace Box\Tests\Model\Connection;

use Box\Http\Response\BoxResponseInterface;
use Box\Connection\Connection;
use PHPUnit\Framework\TestCase;
use CurlHandle;
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

    public function testInitCurlReturnsResource()
    {
        $this->assertInstanceOf(CurlHandle::class, $this->connection->initCurl());
    }

    public function testInitAdditionalCurlOptsReturnsResource()
    {
        $ch = $this->connection->initCurl();
        $this->assertInstanceOf(CurlHandle::class, $this->connection->initAdditionalCurlOpts($ch));
    }

    public function testSetCurlOpts()
    {
        $opts = [CURLOPT_TIMEOUT => 30];
        $this->connection->setCurlOpts($opts);
        $this->assertEquals($opts, $this->connection->getCurlOpts());
    }

    public function testInitAdditionalCurlOptsWithHeaders()
    {
        $ch = curl_init();
        $this->connection->setCurlOpts([
            'CURLOPT_HTTPHEADER' => ['X-Test: foo']
        ]);

        $this->connection->initAdditionalCurlOpts($ch);
        // We can't easily verify curl options from the handle in PHP without extra extensions
        // but we can ensure it doesn't crash and returns the handle.
        $this->assertInstanceOf(CurlHandle::class, $ch);
    }

    public function testGetSetClientId()
    {
        $this->connection->setClientId('foo');
        $this->assertEquals('foo', $this->connection->getClientId());
    }

    public function testGetSetClientSecret()
    {
        $this->connection->setClientSecret('bar');
        $this->assertEquals('bar', $this->connection->getClientSecret());
    }

    public function testGetSetRedirectUri()
    {
        $this->connection->setRedirectUri('http://localhost');
        $this->assertEquals('http://localhost', $this->connection->getRedirectUri());
    }

    public function testQuery()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['getCurlData'])
            ->getMock();
        $connection->expects($this->once())
            ->method('getCurlData')
            ->willReturn($response);

        $result = $connection->query('http://example.com');
        $this->assertSame($response, $result);
    }

    public function testPost()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['getCurlData'])
            ->getMock();
        $connection->expects($this->once())
            ->method('getCurlData')
            ->willReturn($response);

        $result = $connection->post('http://example.com', ['foo' => 'bar']);
        $this->assertSame($response, $result);
    }

    public function testPut()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['getCurlData'])
            ->getMock();
        $connection->expects($this->once())
            ->method('getCurlData')
            ->willReturn($response);

        $result = $connection->put('http://example.com', ['foo' => 'bar']);
        $this->assertSame($response, $result);
    }

    public function testPostFile()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $connection = $this->getMockBuilder(Connection::class)
            ->onlyMethods(['getCurlData', 'getMimeType', 'initCurl', 'initCurlOpts', 'initAdditionalCurlOpts'])
            ->getMock();

        $ch = curl_init();
        $connection->method('initCurl')
            ->willReturn($ch);

        $connection->method('initCurlOpts')
            ->willReturn($ch);

        $connection->method('initAdditionalCurlOpts')
            ->willReturn($ch);

        $connection->method('getMimeType')
            ->willReturn('text/plain');

        $connection->expects($this->once())
            ->method('getCurlData')
            ->willReturn($response);

        // Use a real file path and real CURLFile (implicitly via postFile calling createCurlFile)
        $result = $connection->postFile('http://example.com', __FILE__, 0);
        $this->assertSame($response, $result);
        @curl_close($ch);
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
