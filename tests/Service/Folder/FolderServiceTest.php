<?php

declare(strict_types=1);

namespace Box\Tests\Service\Folder;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\Folder;
use Box\Service\Folder\FolderService;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;

class FolderServiceTest extends TestCase
{
    private function createService(ConnectionInterface $connection): FolderService
    {
        $service = new FolderService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    private function createMockResponse(array $data): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($data));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object)$data);
        return $response;
    }

    public function testGetFolderItemsUri(): void
    {
        $service = new FolderService();

        $this->assertEquals(
            FolderService::ENDPOINT . '/12345/items?limit=50&offset=10',
            $service->getFolderItemsUri('12345', 50, 10)
        );
    }

    public function testGetFolderItemsUriDefaults(): void
    {
        $service = new FolderService();

        $this->assertEquals(
            FolderService::ENDPOINT . '/12345/items?limit=100&offset=0',
            $service->getFolderItemsUri(12345)
        );
    }

    public function testGetFolderReturnsFolder(): void
    {
        $folderId = '11446498';
        $responseData = BoxApiFixtures::folderResponse(['id' => $folderId]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(FolderService::ENDPOINT . '/' . $folderId)
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->getFolder($folderId);

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame($folderId, $result->getId());
        $this->assertSame('Pictures', $result->getName());
        $this->assertSame('1', $result->getEtag());
        $this->assertSame('active', $result->getItemStatus());
        $this->assertSame(629644, $result->getSize());
    }

    public function testCreateFolderReturnsFolder(): void
    {
        $responseData = BoxApiFixtures::folderResponse(['id' => '99001', 'name' => 'New Folder']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(
                FolderService::ENDPOINT,
                $this->callback(fn($p) => is_string($p) && str_contains($p, '"New Folder"'))
            )
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->createFolder('New Folder', '0');

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame('99001', $result->getId());
        $this->assertSame('New Folder', $result->getName());
    }

    public function testCreateSharedLinkReturnsFolder(): void
    {
        $folderId = '11446498';
        $responseData = BoxApiFixtures::folderResponse([
            'id'          => $folderId,
            'shared_link' => ['access' => 'collaborators', 'url' => 'https://app.box.com/s/xyz'],
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(FolderService::ENDPOINT . '/' . $folderId)
            ->willReturn($this->createMockResponse($responseData));

        $folder = new Folder();
        $folder->setId($folderId);

        $result = $this->createService($connection)->createSharedLink($folder);

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame($folderId, $result->getId());
    }

    public function testCopyFolderReturnsFolder(): void
    {
        $originalId = '11446498';
        $parentId   = '0';
        $responseData = BoxApiFixtures::folderResponse(['id' => '22334455', 'name' => 'Pictures (Copy)']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(
                FolderService::ENDPOINT . '/' . $originalId . '/copy',
                $this->callback(fn($p) => is_string($p) && str_contains($p, '"parent"'))
            )
            ->willReturn($this->createMockResponse($responseData));

        $original = new Folder();
        $original->setId($originalId);

        $parent = new Folder();
        $parent->setId($parentId);

        $result = $this->createService($connection)->copyFolder($original, $parent, 'Pictures (Copy)');

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame('22334455', $result->getId());
        $this->assertSame('Pictures (Copy)', $result->getName());
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

        $this->createService($connection)->deleteFolder($folderId);
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

        $this->createService($connection)->deleteFolder($folderId, true);
        $this->addToAssertionCount(1);
    }

    public function testUpdateFolderReturnsFolderResource(): void
    {
        $folderId = '11446498';
        $responseData = BoxApiFixtures::folderResponse(['id' => $folderId, 'name' => 'Renamed Folder']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FolderService::ENDPOINT . '/' . $folderId,
                $this->callback(fn($p) => str_contains($p, 'Renamed Folder'))
            )
            ->willReturn($this->createMockResponse($responseData));

        $folder = new Folder();
        $folder->setId($folderId);
        $folder->setName('Renamed Folder');

        $result = $this->createService($connection)->updateFolder($folder);

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame($folderId, $result->getId());
        $this->assertSame('Renamed Folder', $result->getName());
    }

    public function testBuildWebUrl(): void
    {
        $this->assertSame(
            'https://acme.app.box.com/folder/99001',
            FolderService::buildWebUrl('99001', 'acme')
        );
    }
}
