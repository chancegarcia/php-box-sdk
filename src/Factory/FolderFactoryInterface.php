<?php

namespace Box\Factory;

use Box\Resource\Folder;

interface FolderFactoryInterface
{
    public function createFolder(?array $options = null): Folder;
}
