<?php

namespace Box\Factory;

use Box\Resource\File;

class FileFactory
{
    public function createFile(?array $options = null): File
    {
        return new File();
    }
}
