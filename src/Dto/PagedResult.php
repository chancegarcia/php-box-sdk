<?php

declare(strict_types=1);

namespace Box\Dto;

/**
 * @template T of object
 */
final readonly class PagedResult
{
    /**
     * @param T[] $entries
     */
    public function __construct(
        public array $entries,
        public int $totalCount,
        public int $offset,
        public int $limit,
    ) {
    }
}
