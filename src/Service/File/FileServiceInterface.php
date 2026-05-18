<?php

namespace Box\Service\File;

use Box\Dto\File\Request\CreateSharedLinkRequest;
use Box\Dto\File\UploadPart;
use Box\Dto\File\UploadSession;
use Box\Http\FileStream;
use Box\Resource\File;
use Box\Resource\SharedLink;
use Box\Service\AuthenticatedServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

interface FileServiceInterface extends AuthenticatedServiceInterface
{
    public function getFile(string $id): File;

    /**
     * @throws \JsonException
     */
    public function updateFile(File $file): File;

    public function deleteFile(string $id): void;

    public function downloadFile(string $id): string;

    /**
     * @param SharedLink|CreateSharedLinkRequest|array|null $sharedLink shared link object used to set box permissions
     *
     * @throws \JsonException
     */
    public function createSharedLink(File $file, SharedLink|CreateSharedLinkRequest|array|null $sharedLink = null): File;

    public function uploadFile(string|FileStream $file, string|int $parentId): File;

    public function createNewFile(): File;

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void;

    public function getEventDispatcher(): ?EventDispatcherInterface;

    public function createUploadSession(string|int $parentId, string $filename, int $fileSize): UploadSession;

    public function uploadPart(string $sessionId, string $data, int $offset, int $totalSize): UploadPart;

    /** @return UploadPart[] */
    public function listUploadSessionParts(string $sessionId): array;

    /** @param UploadPart[] $parts */
    public function commitUploadSession(string $sessionId, array $parts, string $fileSha1): File;

    public function abortUploadSession(string $sessionId): void;

    public function chunkedUpload(string|FileStream $file, string|int $parentId, ?int $partSize = null): File;
}
