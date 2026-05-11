<?php

namespace Box\Tests\Factory;

use Box\Factory\FileFactory;
use Box\Resource\File;
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

    public function testCreateFileHydratesWhenOptionsIsProvided(): void
    {
        $factory = new FileFactory();
        $options = [
            'id' => '123',
            'name' => 'test.txt',
            'size' => 1024
        ];
        $file = $factory->createFile($options);

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('123', $file->getId());
        $this->assertEquals('test.txt', $file->getName());
        $this->assertEquals(1024, $file->getSize());
    }
}
