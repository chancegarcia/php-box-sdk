<?php

declare(strict_types=1);

namespace Box\Service\Collaboration;

use Box\Dto\PagedResult;
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
     *
     * @return PagedResult<Collaboration>
     */
    public function getFolderCollaborations(Folder $folder): PagedResult;

    /**
     * Add a collaboration to a folder or file.
     *
     * @param Folder|File|string|int $item
     * @param mixed $collaborator
     * @param string $role
     *
     * @throws \JsonException
     * @return Collaboration
     */
    public function addCollaboration(Folder|File|string|int $item, mixed $collaborator, string $role = 'editor'): Collaboration;

    public function getCollaboration(string $id): Collaboration;

    /**
     * @throws \JsonException
     */
    public function updateCollaboration(Collaboration $collaboration): Collaboration;

    public function deleteCollaboration(string $id): void;
}
