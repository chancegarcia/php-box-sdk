<?php

namespace Box\Service\Folder;

use Box\Exception\BoxResponseException;
use Box\Resource\Folder;
use Box\Service\AuthenticatedServiceInterface;

interface FolderServiceInterface extends AuthenticatedServiceInterface
{
    public function getFolderItemsUri(string|int $folderId, int $limit = 100, int $offset = 0): string;

    public function getFolder(string|int $id): Folder;

    public function getFolderBySharedUri(string $sharedUri): Folder|false;

    public function getFolderItems(string|int $id, int $limit = 100, int $offset = 0): Folder;

    /**
     * @throws \JsonException
     */
    public function createFolder(string $name, string|int $parentId = 0, array $options = []): Folder;

    /**
     * @throws BoxResponseException
     * @throws \JsonException
     */
    public function updateFolder(Folder $folder, string|bool|null $ifMatch = null): Folder;

    public function deleteFolder(string $id, bool $recursive = false): void;

    /**
     * @param array|null $params
     *
     * @throws \JsonException
     */
    public function createSharedLink(Folder $folder, ?array $params = null): Folder;

    /**
     * @throws \JsonException
     */
    public function copyFolder(Folder $originalFolder, Folder $parent, ?string $name = null): Folder;
}
