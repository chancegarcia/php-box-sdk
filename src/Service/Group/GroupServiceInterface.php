<?php

namespace Box\Service\Group;

use Box\Service\AuthenticatedServiceInterface;

interface GroupServiceInterface extends AuthenticatedServiceInterface
{
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
}
