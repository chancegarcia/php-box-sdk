<?php

namespace Box\Factory;

use Box\Resource\File;

interface FileFactoryInterface
{
    public function createFile(?array $options = null): File;
}
