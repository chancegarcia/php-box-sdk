<?php

namespace Box\Event\File;

use Box\Resource\File;

readonly class FileUploaded
{
    public function __construct(
        public File $file,
    ) {
    }
}
