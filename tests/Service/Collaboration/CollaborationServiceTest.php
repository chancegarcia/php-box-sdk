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
use Box\Tests\Fixtures\BoxApiFixtures;
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
        $folder->setId('11446498');

        $collabData = BoxApiFixtures::collaborationResponse([
            'id'   => 'collab-1',
            'role' => 'editor',
            'item' => ['type' => 'folder', 'id' => '11446498', 'name' => 'Pictures'],
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                CollaborationService::ENDPOINT,
                $this->callback(fn($b) => str_contains($b, '"folder"') && str_contains($b, '11446498'))
            )
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->addCollaboration(
            $folder,
            ['id' => '755492', 'type' => 'user']
        );

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame('collab-1', $result->getId());
        $this->assertSame('editor', $result->getRole());
    }

    public function testAddCollaborationWithFileObject(): void
    {
        $file = new File();
        $file->setId('817696835');

        $collabData = BoxApiFixtures::collaborationResponse([
            'id'   => 'collab-2',
            'role' => 'viewer',
            'item' => ['type' => 'file', 'id' => '817696835', 'name' => 'tigers.jpeg'],
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                CollaborationService::ENDPOINT,
                $this->callback(fn($b) => str_contains($b, '"file"') && str_contains($b, '817696835'))
            )
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->addCollaboration(
            $file,
            ['id' => '755492', 'type' => 'user'],
            'viewer'
        );

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame('collab-2', $result->getId());
        $this->assertSame('viewer', $result->getRole());
    }

    public function testGetCollaborationReturnsCollaborationResource(): void
    {
        $collabId = '14176246';
        $collabData = BoxApiFixtures::collaborationResponse(['id' => $collabId]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(CollaborationService::ENDPOINT . '/' . $collabId)
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->getCollaboration($collabId);

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame($collabId, $result->getId());
        $this->assertSame('editor', $result->getRole());
        $this->assertSame('accepted', $result->getStatus());
    }

    public function testUpdateCollaborationCallsPut(): void
    {
        $collab = new Collaboration();
        $collab->setId('14176246');
        $collab->setRole('viewer');

        $collabData = BoxApiFixtures::collaborationResponse(['id' => '14176246', 'role' => 'viewer']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                CollaborationService::ENDPOINT . '/14176246',
                $this->callback(fn($b) => str_contains($b, 'viewer'))
            )
            ->willReturn($this->createMockResponse($collabData));

        $result = $this->createService($connection)->updateCollaboration($collab);

        $this->assertInstanceOf(Collaboration::class, $result);
        $this->assertSame('14176246', $result->getId());
        $this->assertSame('viewer', $result->getRole());
    }

    public function testDeleteCollaborationCallsDelete(): void
    {
        $collabId = '14176246';

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(CollaborationService::ENDPOINT . '/' . $collabId)
            ->willReturn($this->createDeleteResponse());

        $this->createService($connection)->deleteCollaboration($collabId);
        $this->addToAssertionCount(1);
    }
}
