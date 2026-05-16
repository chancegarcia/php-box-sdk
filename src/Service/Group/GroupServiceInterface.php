<?php

namespace Box\Service\Group;

use Box\Dto\PagedResult;
use Box\Exception\BoxResponseException;
use Box\Resource\Group;
use Box\Resource\GroupMembership;
use Box\Service\AuthenticatedServiceInterface;

interface GroupServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return PagedResult<Group>
     */
    public function listGroups(int $limit = 100, int $offset = 0): PagedResult;

    /**
     * @throws BoxResponseException
     * @throws \JsonException
     */
    public function createGroup(string $name, array $options = []): Group;

    public function getGroup(string $id): Group;

    public function deleteGroup(string $id): void;

    /**
     * @param string|int $groupId
     * @param int|string $limit
     * @param int|string $offset
     *
     * @return PagedResult<GroupMembership>
     */
    public function getGroupMembershipList(string|int $groupId, int|string $limit = 100, int|string $offset = 0): PagedResult;

    /**
     * @param string|int $groupId
     * @param int|string $limit
     * @param int|string $offset
     *
     * @return string
     */
    public function getMembershipListUri(string|int $groupId, int|string $limit = 100, int|string $offset = 0): string;

    /**
     * @throws BoxResponseException
     * @throws \JsonException
     */
    public function addGroupMember(string $groupId, string $userId, string $role = 'member'): GroupMembership;

    public function removeGroupMember(string $membershipId): void;
}
