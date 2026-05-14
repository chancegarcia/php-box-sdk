<?php

namespace Box\Service\Group;

use Box\Resource\Group;
use Box\Service\Service;

class GroupService extends Service implements GroupServiceInterface
{
    public const ENDPOINT = "https://api.box.com/2.0/groups";
    public const MEMBERSHIP_ENDPOINT = "https://api.box.com/2.0/group_memberships";

    public function listGroups(int $limit = 100, int $offset = 0): array
    {
        $uri = self::ENDPOINT . '?limit=' . $limit . '&offset=' . $offset;

        return $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');
    }

    public function createGroup(string $name, array $options = []): Group
    {
        $params = array_merge(['name' => $name], $options);
        $uri = self::ENDPOINT;

        $response = $this->getConnection()->post($uri, json_encode($params));
        $data = $this->handleBoxResponse($response, 'flat');

        return $this->hydrate(Group::class, $data);
    }

    public function getGroup(string $id): Group
    {
        $uri = self::ENDPOINT . '/' . $id;

        return $this->getResourceFromBox($uri, Group::class);
    }

    public function deleteGroup(string $id): void
    {
        $uri = self::ENDPOINT . '/' . $id;
        $this->sendDeleteToBox($uri);
    }

    public function getGroupMembershipList(string|int $groupId, int|string $limit = 100, int|string $offset = 0): array
    {
        $uri = $this->getMembershipListUri($groupId, $limit, $offset);

        return $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');
    }

    /**
     * @inheritdoc
     */
    public function getMembershipListUri(string|int $groupId, int|string $limit = 100, int|string $offset = 0): string
    {
        return self::ENDPOINT . "/" . $groupId . "/memberships" . "?offset=" . $offset . "&limit=" . $limit;
    }

    public function addGroupMember(string $groupId, string $userId, string $role = 'member'): array
    {
        $params = [
            'user' => ['id' => $userId],
            'group' => ['id' => $groupId],
            'role' => $role,
        ];

        $response = $this->getConnection()->post(self::MEMBERSHIP_ENDPOINT, json_encode($params));

        return $this->handleBoxResponse($response, 'flat');
    }

    public function removeGroupMember(string $membershipId): void
    {
        $uri = self::MEMBERSHIP_ENDPOINT . '/' . $membershipId;
        $this->sendDeleteToBox($uri);
    }
}
