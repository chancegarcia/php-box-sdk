<?php

declare(strict_types=1);

namespace Box\Tests\Service\File;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Event\File\FileUploaded;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\File;
use Box\Service\File\FileService;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class FileUploadedEventTest extends TestCase
{
    private function createMockResponse(array $data): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($data));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object)$data);
        return $response;
    }

    private function createService(ConnectionInterface $connection): FileService
    {
        $service = new FileService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    public function testUploadFileDispatchesFileUploadedEvent(): void
    {
        $responseData = BoxApiFixtures::uploadFileResponse(['id' => '123', 'name' => 'test.txt']);
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('postFile')->willReturn($this->createMockResponse($responseData));

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(FileUploaded::class));

        $service = $this->createService($connection);
        $service->setEventDispatcher($dispatcher);
        $service->uploadFile('path/to/file.txt', '0');
    }

    public function testUploadFileDispatchedEventHoldsHydratedFile(): void
    {
        $responseData = BoxApiFixtures::uploadFileResponse(['id' => '456', 'name' => 'report.pdf']);
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('postFile')->willReturn($this->createMockResponse($responseData));

        $capturedEvent = null;
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnCallback(function (object $event) use (&$capturedEvent) {
            $capturedEvent = $event;
            return $event;
        });

        $service = $this->createService($connection);
        $service->setEventDispatcher($dispatcher);
        $result = $service->uploadFile('path/to/file.txt', '0');

        $this->assertInstanceOf(FileUploaded::class, $capturedEvent);
        $this->assertInstanceOf(File::class, $capturedEvent->file);
        $this->assertSame('456', $capturedEvent->file->getId());
        $this->assertSame($result, $capturedEvent->file);
    }

    public function testUploadFileDoesNotDispatchWhenNoDispatcher(): void
    {
        $responseData = BoxApiFixtures::uploadFileResponse(['id' => '789', 'name' => 'no-event.txt']);
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('postFile')->willReturn($this->createMockResponse($responseData));

        $service = $this->createService($connection);
        $result = $service->uploadFile('path/to/file.txt', '0');

        $this->assertInstanceOf(File::class, $result);
    }
}
