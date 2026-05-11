<?php

namespace Box\Service\Folder;

use Box\Service\Service;

class FolderService extends Service implements FolderServiceInterface
{
    public const ENDPOINT = 'https://api.box.com/2.0/folders';
    public const SHARED_ITEM_ENDPOINT = 'https://api.box.com/2.0/shared_items';

    /**
     * @inheritdoc
     */
    public function getFolderItemsUri(string|int $folderId, int $limit = 100, int $offset = 0): string
    {
        return self::ENDPOINT . "/" . $folderId . "/items" . "?limit=" . $limit . "&offset=" . $offset;
    }
}
