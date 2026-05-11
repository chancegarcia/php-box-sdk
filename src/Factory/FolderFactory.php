<?php

namespace Box\Factory;

use Box\Resource\Folder;

class FolderFactory
{
    public function createFolder(?array $options = null): Folder
    {
        return new Folder($options);
    }
}
