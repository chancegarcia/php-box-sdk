<?php

namespace Box\Service\Collaboration;

use Box\Resource\Collaboration;
use Box\Resource\Folder;
use Box\Service\Service;

class CollaborationService extends Service implements CollaborationServiceInterface
{
    public const ENDPOINT = "https://api.box.com/2.0/collaborations";

    /**
     * @inheritdoc
     */
    public function getFolderCollaborations(Folder $folder): array
    {
        $uri = "https://api.box.com/2.0/folders/" . $folder->getId() . "/collaborations";

        return $this->queryBox($uri, 'flat');
    }

    /**
     * @inheritdoc
     */
    public function addCollaboration(Folder|string|int $folder, mixed $collaborator, string $role = 'editor'): Collaboration
    {
        $folderId = $folder instanceof Folder ? $folder->getId() : $folder;

        $params = [
            'item' => [
                'type' => 'folder',
                'id' => (string) $folderId,
            ],
            'accessible_by' => $collaborator,
            'role' => $role,
        ];

        return $this->sendUpdateAndHydrate(self::ENDPOINT, $params, Collaboration::class);
    }
}
