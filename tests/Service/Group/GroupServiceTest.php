<?php

namespace Box\Tests\Service\Group;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\Group;
use Box\Service\Group\GroupService;
use PHPUnit\Framework\TestCase;

class GroupServiceTest extends TestCase
{
    private function createService(ConnectionInterface $connection): GroupService
    {
        $service = new GroupService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    private function createMockResponse(array $data, int $status = 200): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn($status >= 200 && $status < 300);
        $response->method('getContent')->willReturn($data ? json_encode($data) : '');
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object)$data);
        return $response;
    }

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

    public function testListGroupsReturnsArray(): void
    {
        $listData = [
            'total_count' => 1,
            'entries' => [['type' => 'group', 'id' => '11', 'name' => 'Admins']],
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(GroupService::ENDPOINT . '?limit=100&offset=0')
            ->willReturn($this->createMockResponse($listData));

        $result = $this->createService($connection)->listGroups();

        $this->assertIsArray($result);
        $this->assertSame(1, $result['total_count']);
    }

    public function testCreateGroupReturnsGroupResource(): void
    {
        $groupData = ['type' => 'group', 'id' => '22', 'name' => 'Engineering'];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(GroupService::ENDPOINT, $this->callback(fn($b) => str_contains($b, 'Engineering')))
            ->willReturn($this->createMockResponse($groupData));

        $result = $this->createService($connection)->createGroup('Engineering');

        $this->assertInstanceOf(Group::class, $result);
        $this->assertSame('22', $result->getId());
        $this->assertSame('Engineering', $result->getName());
    }

    public function testGetGroupReturnsGroupResource(): void
    {
        $groupData = ['type' => 'group', 'id' => '33', 'name' => 'Finance'];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(GroupService::ENDPOINT . '/33')
            ->willReturn($this->createMockResponse($groupData));

        $result = $this->createService($connection)->getGroup('33');

        $this->assertInstanceOf(Group::class, $result);
        $this->assertSame('33', $result->getId());
    }

    public function testDeleteGroupCallsDelete(): void
    {
        $deleteResponse = $this->createMock(BoxResponseInterface::class);
        $deleteResponse->method('isSuccessful')->willReturn(true);
        $deleteResponse->method('json')->willReturn([]);
        $deleteResponse->method('getContent')->willReturn('');

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(GroupService::ENDPOINT . '/44')
            ->willReturn($deleteResponse);

        $this->createService($connection)->deleteGroup('44');
        $this->addToAssertionCount(1);
    }

    public function testAddGroupMemberReturnsArray(): void
    {
        $membershipData = [
            'type' => 'group_membership',
            'id' => 'mem-1',
            'role' => 'member',
        ];

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(
                GroupService::MEMBERSHIP_ENDPOINT,
                $this->callback(fn($b) => str_contains($b, 'user-id-1') && str_contains($b, 'group-id-1'))
            )
            ->willReturn($this->createMockResponse($membershipData));

        $result = $this->createService($connection)->addGroupMember('group-id-1', 'user-id-1');

        $this->assertIsArray($result);
        $this->assertSame('group_membership', $result['type']);
    }

    public function testRemoveGroupMemberCallsDelete(): void
    {
        $deleteResponse = $this->createMock(BoxResponseInterface::class);
        $deleteResponse->method('isSuccessful')->willReturn(true);
        $deleteResponse->method('json')->willReturn([]);
        $deleteResponse->method('getContent')->willReturn('');

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('delete')
            ->with(GroupService::MEMBERSHIP_ENDPOINT . '/mem-99')
            ->willReturn($deleteResponse);

        $this->createService($connection)->removeGroupMember('mem-99');
        $this->addToAssertionCount(1);
    }
}
