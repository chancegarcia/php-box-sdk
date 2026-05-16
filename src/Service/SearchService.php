<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Exception\BoxException;

class SearchService extends Service implements SearchServiceInterface
{
    public const string ENDPOINT = "https://api.box.com/2.0/search";

    public function search(?string $query = null, int|string|null $limit = null, int|string|null $offset = null, ?string $type = null): array
    {
        if (null === $query || "" === $query) {
            throw new BoxException('please enter a search term', BoxException::INVALID_INPUT);
        }

        $uriQuery = urlencode($query);
        $uri = self::ENDPOINT . "/?query=" . $uriQuery;

        if (null !== $limit) {
            $uri .= "&limit=" . $limit;
        }

        if (null !== $offset) {
            $uri .= "&offset=" . $offset;
        }

        if (null !== $type) {
            $uri .= "&type=" . $type;
        }

        return $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');
    }
}
