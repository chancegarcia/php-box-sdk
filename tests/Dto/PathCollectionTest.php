<?php

declare(strict_types=1);

namespace Box\Tests\Dto;

use Box\Dto\PathCollection;
use Box\Resource\Folder;
use PHPUnit\Framework\TestCase;

class PathCollectionTest extends TestCase
{
    public function testConstructionAndPropertyAccess(): void
    {
        $folder = new Folder();
        $folder->setId('0');
        $folder->setName('All Files');

        $pathCollection = new PathCollection(1, [$folder]);

        $this->assertSame(1, $pathCollection->totalCount);
        $this->assertCount(1, $pathCollection->entries);
        $this->assertSame($folder, $pathCollection->entries[0]);
    }

    public function testEmptyEntries(): void
    {
        $pathCollection = new PathCollection(0, []);

        $this->assertSame(0, $pathCollection->totalCount);
        $this->assertSame([], $pathCollection->entries);
    }
}
