<?php

declare(strict_types=1);

namespace Box\Service\Collaboration;

use Box\Resource\Collaboration;
use Box\Resource\File;
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
     * Add a collaboration to a folder or file.
     *
     * @param Folder|File|string|int $item
     * @param mixed $collaborator
     * @param string $role
     * @return Collaboration
     */
    public function addCollaboration(Folder|File|string|int $item, mixed $collaborator, string $role = 'editor'): Collaboration;

    public function getCollaboration(string $id): Collaboration;

    public function updateCollaboration(Collaboration $collaboration): Collaboration;

    public function deleteCollaboration(string $id): void;
}
