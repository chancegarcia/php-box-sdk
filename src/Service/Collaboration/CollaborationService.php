<?php

namespace Box\Service\Collaboration;

use Box\Dto\PagedResult;
use Box\Exception\BoxResponseException;
use Box\Resource\Collaboration;
use Box\Resource\File;
use Box\Resource\Folder;
use Box\Service\Service;

class CollaborationService extends Service implements CollaborationServiceInterface
{
    public const string ENDPOINT = "https://api.box.com/2.0/collaborations";

    /**
     * @throws BoxResponseException
     * @return PagedResult<Collaboration>
     *
     * @inheritdoc
     */
    public function getFolderCollaborations(Folder $folder): PagedResult
    {
        $uri = "https://api.box.com/2.0/folders/" . $folder->getId() . "/collaborations";
        $data = $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');

        return $this->hydratePagedResult($data, Collaboration::class);
    }

    /**
     * @inheritdoc
     */
    public function addCollaboration(Folder|File|string|int $item, mixed $collaborator, string $role = 'editor'): Collaboration
    {
        if ($item instanceof Folder) {
            $itemType = 'folder';
            $itemId = (string) $item->getId();
        } elseif ($item instanceof File) {
            $itemType = 'file';
            $itemId = (string) $item->getId();
        } else {
            $itemType = 'folder';
            $itemId = (string) $item;
        }

        $params = [
            'item' => [
                'type' => $itemType,
                'id' => $itemId,
            ],
            'accessible_by' => $collaborator,
            'role' => $role,
        ];

        return $this->sendUpdateAndHydrate(self::ENDPOINT, $params, Collaboration::class);
    }

    public function getCollaboration(string $id): Collaboration
    {
        $uri = self::ENDPOINT . '/' . $id;

        return $this->getResourceFromBox($uri, Collaboration::class);
    }

    public function updateCollaboration(Collaboration $collaboration): Collaboration
    {
        $uri = self::ENDPOINT . '/' . $collaboration->getId();

        $params = array_filter([
            'role' => $collaboration->getRole(),
            'status' => $collaboration->getStatus(),
        ], fn($v) => null !== $v);

        return $this->sendUpdateAndHydrate($uri, $params, Collaboration::class);
    }

    public function deleteCollaboration(string $id): void
    {
        $uri = self::ENDPOINT . '/' . $id;
        $this->sendDeleteToBox($uri);
    }
}
