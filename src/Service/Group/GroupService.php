<?php

namespace Box\Service\Group;

use Box\Service\Service;

class GroupService extends Service implements GroupServiceInterface
{
    public const ENDPOINT = "https://api.box.com/2.0/groups";
    public const MEMBERSHIP_ENDPOINT = "https://api.box.com/2.0/group_memberships";

    /**
     * @inheritdoc
     */
    public function getMembershipListUri(string|int $groupId, int|string $limit = 100, int|string $offset = 0): string
    {
        return self::ENDPOINT . "/" . $groupId . "/memberships" . "?offset=" . $offset . "&limit=" . $limit;
    }
}
