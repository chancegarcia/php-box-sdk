<?php

declare(strict_types=1);

namespace Box\Tests\Service\File;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Dto\File\Request\CreateSharedLinkRequest;
use Box\Resource\File;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\File\FileService;
use PHPUnit\Framework\TestCase;
use Box\Item\SharedLink\SharedLink;

class FileServiceTest extends TestCase
{
    private function createService(ConnectionInterface $connection): FileService
    {
        $service = new FileService();
        $service->setAuthorizedConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    private function createMockResponse(array $data): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($data));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(function (bool $assoc) use ($data) {
            return $assoc ? $data : (object)$data;
        });
        return $response;
    }

    public function testCreateSharedLinkWithRequestDto(): void
    {
        $fileId = '12345';
        $file = new File();
        $file->setId($fileId);

        $request = (new CreateSharedLinkRequest())->withAccess('open');

        $responseData = [
            'type' => 'file',
            'id' => $fileId,
            'shared_link' => [
                'url' => 'https://box.com/s/abcdef',
                'access' => 'open'
            ]
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                json_encode(['shared_link' => ['access' => 'open']])
            )
            ->willReturn($this->createMockResponse($responseData));

        $service = $this->createService($connection);
        $result = $service->createSharedLink($file, $request);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
        $sharedLink = $result->getSharedLink();
        // Depending on hydration, this might be an array or object.
        // File::getSharedLink() says @return SharedLink|array|null
        $this->assertNotNull($sharedLink);
    }

    public function testCreateSharedLinkWithArray(): void
    {
        $fileId = '67890';
        $file = new File();
        $file->setId($fileId);

        $requestArray = ['access' => 'collaborators'];

        $responseData = [
            'type' => 'file',
            'id' => $fileId,
            'shared_link' => [
                'access' => 'collaborators'
            ]
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                json_encode(['shared_link' => ['access' => 'collaborators']])
            )
            ->willReturn($this->createMockResponse($responseData));

        $service = $this->createService($connection);
        $result = $service->createSharedLink($file, $requestArray);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
    }

    public function testCreateSharedLinkWithLegacySharedLinkInterface(): void
    {
        $fileId = '112233';
        $file = new File();
        $file->setId($fileId);

        // In PHPUnit 10, addMethods is for non-existent methods on classes.
        // For interfaces, use setMethods (deprecated) or just createMock and hope it has it,
        // or better, use a class that implements the interface and has the method.
        // Since we removed toArray from the interface, we can just use a generic mock
        // and add the method if it's a class mock, or just mock the interface if we don't need toArray.
        // However, FileService checks for method_exists($sharedLink, 'toArray').

        $legacySharedLink = $this->getMockBuilder(SharedLink::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray'])
            ->getMock();
        $legacySharedLink->method('toArray')->willReturn(['access' => 'company']);

        $responseData = [
            'type' => 'file',
            'id' => $fileId,
            'shared_link' => [
                'access' => 'company'
            ]
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->willReturn($this->createMockResponse($responseData));

        $service = $this->createService($connection);
        $result = $service->createSharedLink($file, $legacySharedLink);

        $this->assertInstanceOf(File::class, $result);
    }

    public function testCreateSharedLinkWithNullSharedLink(): void
    {
        $fileId = '998877';
        $file = new File();
        $file->setId($fileId);

        $responseData = [
            'type' => 'file',
            'id' => $fileId,
            'shared_link' => null
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FileService::ENDPOINT . '/' . $fileId,
                json_encode(['shared_link' => []])
            )
            ->willReturn($this->createMockResponse($responseData));

        $service = $this->createService($connection);
        $result = $service->createSharedLink($file, null);

        $this->assertInstanceOf(File::class, $result);
        $this->assertSame($fileId, $result->getId());
    }
}
