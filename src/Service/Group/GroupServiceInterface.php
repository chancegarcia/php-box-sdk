<?php

namespace Box\Service\Group;

use Box\Resource\Group;
use Box\Service\AuthenticatedServiceInterface;

interface GroupServiceInterface extends AuthenticatedServiceInterface
{
    public function listGroups(int $limit = 100, int $offset = 0): array;

    public function createGroup(string $name, array $options = []): Group;

    public function getGroup(string $id): Group;

    public function deleteGroup(string $id): void;

    /**
     * @param string|int $groupId
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getGroupMembershipList(string|int $groupId, int|string $limit = 100, int|string $offset = 0): array;

    /**
     * @param string|int $groupId
     * @param int|string $limit
     * @param int|string $offset
     * @return string
     */
    public function getMembershipListUri(string|int $groupId, int|string $limit = 100, int|string $offset = 0): string;

    public function addGroupMember(string $groupId, string $userId, string $role = 'member'): array;

    public function removeGroupMember(string $membershipId): void;
}
