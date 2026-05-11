<?php

declare(strict_types=1);

namespace Box\Service;

interface SearchServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * Search for items.
     *
     * @param string|null $query
     * @param int|string|null $limit
     * @param int|string|null $offset
     * @param string|null $type
     * @return array
     */
    public function search(?string $query = null, int|string|null $limit = null, int|string|null $offset = null, ?string $type = null): array;
}
