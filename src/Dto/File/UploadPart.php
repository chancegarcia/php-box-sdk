<?php

namespace Box\Dto\File;

readonly class UploadPart
{
    public function __construct(
        public string $partId,
        public int $offset,
        public int $size,
        public string $sha1,
    ) {
    }
}
