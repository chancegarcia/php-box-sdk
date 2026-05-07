<?php

namespace Box\Factory;

use Box\Folder\FolderInterface;

interface FolderFactoryInterface
{
    public function createFolder(?array $options = null): FolderInterface;
}
