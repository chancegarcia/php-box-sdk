<?php

declare(strict_types=1);

namespace Box\Dto;

use Box\Resource\Folder;

class PathCollection
{
    /** @param Folder[] $entries */
    public function __construct(
        public readonly int $totalCount,
        public readonly array $entries,
    ) {
    }
}
