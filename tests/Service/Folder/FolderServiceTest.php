<?php

namespace Box\Tests\Service\Folder;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Http\Response\BoxResponseInterface;
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

    public function testDeleteFolderCallsDelete(): void
    {
        $folderId = '99001';

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn([]);
        $response->method('getContent')->willReturn('');

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(FolderService::ENDPOINT . '/' . $folderId)
            ->willReturn($response);

        $service = new FolderService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));

        $service->deleteFolder($folderId);
        $this->addToAssertionCount(1);
    }

    public function testDeleteFolderRecursiveAppendsQueryParam(): void
    {
        $folderId = '99002';

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn([]);
        $response->method('getContent')->willReturn('');

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(FolderService::ENDPOINT . '/' . $folderId . '?recursive=true')
            ->willReturn($response);

        $service = new FolderService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));

        $service->deleteFolder($folderId, true);
        $this->addToAssertionCount(1);
    }
}
