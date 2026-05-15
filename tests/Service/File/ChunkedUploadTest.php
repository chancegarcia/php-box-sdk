<?php

declare(strict_types=1);

namespace Box\Tests\Service\File;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Dto\File\UploadPart;
use Box\Dto\File\UploadSession;
use Box\Event\File\UploadPartUploaded;
use Box\Event\File\UploadSessionAborted;
use Box\Event\File\UploadSessionCommitted;
use Box\Event\File\UploadSessionCreated;
use Box\Http\FileStream;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\File;
use Box\Service\File\FileService;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ChunkedUploadTest extends TestCase
{
    private function createService(ConnectionInterface $connection): FileService
    {
        $service = new FileService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    private function createMockResponse(array $data): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($data));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object) $data);
        return $response;
    }

    private function makeSession(
        string $sessionId = 'SESSION123',
        int $partSize = 100,
        int $totalParts = 1,
    ): UploadSession {
        return new UploadSession(
            sessionId: $sessionId,
            uploadUrl: FileService::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId,
            partSize: $partSize,
            sessionExpiresAt: '2026-05-15T10:53:43-08:00',
            totalParts: $totalParts,
            numPartsProcessed: 0,
        );
    }

    private function makePart(string $partId = '6F2D3486', int $offset = 0, int $size = 11): UploadPart
    {
        return new UploadPart(
            partId: $partId,
            offset: $offset,
            size: $size,
            sha1: '134b65991ed521fcfe4724b7d814ab8ded5185dc',
        );
    }

    /** @return FileService&MockObject */
    private function makePartialService(): FileService
    {
        return $this->getMockBuilder(FileService::class)
            ->onlyMethods(['createUploadSession', 'uploadPart', 'commitUploadSession', 'abortUploadSession'])
            ->getMock();
    }

    // ── Low-level API ─────────────────────────────────────────────────────────

    public function testCreateUploadSessionBuildsSessionFromResponse(): void
    {
        $sessionData = [
            'id' => 'F971964745A5CD0C001BBE4E58196BFD',
            'upload_url' => FileService::UPLOAD_SESSION_ENDPOINT . '/F971964745A5CD0C001BBE4E58196BFD',
            'part_size' => 8388608,
            'session_expires_at' => '2026-05-15T10:53:43-08:00',
            'total_parts' => 14,
            'num_parts_processed' => 0,
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                FileService::UPLOAD_SESSION_ENDPOINT,
                $this->callback(fn(array $o) =>
                    ($o['headers']['Content-Type'] ?? null) === 'application/json'
                    && str_contains($o['body'] ?? '', '"folder_id":"12345"')
                    && str_contains($o['body'] ?? '', '"file_size":50000000'))
            )
            ->willReturn($this->createMockResponse($sessionData));

        $result = $this->createService($connection)->createUploadSession('12345', 'video.mp4', 50000000);

        $this->assertInstanceOf(UploadSession::class, $result);
        $this->assertSame('F971964745A5CD0C001BBE4E58196BFD', $result->sessionId);
        $this->assertSame(8388608, $result->partSize);
        $this->assertSame(14, $result->totalParts);
        $this->assertSame(0, $result->numPartsProcessed);
    }

    public function testUploadPartSendsCorrectHeadersAndReturnsPart(): void
    {
        $sessionId = 'SESSION123';
        $chunk = 'hello world';
        $offset = 0;
        $totalSize = 11;
        $expectedDigest = 'sha=' . base64_encode(sha1($chunk, true));

        $partData = ['part' => ['part_id' => '6F2D3486', 'offset' => 0, 'size' => 11, 'sha1' => 'abc123']];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('request')
            ->with(
                'PUT',
                FileService::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId,
                $this->callback(fn(array $o) =>
                    $o['body'] === $chunk
                    && ($o['headers']['Content-Type'] ?? null) === 'application/octet-stream'
                    && ($o['headers']['Content-Range'] ?? null) === 'bytes 0-10/11'
                    && ($o['headers']['Digest'] ?? null) === $expectedDigest)
            )
            ->willReturn($this->createMockResponse($partData));

        $result = $this->createService($connection)->uploadPart($sessionId, $chunk, $offset, $totalSize);

        $this->assertInstanceOf(UploadPart::class, $result);
        $this->assertSame('6F2D3486', $result->partId);
        $this->assertSame(0, $result->offset);
        $this->assertSame(11, $result->size);
    }

    public function testListUploadSessionPartsReturnsMappedArray(): void
    {
        $sessionId = 'SESSION123';
        $listData = [
            'entries' => [
                ['part_id' => '6F2D3486', 'offset' => 0, 'size' => 8388608, 'sha1' => 'abc123'],
                ['part_id' => '7G3E4597', 'offset' => 8388608, 'size' => 4194304, 'sha1' => 'def456'],
            ],
            'total_count' => 2,
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(FileService::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId . '/parts')
            ->willReturn($this->createMockResponse($listData));

        $result = $this->createService($connection)->listUploadSessionParts($sessionId);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(UploadPart::class, $result);
        $this->assertSame('6F2D3486', $result[0]->partId);
        $this->assertSame('7G3E4597', $result[1]->partId);
        $this->assertSame(8388608, $result[1]->offset);
    }

    public function testCommitUploadSessionHydratesFile(): void
    {
        $sessionId = 'SESSION123';
        $fileSha1 = base64_encode(random_bytes(20));
        $commitData = ['entries' => [BoxApiFixtures::fileResponse()], 'total_count' => 1];
        $parts = [$this->makePart()];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                FileService::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId . '/commit',
                $this->callback(fn(array $o) =>
                    ($o['headers']['Digest'] ?? null) === 'sha=' . $fileSha1
                    && ($o['headers']['Content-Type'] ?? null) === 'application/json'
                    && str_contains($o['body'] ?? '', '6F2D3486'))
            )
            ->willReturn($this->createMockResponse($commitData));

        $result = $this->createService($connection)->commitUploadSession($sessionId, $parts, $fileSha1);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame('817696835', $result->getId());
    }

    public function testAbortUploadSessionCallsDelete(): void
    {
        $sessionId = 'SESSION123';

        $deleteResponse = $this->createMock(BoxResponseInterface::class);
        $deleteResponse->method('isSuccessful')->willReturn(true);
        $deleteResponse->method('json')->willReturn([]);
        $deleteResponse->method('getContent')->willReturn('');

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(FileService::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId)
            ->willReturn($deleteResponse);

        $this->createService($connection)->abortUploadSession($sessionId);
        $this->addToAssertionCount(1);
    }

    // ── Orchestrator ──────────────────────────────────────────────────────────

    public function testChunkedUploadHappyPathReturnsCommittedFile(): void
    {
        $content = 'hello world';
        $file = FileStream::fromString($content, 'test.txt');
        $session = $this->makeSession(partSize: 100);
        $part = $this->makePart(size: strlen($content));
        $expectedFile = new File();

        $service = $this->makePartialService();
        $service->expects($this->once())->method('createUploadSession')
            ->with('12345', 'test.txt', strlen($content))
            ->willReturn($session);
        $service->expects($this->once())->method('uploadPart')
            ->with($session->sessionId, $content, 0, strlen($content))
            ->willReturn($part);
        $service->expects($this->once())->method('commitUploadSession')
            ->willReturn($expectedFile);
        $service->expects($this->never())->method('abortUploadSession');

        $this->assertSame($expectedFile, $service->chunkedUpload($file, '12345'));
    }

    public function testChunkedUploadAcceptsStringPath(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'box_test_');
        file_put_contents($path, 'test content');

        $session = $this->makeSession();
        $expectedFile = new File();

        $service = $this->makePartialService();
        $service->method('createUploadSession')->willReturn($session);
        $service->method('uploadPart')->willReturn($this->makePart());
        $service->method('commitUploadSession')->willReturn($expectedFile);

        $result = $service->chunkedUpload($path, '12345');

        $this->assertSame($expectedFile, $result);
        unlink($path);
    }

    public function testChunkedUploadAbortsAndRethrowsOnFailure(): void
    {
        $file = FileStream::fromString('hello world', 'test.txt');
        $session = $this->makeSession();
        $error = new \RuntimeException('Part upload failed');

        $service = $this->makePartialService();
        $service->method('createUploadSession')->willReturn($session);
        $service->method('uploadPart')->willThrowException($error);
        $service->expects($this->once())->method('abortUploadSession')
            ->with($session->sessionId);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Part upload failed');
        $service->chunkedUpload($file, '12345');
    }

    public function testChunkedUploadDispatchesSessionAndPartAndCommitEvents(): void
    {
        $content = 'hello world';
        $file = FileStream::fromString($content, 'test.txt');
        $session = $this->makeSession();

        $service = $this->makePartialService();
        $service->method('createUploadSession')->willReturn($session);
        $service->method('uploadPart')->willReturn($this->makePart());
        $service->method('commitUploadSession')->willReturn(new File());

        $dispatched = [];
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnCallback(function (object $event) use (&$dispatched) {
            $dispatched[] = $event;
            return $event;
        });

        $service->setEventDispatcher($dispatcher);
        $service->chunkedUpload($file, '12345');

        $this->assertCount(3, $dispatched);
        $this->assertInstanceOf(UploadSessionCreated::class, $dispatched[0]);
        $this->assertInstanceOf(UploadPartUploaded::class, $dispatched[1]);
        $this->assertInstanceOf(UploadSessionCommitted::class, $dispatched[2]);
        $this->assertSame($session, $dispatched[0]->session);
        $this->assertSame(1, $dispatched[1]->partNumber);
        $this->assertSame($session->totalParts, $dispatched[1]->totalParts);
    }

    public function testChunkedUploadDispatchesAbortedEventOnFailure(): void
    {
        $file = FileStream::fromString('hello world', 'test.txt');
        $session = $this->makeSession();

        $service = $this->makePartialService();
        $service->method('createUploadSession')->willReturn($session);
        $service->method('uploadPart')->willThrowException(new \RuntimeException('oops'));
        $service->method('abortUploadSession');


        $dispatched = [];
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnCallback(function (object $event) use (&$dispatched) {
            $dispatched[] = $event;
            return $event;
        });

        $service->setEventDispatcher($dispatcher);

        try {
            $service->chunkedUpload($file, '12345');
            $this->fail('Expected exception not thrown');
        } catch (\RuntimeException) {
            // expected
        }

        $this->assertCount(2, $dispatched);
        $this->assertInstanceOf(UploadSessionCreated::class, $dispatched[0]);
        $this->assertInstanceOf(UploadSessionAborted::class, $dispatched[1]);
        $this->assertSame($session->sessionId, $dispatched[1]->sessionId);
        $this->assertInstanceOf(\RuntimeException::class, $dispatched[1]->error);
    }
}
