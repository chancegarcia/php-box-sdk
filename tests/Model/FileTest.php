<?php

namespace Box\Tests\Model;

use Box\File\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetExtension()
    {
        $file = new File();

        // null name
        $this->assertEquals('', $file->getExtension());

        // empty name
        $file->setName('');
        $this->assertEquals('', $file->getExtension());

        // normal extension
        $file->setName('document.pdf');
        $this->assertEquals('pdf', $file->getExtension());

        // multi-dot name
        $file->setName('archive.tar.gz');
        $this->assertEquals('gz', $file->getExtension());

        // no extension
        $file->setName('README');
        $this->assertEquals('', $file->getExtension());

        // dotfile (.env)
        $file->setName('.env');
        $this->assertEquals('', $file->getExtension());

        // hidden file with extension (.gitignore) - also treated as no extension per the .env rule
        $file->setName('.gitignore');
        $this->assertEquals('', $file->getExtension());
    }
}
