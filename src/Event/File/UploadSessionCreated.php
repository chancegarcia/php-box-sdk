<?php

namespace Box\Event\File;

use Box\Dto\File\UploadSession;

readonly class UploadSessionCreated
{
    public function __construct(
        public UploadSession $session,
    ) {
    }
}
