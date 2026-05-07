<?php

namespace Box\Factory;

use Box\File\FileInterface;

interface FileFactoryInterface
{
    public function createFile(?array $options = null): FileInterface;
}
