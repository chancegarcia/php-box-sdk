<?php

namespace Box\Factory;

use Box\Resource\Folder;
use Box\Mapper\Hydrator;

class FolderFactory
{
    public function createFolder(?array $options = null): Folder
    {
        $folder = new Folder();
        if (null !== $options) {
            (new Hydrator())->hydrate($folder, $options);
        }

        return $folder;
    }
}
