<?php

declare(strict_types=1);

namespace Box\Tests\Factory;

use Box\Factory\FolderFactory;
use Box\Resource\Folder;
use Box\Tests\Fixtures\BoxApiFixtures;
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

    public function testCreateFolderHydratesRealisticApiResponse(): void
    {
        $factory = new FolderFactory();
        $folder = $factory->createFolder(BoxApiFixtures::folderResponse());

        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertSame('11446498', $folder->getId());
        $this->assertSame('Pictures', $folder->getName());
        $this->assertSame(629644, $folder->getSize());
        $this->assertSame('1', $folder->getEtag());
        $this->assertSame('A collection of photos', $folder->getDescription());
        $this->assertSame('active', $folder->getItemStatus());
        $this->assertSame('2012-12-12T10:53:43-08:00', $folder->getCreatedAt());
        $this->assertSame('2012-12-12T11:15:04-08:00', $folder->getModifiedAt());
    }

    public function testCreateFolderSupportsOverrides(): void
    {
        $factory = new FolderFactory();
        $folder = $factory->createFolder(BoxApiFixtures::folderResponse(['id' => '777', 'name' => 'Overrides']));

        $this->assertSame('777', $folder->getId());
        $this->assertSame('Overrides', $folder->getName());
    }
}
