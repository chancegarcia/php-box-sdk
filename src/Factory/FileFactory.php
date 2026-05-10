<?php

namespace Box\Factory;

use Box\Resource\File;

class FileFactory implements FileFactoryInterface
{
    public function createFile(?array $options = null): File
    {
        return new File($options);
    }
}
