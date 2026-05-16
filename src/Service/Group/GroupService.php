<?php

namespace Box\Service\Group;

use Box\Dto\PagedResult;
use Box\Exception\BoxException;
use Box\Exception\BoxResponseException;
use Box\Resource\Group;
use Box\Resource\GroupMembership;
use Box\Service\Service;
use JsonException;

class GroupService extends Service implements GroupServiceInterface
{
    public const string ENDPOINT = "https://api.box.com/2.0/groups";
    public const string MEMBERSHIP_ENDPOINT = "https://api.box.com/2.0/group_memberships";

    /**
     * @throws BoxResponseException
     * @return PagedResult<Group>
     */
    public function listGroups(int $limit = 100, int $offset = 0): PagedResult
    {
        $uri = self::ENDPOINT . '?limit=' . $limit . '&offset=' . $offset;
        $data = $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');

        return $this->hydratePagedResult($data, Group::class);
    }

    /**
     * @throws BoxResponseException
     * @throws JsonException
     */
    public function createGroup(string $name, array $options = []): Group
    {
        $params = array_merge(['name' => $name], $options);
        $uri = self::ENDPOINT;

        $response = $this->getConnection()->post($uri, json_encode($params, JSON_THROW_ON_ERROR));
        $data = $this->handleBoxResponse($response, 'flat');

        return $this->hydrate(Group::class, $data);
    }

    /**
     * @throws BoxException
     */
    public function getGroup(string $id): Group
    {
        $uri = self::ENDPOINT . '/' . $id;

        return $this->getResourceFromBox($uri, Group::class);
    }

    /**
     * @throws BoxResponseException
     */
    public function deleteGroup(string $id): void
    {
        $uri = self::ENDPOINT . '/' . $id;
        $this->sendDeleteToBox($uri);
    }

    /**
     * @throws BoxResponseException
     * @return PagedResult<GroupMembership>
     */
    public function getGroupMembershipList(string|int $groupId, int|string $limit = 100, int|string $offset = 0): PagedResult
    {
        $uri = $this->getMembershipListUri($groupId, $limit, $offset);
        $data = $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');

        return $this->hydratePagedResult($data, GroupMembership::class);
    }

    public function getMembershipListUri(string|int $groupId, int|string $limit = 100, int|string $offset = 0): string
    {
        return self::ENDPOINT . "/" . $groupId . "/memberships" . "?offset=" . $offset . "&limit=" . $limit;
    }

    /**
     * @throws BoxResponseException
     * @throws JsonException
     */
    public function addGroupMember(string $groupId, string $userId, string $role = 'member'): GroupMembership
    {
        $params = [
            'user' => ['id' => $userId],
            'group' => ['id' => $groupId],
            'role' => $role,
        ];

        $response = $this->getConnection()->post(self::MEMBERSHIP_ENDPOINT, json_encode($params, JSON_THROW_ON_ERROR));
        $data = $this->handleBoxResponse($response, 'flat');

        return $this->hydrate(GroupMembership::class, $data);
    }

    /**
     * @throws BoxResponseException
     */
    public function removeGroupMember(string $membershipId): void
    {
        $uri = self::MEMBERSHIP_ENDPOINT . '/' . $membershipId;
        $this->sendDeleteToBox($uri);
    }
}
