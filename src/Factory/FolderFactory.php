<?php

namespace Box\Factory;

use Box\Resource\Folder;

class FolderFactory implements FolderFactoryInterface
{
    public function createFolder(?array $options = null): Folder
    {
        return new Folder($options);
    }
}
