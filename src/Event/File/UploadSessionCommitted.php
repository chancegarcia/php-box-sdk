<?php

namespace Box\Event\File;

use Box\Resource\File;

readonly class UploadSessionCommitted
{
    public function __construct(
        public File $file,
    ) {
    }
}
