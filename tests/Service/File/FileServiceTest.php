<?php

declare(strict_types=1);

namespace Box\Tests\Service\File;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Dto\File\Request\CreateSharedLinkRequest;
use Box\Resource\File;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\SharedLink;
use Box\Service\File\FileService;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;

class FileServiceTest extends TestCase
{
    private function createService(ConnectionInterface $connection): FileService
    {
        $service = new FileService();
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

    public function testGetFileReturnsFileResource(): void
    {
        $fileId = '817696835';
        $responseData = BoxApiFixtures::fileResponse(['id' => $fileId]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(FileService::ENDPOINT . '/' . $fileId)
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->getFile($fileId);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
        $this->assertSame('tigers.jpeg', $result->getName());
        $this->assertSame('134b65991ed521fcfe4724b7d814ab8ded5185dc', $result->getSha1());
        $this->assertSame('3', $result->getEtag());
        $this->assertSame('active', $result->getItemStatus());
        $this->assertSame(629644, $result->getSize());
    }

    public function testUpdateFileCallsPutWithNameAndDescription(): void
    {
        $fileId = '817696835';
        $file = new File();
        $file->setId($fileId);
        $file->setName('updated-tigers.jpeg');
        $file->setDescription('Updated description');

        $responseData = BoxApiFixtures::fileResponse(['id' => $fileId, 'name' => 'updated-tigers.jpeg', 'description' => 'Updated description']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                $this->callback(fn($p) => str_contains($p, 'updated-tigers.jpeg'))
            )
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->updateFile($file);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
        $this->assertSame('updated-tigers.jpeg', $result->getName());
    }

    public function testDeleteFileCallsDeleteOnConnection(): void
    {
        $fileId = '817696835';

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn([]);
        $response->method('getContent')->willReturn('');

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(FileService::ENDPOINT . '/' . $fileId)
            ->willReturn($response);

        $this->createService($connection)->deleteFile($fileId);
        $this->addToAssertionCount(1);
    }

    public function testDownloadFileReturnsContent(): void
    {
        $fileId = '817696835';
        $fileContent = 'binary file content here';

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('getContent')->willReturn($fileContent);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(FileService::ENDPOINT . '/' . $fileId . '/content')
            ->willReturn($response);

        $result = $this->createService($connection)->downloadFile($fileId);

        $this->assertSame($fileContent, $result);
    }

    public function testCreateSharedLinkWithRequestDto(): void
    {
        $fileId = '817696835';
        $file = new File();
        $file->setId($fileId);

        $request = (new CreateSharedLinkRequest())->withAccess('open');

        $responseData = BoxApiFixtures::fileResponse([
            'id'          => $fileId,
            'shared_link' => [
                'url'                => 'https://app.box.com/s/abcdef',
                'download_url'       => 'https://app.box.com/shared/static/abcdef.jpeg',
                'vanity_url'         => null,
                'is_password_enabled' => false,
                'access'             => 'open',
                'effective_access'   => 'open',
                'effective_permission' => 'can_download',
                'permissions'        => ['can_download' => true, 'can_preview' => true],
            ],
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                json_encode(['shared_link' => ['access' => 'open']])
            )
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->createSharedLink($file, $request);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
        $this->assertNotNull($result->getSharedLink());
        $this->assertInstanceOf(SharedLink::class, $result->getSharedLink());
    }

    public function testCreateSharedLinkWithArray(): void
    {
        $fileId = '817696835';
        $file = new File();
        $file->setId($fileId);

        $responseData = BoxApiFixtures::fileResponse([
            'id'          => $fileId,
            'shared_link' => ['access' => 'collaborators'],
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                json_encode(['shared_link' => ['access' => 'collaborators']])
            )
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->createSharedLink($file, ['access' => 'collaborators']);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
    }

    public function testCreateSharedLinkWithLegacySharedLink(): void
    {
        $fileId = '817696835';
        $file = new File();
        $file->setId($fileId);

        $legacySharedLink = $this->getMockBuilder(SharedLink::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray'])
            ->getMock();
        $legacySharedLink->method('toArray')->willReturn(['access' => 'company']);

        $responseData = BoxApiFixtures::fileResponse([
            'id'          => $fileId,
            'shared_link' => ['access' => 'company'],
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->createSharedLink($file, $legacySharedLink);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
    }

    public function testCreateSharedLinkWithNullSharedLink(): void
    {
        $fileId = '817696835';
        $file = new File();
        $file->setId($fileId);

        $responseData = BoxApiFixtures::fileResponse(['id' => $fileId, 'shared_link' => null]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                json_encode(['shared_link' => []])
            )
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->createSharedLink($file, null);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
        $this->assertNull($result->getSharedLink());
    }

    public function testUploadFileReturnsFileResource(): void
    {
        $responseData = BoxApiFixtures::uploadFileResponse(['id' => '987654321', 'name' => 'upload.txt']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('postFile')
            ->with(FileService::UPLOAD_ENDPOINT, 'local/path.txt', '0')
            ->willReturn($this->createMockResponse($responseData));

        $result = $this->createService($connection)->uploadFile('local/path.txt', '0');

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame('987654321', $result->getId());
        $this->assertSame('upload.txt', $result->getName());
    }

    public function testBuildWebUrl(): void
    {
        $this->assertSame(
            'https://acme.app.box.com/file/12345',
            FileService::buildWebUrl('12345', 'acme')
        );
    }
}
