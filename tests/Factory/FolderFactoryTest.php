<?php

namespace Box\Tests\Factory;

use Box\Factory\FolderFactory;
use Box\Resource\Folder;
use PHPUnit\Framework\TestCase;

class FolderFactoryTest extends TestCase
{
    public function testCreateFolderReturnsEmptyResourceWhenOptionsIsNull(): void
    {
        $factory = new FolderFactory();
        $folder = $factory->createFolder(null);

        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertNull($folder->getId());
    }

    public function testCreateFolderHydratesWhenOptionsIsProvided(): void
    {
        $factory = new FolderFactory();
        $options = [
            'id' => '456',
            'name' => 'test folder',
            'size' => 2048
        ];
        $folder = $factory->createFolder($options);

        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals('456', $folder->getId());
        $this->assertEquals('test folder', $folder->getName());
        $this->assertEquals(2048, $folder->getSize());
    }
}
