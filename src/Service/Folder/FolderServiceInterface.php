<?php

namespace Box\Service\Folder;

use Box\Service\ServiceInterface;

interface FolderServiceInterface extends ServiceInterface
{
    /**
     * @param string|int $folderId
     * @param int $limit
     * @param int $offset
     * @return string
     */
    public function getFolderItemsUri(string|int $folderId, int $limit = 100, int $offset = 0): string;
}
