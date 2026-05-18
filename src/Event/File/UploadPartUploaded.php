<?php

namespace Box\Event\File;

use Box\Dto\File\UploadPart;

readonly class UploadPartUploaded
{
    public function __construct(
        public UploadPart $part,
        public int $partNumber,
        public int $totalParts,
    ) {
    }
}
