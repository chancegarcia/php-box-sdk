<?php

namespace Box\Tests\Service\Folder;

use Box\Service\Folder\FolderService;
use PHPUnit\Framework\TestCase;

class FolderServiceTest extends TestCase
{
    public function testGetFolderItemsUri(): void
    {
        $service = new FolderService();
        $folderId = '12345';
        $limit = 50;
        $offset = 10;

        $expectedUri = FolderService::ENDPOINT . "/12345/items?limit=50&offset=10";
        $this->assertEquals($expectedUri, $service->getFolderItemsUri($folderId, $limit, $offset));
    }

    public function testGetFolderItemsUriDefaults(): void
    {
        $service = new FolderService();
        $folderId = 12345;

        $expectedUri = FolderService::ENDPOINT . "/12345/items?limit=100&offset=0";
        $this->assertEquals($expectedUri, $service->getFolderItemsUri($folderId));
    }
}
