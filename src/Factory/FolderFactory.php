<?php

namespace Box\Factory;

use Box\Resource\Folder;
use Box\Mapper\Hydrator;
use ReflectionException;

class FolderFactory
{
    /**
     * @throws ReflectionException
     */
    public function createFolder(?array $options = null): Folder
    {
        $folder = new Folder();
        if (null !== $options) {
            new Hydrator()->hydrate($folder, $options);
        }

        return $folder;
    }
}
