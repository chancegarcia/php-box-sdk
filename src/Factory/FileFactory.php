<?php

namespace Box\Factory;

use Box\File\File;
use Box\File\FileInterface;

class FileFactory implements FileFactoryInterface
{
    public function createFile(?array $options = null): FileInterface
    {
        return new File($options);
    }
}
