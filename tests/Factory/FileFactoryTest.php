<?php

declare(strict_types=1);

namespace Box\Tests\Factory;

use Box\Factory\FileFactory;
use Box\Resource\File;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;

class FileFactoryTest extends TestCase
{
    public function testCreateFileReturnsEmptyResourceWhenOptionsIsNull(): void
    {
        $factory = new FileFactory();
        $file = $factory->createFile(null);

        $this->assertInstanceOf(File::class, $file);
        $this->assertNull($file->getId());
    }

    public function testCreateFileHydratesRealisticApiResponse(): void
    {
        $factory = new FileFactory();
        $file = $factory->createFile(BoxApiFixtures::fileResponse());

        $this->assertInstanceOf(File::class, $file);
        $this->assertSame('817696835', $file->getId());
        $this->assertSame('tigers.jpeg', $file->getName());
        $this->assertSame(629644, $file->getSize());
        $this->assertSame('134b65991ed521fcfe4724b7d814ab8ded5185dc', $file->getSha1());
        $this->assertSame('3', $file->getEtag());
        $this->assertSame('A photo of tigers', $file->getDescription());
        $this->assertSame('active', $file->getItemStatus());
        $this->assertSame('2012-12-12T10:55:30-08:00', $file->getCreatedAt());
        $this->assertSame('2012-12-12T11:04:26-08:00', $file->getModifiedAt());
        $this->assertNull($file->getTrashedAt());
    }

    public function testCreateFileSupportsOverrides(): void
    {
        $factory = new FileFactory();
        $file = $factory->createFile(BoxApiFixtures::fileResponse(['id' => '999', 'name' => 'override.txt']));

        $this->assertSame('999', $file->getId());
        $this->assertSame('override.txt', $file->getName());
    }
}
