<?php

declare(strict_types=1);

namespace Box\Tests\Service\Collaboration;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\Collaboration;
use Box\Resource\File;
use Box\Resource\Folder;
use Box\Service\Collaboration\CollaborationService;
use PHPUnit\Framework\TestCase;

class CollaborationServiceTest extends TestCase
{
    private function createService(ConnectionInterface $connection): CollaborationService
    {
        $service = new CollaborationService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    private function createMockResponse(array $data): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('getContent')->willReturn(json_encode($data));
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object)$data);
        return $response;
    }

    private function createDeleteResponse(): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn([]);
        $response->method('getContent')->willReturn('');
        return $response;
    }

    public function testAddCollaborationWithFolderObject(): void
    {
        $folder = new Folder();
        $folder->setId('folder-1');

        $collabData = [
            'type' => 'collaboration',
            'id' => 'collab-1',
            'role' => 'editor',
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                CollaborationService::ENDPOINT,
                $this->callback(fn($b) => str_contains($b, 'folder') && str_contains($b, 'folder-1'))
            )
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->addCollaboration($folder, ['id' => 'user-1', 'type' => 'user']);

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame('collab-1', $result->getId());
    }

    public function testAddCollaborationWithFileObject(): void
    {
        $file = new File();
        $file->setId('file-1');

        $collabData = [
            'type' => 'collaboration',
            'id' => 'collab-2',
            'role' => 'viewer',
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                CollaborationService::ENDPOINT,
                $this->callback(fn($b) => str_contains($b, '"file"') && str_contains($b, 'file-1'))
            )
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->addCollaboration($file, ['id' => 'user-2', 'type' => 'user'], 'viewer');

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame('collab-2', $result->getId());
    }

    public function testGetCollaborationReturnsCollaborationResource(): void
    {
        $collabId = 'collab-get-1';
        $collabData = ['type' => 'collaboration', 'id' => $collabId, 'role' => 'editor'];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(CollaborationService::ENDPOINT . '/' . $collabId)
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->getCollaboration($collabId);

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame($collabId, $result->getId());
    }

    public function testUpdateCollaborationCallsPut(): void
    {
        $collab = new Collaboration();
        $collab->setId('collab-upd-1');
        $collab->setRole('viewer');

        $collabData = ['type' => 'collaboration', 'id' => 'collab-upd-1', 'role' => 'viewer'];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                CollaborationService::ENDPOINT . '/collab-upd-1',
                $this->callback(fn($b) => str_contains($b, 'viewer'))
            )
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->updateCollaboration($collab);

        $this->assertInstanceOf(Collaboration::class, $result);
    }

    public function testDeleteCollaborationCallsDelete(): void
    {
        $collabId = 'collab-del-1';

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(CollaborationService::ENDPOINT . '/' . $collabId)
            ->willReturn($this->createDeleteResponse());

        $this->createService($connection)->deleteCollaboration($collabId);
        $this->addToAssertionCount(1);
    }
}
