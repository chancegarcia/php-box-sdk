<?php

namespace Box\Tests\Service\Group;

use Box\Service\Group\GroupService;
use PHPUnit\Framework\TestCase;

class GroupServiceTest extends TestCase
{
    public function testGetMembershipListUri(): void
    {
        $service = new GroupService();
        $groupId = '98765';
        $limit = 20;
        $offset = 5;

        $expectedUri = GroupService::ENDPOINT . "/98765/memberships?offset=5&limit=20";
        $this->assertEquals($expectedUri, $service->getMembershipListUri($groupId, $limit, $offset));
    }

    public function testGetMembershipListUriDefaults(): void
    {
        $service = new GroupService();
        $groupId = 98765;

        $expectedUri = GroupService::ENDPOINT . "/98765/memberships?offset=0&limit=100";
        $this->assertEquals($expectedUri, $service->getMembershipListUri($groupId));
    }
}
