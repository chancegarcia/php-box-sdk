<?php

namespace Box\Factory;

use Box\Folder\Folder;
use Box\Folder\FolderInterface;

class FolderFactory implements FolderFactoryInterface
{
    public function createFolder(?array $options = null): FolderInterface
    {
        return new Folder($options);
    }
}
