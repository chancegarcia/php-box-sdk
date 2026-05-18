<?php

namespace Box\Event\File;

readonly class UploadSessionAborted
{
    public function __construct(
        public string $sessionId,
        public \Throwable $error,
    ) {
    }
}
