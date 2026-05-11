<?php

namespace Box\Service\Folder;

use Box\Resource\Folder;
use Box\Service\AuthenticatedServiceInterface;

interface FolderServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * @param string|int $folderId
     * @param int $limit
     * @param int $offset
     * @return string
     */
    public function getFolderItemsUri(string|int $folderId, int $limit = 100, int $offset = 0): string;

    /**
     * @param string|int $id
     * @return Folder
     */
    public function getFolder(string|int $id): Folder;

    /**
     * @param string $sharedUri
     * @return Folder|false
     */
    public function getFolderBySharedUri(string $sharedUri): Folder|false;

    /**
     * @param string|int $id
     * @param int $limit
     * @param int $offset
     * @return Folder
     */
    public function getFolderItems(string|int $id, int $limit = 100, int $offset = 0): Folder;

    /**
     * @param string $name
     * @param string|int $parentId
     * @param array $options
     * @return Folder
     */
    public function createFolder(string $name, string|int $parentId = 0, array $options = []): Folder;

    /**
     * @param Folder $folder
     * @param string|bool|null $ifMatch
     * @return array
     */
    public function updateFolder(Folder $folder, string|bool|null $ifMatch = null): array;

    /**
     * @param Folder $folder
     * @return array
     */
    public function getFolderCollaborations(Folder $folder): array;

    /**
     * @param Folder $folder
     * @param array|null $params
     * @return Folder
     */
    public function createSharedLink(Folder $folder, ?array $params = null): Folder;

    /**
     * @param Folder $originalFolder
     * @param Folder $parent
     * @param string|null $name
     * @return Folder
     */
    public function copyFolder(Folder $originalFolder, Folder $parent, ?string $name = null): Folder;
}
