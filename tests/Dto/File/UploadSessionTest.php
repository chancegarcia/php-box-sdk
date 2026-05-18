<?php

declare(strict_types=1);

namespace Box\Tests\Dto\File;

use Box\Dto\File\UploadSession;
use PHPUnit\Framework\TestCase;

class UploadSessionTest extends TestCase
{
    public function testConstructionAndProperties(): void
    {
        $session = new UploadSession(
            sessionId: 'F971964745A5CD0C001BBE4E58196BFD',
            uploadUrl: 'https://upload.box.com/api/2.0/files/upload-sessions/F971964745A5CD0C001BBE4E58196BFD',
            partSize: 8388608,
            sessionExpiresAt: '2026-05-15T10:53:43-08:00',
            totalParts: 14,
            numPartsProcessed: 0,
        );

        $this->assertSame('F971964745A5CD0C001BBE4E58196BFD', $session->sessionId);
        $this->assertSame('https://upload.box.com/api/2.0/files/upload-sessions/F971964745A5CD0C001BBE4E58196BFD', $session->uploadUrl);
        $this->assertSame(8388608, $session->partSize);
        $this->assertSame('2026-05-15T10:53:43-08:00', $session->sessionExpiresAt);
        $this->assertSame(14, $session->totalParts);
        $this->assertSame(0, $session->numPartsProcessed);
    }
}
