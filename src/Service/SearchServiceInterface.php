<?php

declare(strict_types=1);

namespace Box\Service;

interface SearchServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * Search for items.
     */
    public function search(?string $query = null, int|string|null $limit = null, int|string|null $offset = null, ?string $type = null): array;
}
