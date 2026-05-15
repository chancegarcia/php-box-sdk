<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 10/9/15
 * Time: 5:32 PM
 *
 * @package     Box
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013 Chance Garcia, chancegarcia.com
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2016 Chance Garcia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Box\Service\File;

use Box\Dto\File\Request\CreateSharedLinkRequest;
use Box\Dto\File\UploadPart;
use Box\Dto\File\UploadSession;
use Box\Event\File\UploadPartUploaded;
use Box\Event\File\UploadSessionAborted;
use Box\Event\File\UploadSessionCommitted;
use Box\Event\File\UploadSessionCreated;
use Box\Exception\BoxResponseException;
use Box\Http\FileStream;
use Box\Resource\File;
use Box\Resource\SharedLink;
use Box\Service\Service;
use Psr\EventDispatcher\EventDispatcherInterface;

class FileService extends Service implements FileServiceInterface
{
    public const ENDPOINT = "https://api.box.com/2.0/files";
    public const UPLOAD_ENDPOINT = "https://upload.box.com/api/2.0/files/content";
    public const UPLOAD_SESSION_ENDPOINT = "https://upload.box.com/api/2.0/files/upload-sessions";

    private ?EventDispatcherInterface $eventDispatcher = null;

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->eventDispatcher = $dispatcher;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getFile(string $id): File
    {
        $uri = self::ENDPOINT . '/' . $id;

        return $this->getResourceFromBox($uri, File::class);
    }

    public function updateFile(File $file): File
    {
        $uri = self::ENDPOINT . '/' . $file->getId();

        $params = array_filter([
            'name' => $file->getName(),
            'description' => $file->getDescription(),
        ], fn($v) => null !== $v);

        return $this->sendUpdateAndHydrate($uri, $params, File::class);
    }

    public function deleteFile(string $id): void
    {
        $uri = self::ENDPOINT . '/' . $id;
        $this->sendDeleteToBox($uri);
    }

    public function downloadFile(string $id): string
    {
        $uri = self::ENDPOINT . '/' . $id . '/content';
        $response = $this->getConnection()->query($uri);
        if (!$response->isSuccessful()) {
            throw $this->processResponseError($response);
        }

        return $response->getContent();
    }

    /**
     * {@inheritdoc}
     *
     * @param File $file
     * @param SharedLink|CreateSharedLinkRequest|array|null $sharedLink
     */
    public function createSharedLink(File $file, SharedLink|CreateSharedLinkRequest|array|null $sharedLink = null): File
    {
        $uri = self::ENDPOINT . "/" . $file->getId();

        if (is_array($sharedLink)) {
            // Normalize array to DTO
            $sharedLink = $this->hydrate(CreateSharedLinkRequest::class, $sharedLink);
        }

        $params = [
            'shared_link' => $this->normalizeSharedLinkPayload($sharedLink),
        ];

        return $this->sendUpdateAndHydrate($uri, $params, File::class);
    }

    /**
     * @param SharedLink|CreateSharedLinkRequest|null $sharedLink
     * @return array
     */
    private function normalizeSharedLinkPayload(SharedLink|CreateSharedLinkRequest|null $sharedLink): array
    {
        if (null === $sharedLink) {
            return [];
        }

        if ($sharedLink instanceof CreateSharedLinkRequest) {
            return $sharedLink->toArray();
        }

        // Fallback for legacy models that might not be fully hydrated to DTOs yet
        if (method_exists($sharedLink, 'toArray')) {
            return $sharedLink->toArray();
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function uploadFile(string|FileStream $file, string|int $parentId): array
    {
        $uri = self::UPLOAD_ENDPOINT;
        $response = $this->getConnection()->postFile($uri, $file, $parentId);

        return $this->handleBoxResponse($response, 'flat');
    }

    /**
     * @return File
     */
    public function createNewFile(): File
    {
        return new File();
    }

    /**
     * @throws BoxResponseException
     */
    public function createUploadSession(string|int $parentId, string $filename, int $fileSize): UploadSession
    {
        $body = json_encode([
            'folder_id' => (string) $parentId,
            'file_name' => $filename,
            'file_size' => $fileSize,
        ], JSON_THROW_ON_ERROR);

        $response = $this->getConnection()->request('POST', self::UPLOAD_SESSION_ENDPOINT, [
            'body' => $body,
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        $data = $this->handleBoxResponse($response, 'flat');

        return new UploadSession(
            sessionId: $data['id'],
            uploadUrl: $data['upload_url'],
            partSize: $data['part_size'],
            sessionExpiresAt: $data['session_expires_at'],
            totalParts: $data['total_parts'],
            numPartsProcessed: $data['num_parts_processed'],
        );
    }

    /**
     * @throws BoxResponseException
     */
    public function uploadPart(string $sessionId, string $data, int $offset, int $totalSize): UploadPart
    {
        $length = strlen($data);
        $lastByte = $offset + $length - 1;

        $response = $this->getConnection()->request('PUT', self::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId, [
            'body' => $data,
            'headers' => [
                'Content-Type' => 'application/octet-stream',
                'Content-Range' => sprintf('bytes %d-%d/%d', $offset, $lastByte, $totalSize),
                'Digest' => 'sha=' . base64_encode(sha1($data, true)),
            ],
        ]);

        $partData = $this->handleBoxResponse($response, 'flat');

        return new UploadPart(
            partId: $partData['part']['part_id'],
            offset: $partData['part']['offset'],
            size: $partData['part']['size'],
            sha1: $partData['part']['sha1'],
        );
    }

    /**
     * @return UploadPart[]
     * @throws BoxResponseException
     */
    public function listUploadSessionParts(string $sessionId): array
    {
        $response = $this->getConnection()->query(self::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId . '/parts');
        $data = $this->handleBoxResponse($response, 'flat');

        return array_map(
            fn(array $part) => new UploadPart(
                partId: $part['part_id'],
                offset: $part['offset'],
                size: $part['size'],
                sha1: $part['sha1'],
            ),
            $data['entries'] ?? [],
        );
    }

    /** @param UploadPart[] $parts */
    public function commitUploadSession(string $sessionId, array $parts, string $fileSha1): File
    {
        $partPayload = array_map(
            fn(UploadPart $part) => [
                'part_id' => $part->partId,
                'offset' => $part->offset,
                'size' => $part->size,
            ],
            $parts,
        );

        $response = $this->getConnection()->request('POST', self::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId . '/commit', [
            'body' => json_encode(['parts' => $partPayload], JSON_THROW_ON_ERROR),
            'headers' => [
                'Content-Type' => 'application/json',
                'Digest' => 'sha=' . $fileSha1,
            ],
        ]);

        $data = $this->handleBoxResponse($response, 'flat');

        return $this->hydrate(File::class, $data['entries'][0]);
    }

    public function abortUploadSession(string $sessionId): void
    {
        $this->sendDeleteToBox(self::UPLOAD_SESSION_ENDPOINT . '/' . $sessionId);
    }

    /**
     * Upload a file using the chunked upload API. Handles session lifecycle, part upload, and commit.
     * Aborts the session and re-throws on any failure.
     *
     * @param string|FileStream $file Local path or open FileStream
     * @param string|int $parentId Box folder ID to upload into
     * @param int|null $partSize Override chunk size; defaults to Box-recommended session part size
     * @throws \Throwable
     */
    public function chunkedUpload(string|FileStream $file, string|int $parentId, ?int $partSize = null): File
    {
        if (is_string($file)) {
            $file = FileStream::fromPath($file);
        }

        $fileSize = $file->getSize();
        $session = $this->createUploadSession($parentId, $file->getFilename(), $fileSize);
        $dispatcher = $this->getEventDispatcher();

        if (null !== $dispatcher) {
            $dispatcher->dispatch(new UploadSessionCreated($session));
        }

        $actualPartSize = $partSize ?? $session->partSize;
        $hashCtx = hash_init('sha1');
        $parts = [];
        $offset = 0;
        $partNumber = 0;

        try {
            while (!$file->isEof()) {
                $chunk = $file->readChunk($actualPartSize);
                if ('' === $chunk) {
                    break;
                }

                hash_update($hashCtx, $chunk);
                $part = $this->uploadPart($session->sessionId, $chunk, $offset, $fileSize);
                $partNumber++;

                if (null !== $dispatcher) {
                    $dispatcher->dispatch(new UploadPartUploaded($part, $partNumber, $session->totalParts));
                }

                $parts[] = $part;
                $offset += strlen($chunk);
            }

            $fileSha1 = base64_encode(hash_final($hashCtx, true));
            $committed = $this->commitUploadSession($session->sessionId, $parts, $fileSha1);

            if (null !== $dispatcher) {
                $dispatcher->dispatch(new UploadSessionCommitted($committed));
            }

            return $committed;
        } catch (\Throwable $e) {
            $this->abortUploadSession($session->sessionId);

            if (null !== $dispatcher) {
                $dispatcher->dispatch(new UploadSessionAborted($session->sessionId, $e));
            }

            throw $e;
        }
    }

    public static function buildWebUrl(string $id, string $subdomain): string
    {
        return sprintf('https://%s.app.box.com/file/%s', $subdomain, $id);
    }
}
