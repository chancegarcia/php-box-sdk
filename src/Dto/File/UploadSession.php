<?php

namespace Box\Dto\File;

readonly class UploadSession
{
    public function __construct(
        public string $sessionId,
        public string $uploadUrl,
        public int $partSize,
        public string $sessionExpiresAt,
        public int $totalParts,
        public int $numPartsProcessed,
    ) {
    }
}
