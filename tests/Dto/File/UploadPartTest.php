<?php

declare(strict_types=1);

namespace Box\Tests\Dto\File;

use Box\Dto\File\UploadPart;
use PHPUnit\Framework\TestCase;

class UploadPartTest extends TestCase
{
    public function testConstructionAndProperties(): void
    {
        $part = new UploadPart(
            partId: '6F2D3486',
            offset: 0,
            size: 8388608,
            sha1: '134b65991ed521fcfe4724b7d814ab8ded5185dc',
        );

        $this->assertSame('6F2D3486', $part->partId);
        $this->assertSame(0, $part->offset);
        $this->assertSame(8388608, $part->size);
        $this->assertSame('134b65991ed521fcfe4724b7d814ab8ded5185dc', $part->sha1);
    }
}
