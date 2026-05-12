<?php

namespace Box\Service\Folder;

use Box\Resource\Folder;
use Box\Service\Service;

class FolderService extends Service implements FolderServiceInterface
{
    public const ENDPOINT = 'https://api.box.com/2.0/folders';
    public const SHARED_ITEM_ENDPOINT = 'https://api.box.com/2.0/shared_items';

    /**
     * @inheritdoc
     */
    public function getFolderItemsUri(string|int $folderId, int $limit = 100, int $offset = 0): string
    {
        return self::ENDPOINT . "/" . $folderId . "/items" . "?limit=" . $limit . "&offset=" . $offset;
    }

    /**
     * @param string|int $id
     * @return Folder
     * @throws \Box\Exception\BoxException
     */
    public function getFolder(string|int $id): Folder
    {
        $uri = self::ENDPOINT . '/' . $id;

        return $this->getResourceFromBox($uri, Folder::class);
    }

    /**
     * @inheritdoc
     */
    public function getFolderBySharedUri(string $sharedUri): Folder|false
    {
        $uri = self::SHARED_ITEM_ENDPOINT;

        $connection = $this->getConnection();
        $connection->addHeader('BoxApi', "shared_link=" . $sharedUri);

        $response = $connection->query($uri);
        $data = $this->handleBoxResponse($response, 'flat');

        if (is_array($data) && array_key_exists('type', $data) && 'folder' === $data['type']) {
            return $this->hydrate(Folder::class, $data);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getFolderItems(string|int $id, int $limit = 100, int $offset = 0): Folder
    {
        $uri = $this->getFolderItemsUri($id, $limit, $offset);
        $data = $this->queryBox($uri, 'flat');

        $folder = $this->getFolder($id);
        $folder->setItemCollection($data);

        return $folder;
    }

    /**
     * @inheritdoc
     */
    public function createFolder(string $name, string|int $parentId = 0, array $options = []): Folder
    {
        $params = [
            'name' => $name,
            'parent' => ['id' => (string)$parentId]
        ];

        $params = array_merge_recursive($params, $options);

        $uri = self::ENDPOINT;

        $response = $this->getConnection()->post($uri, $params, true);

        $data = $this->handleBoxResponse($response, 'flat');

        return $this->hydrate(Folder::class, $data);
    }

    /**
     * @inheritdoc
     */
    public function updateFolder(Folder $folder, string|bool|null $ifMatch = null): array
    {
        $uri = self::ENDPOINT . '/' . $folder->getId();

        $params = [
            'name' => $folder->getName(),
            'description' => $folder->getDescription(),
        ];

        $parent = $folder->getParent();
        if (null !== $parent) {
            $parentId = null;
            if (is_array($parent) && isset($parent['id'])) {
                $parentId = $parent['id'];
            } elseif (is_object($parent) && method_exists($parent, 'getId')) {
                $parentId = $parent->getId();
            }

            if (null !== $parentId) {
                $params['parent'] = ['id' => $parentId];
            }
        }

        $sharedLink = $folder->getSharedLink();
        if (null !== $sharedLink) {
            if (method_exists($sharedLink, 'toArray')) {
                $params['shared_link'] = $sharedLink->toArray();
            } else {
                $params['shared_link'] = (array) $sharedLink;
            }
        }

        $params = array_filter($params, fn($v) => null !== $v);

        if (true === $ifMatch) {
            $ifMatch = $folder->getEtag();
        }

        if (is_string($ifMatch) && !empty($ifMatch)) {
            $connection = $this->getConnection();
            $connection->addHeader('If-Match', $ifMatch);
            $response = $connection->put($uri, $params, true);
            return $this->handleBoxResponse($response, 'flat');
        }

        return $this->sendUpdateToBox($uri, $params, 'PUT', null, 'flat');
    }

    /**
     * @inheritdoc
     */
    public function getFolderCollaborations(Folder $folder): array
    {
        $uri = self::ENDPOINT . '/' . $folder->getId() . '/collaborations';

        return $this->queryBox($uri, 'flat');
    }

    /**
     * @inheritdoc
     */
    public function createSharedLink(Folder $folder, ?array $params = null): Folder
    {
        $uri = self::ENDPOINT . "/" . $folder->getId();

        if (null === $params) {
            $params = [
                'shared_link' => [
                    'access' => 'collaborators'
                ]
            ];
        }

        return $this->sendUpdateAndHydrate($uri, $params, Folder::class);
    }

    /**
     * @inheritdoc
     */
    public function copyFolder(Folder $originalFolder, Folder $parent, ?string $name = null): Folder
    {
        $uri = self::ENDPOINT . '/' . $originalFolder->getId() . '/copy';

        $params['parent'] = ['id' => (string)$parent->getId()];
        if (null !== $name) {
            $params['name'] = $name;
        }

        $response = $this->getConnection()->post($uri, $params, true);

        $data = $this->handleBoxResponse($response, 'flat');

        return $this->hydrate(Folder::class, $data);
    }
}
