<?php

declare(strict_types=1);

namespace Box\Service\Collaboration;

use Box\Resource\Collaboration;
use Box\Resource\Folder;
use Box\Service\AuthenticatedServiceInterface;

interface CollaborationServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * Get collaborations for a folder.
     *
     * @param Folder $folder
     * @return array
     */
    public function getFolderCollaborations(Folder $folder): array;

    /**
     * Add a collaboration to a folder.
     *
     * @param Folder|string|int $folder
     * @param mixed $collaborator
     * @param string $role
     * @return Collaboration
     */
    public function addCollaboration(Folder|string|int $folder, mixed $collaborator, string $role = 'editor'): Collaboration;
}
