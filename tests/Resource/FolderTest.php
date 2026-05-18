<?php

namespace Box\Tests\Resource;

use Box\Resource\Folder;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    public function testIsEmpty()
    {
        $folder = new Folder();

        // Default should be empty if itemCollection is null
        $this->assertTrue($folder->isEmpty());

        // Empty array
        $folder->setItemCollection([]);
        $this->assertTrue($folder->isEmpty());

        // Non-empty array
        $folder->setItemCollection(['foo', 'bar']);
        $this->assertFalse($folder->isEmpty());

        // total_count = 0
        $folder->setItemCollection(['total_count' => 0, 'entries' => []]);
        $this->assertTrue($folder->isEmpty());

        // total_count > 0
        $folder->setItemCollection(['total_count' => 1, 'entries' => [['id' => '1']]]);
        $this->assertFalse($folder->isEmpty());
    }

    public function testGetIdDoesNotMutateState(): void
    {
        $folder = new Folder();
        $this->assertNull($folder->getId());
        // verify it stayed null
        $this->assertNull($folder->getId());
    }

    public function testExplicitRootId(): void
    {
        $folder = new Folder();
        $folder->setId(0);
        $this->assertEquals(0, $folder->getId());

        $folder2 = new Folder();
        $folder2->setId('0');
        $this->assertEquals('0', $folder2->getId());
    }
}
