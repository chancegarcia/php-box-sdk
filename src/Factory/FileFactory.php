<?php

namespace Box\Factory;

use Box\Resource\File;
use Box\Mapper\Hydrator;
use ReflectionException;

class FileFactory
{
    /**
     * @throws ReflectionException
     */
    public function createFile(?array $options = null): File
    {
        $file = new File();
        if (null !== $options) {
            new Hydrator()->hydrate($file, $options);
        }

        return $file;
    }
}
