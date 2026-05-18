<?php

declare(strict_types=1);

namespace Box\Tests\Service\Group;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Http\Response\BoxResponseInterface;
use Box\Dto\PagedResult;
use Box\Resource\Group;
use Box\Resource\GroupMembership;
use Box\Service\Group\GroupService;
use Box\Tests\Fixtures\BoxApiFixtures;
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

        $this->assertEquals(
            GroupService::ENDPOINT . '/98765/memberships?offset=5&limit=20',
            $service->getMembershipListUri('98765', 20, 5)
        );
    }

    public function testGetMembershipListUriDefaults(): void
    {
        $service = new GroupService();

        $this->assertEquals(
            GroupService::ENDPOINT . '/98765/memberships?offset=0&limit=100',
            $service->getMembershipListUri(98765)
        );
    }

    public function testListGroupsReturnsPagedResult(): void
    {
        $listData = BoxApiFixtures::groupListResponse();

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(GroupService::ENDPOINT . '?limit=100&offset=0')
            ->willReturn($this->createMockResponse($listData));

        $result = $this->createService($connection)->listGroups();

        $this->assertInstanceOf(PagedResult::class, $result);
        $this->assertSame(2, $result->totalCount);
        $this->assertCount(2, $result->entries);
        $this->assertContainsOnlyInstancesOf(Group::class, $result->entries);
    }

    public function testCreateGroupReturnsGroupResource(): void
    {
        $groupData = BoxApiFixtures::groupResponse(['id' => '189108', 'name' => 'Engineering']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(GroupService::ENDPOINT, $this->callback(fn($b) => str_contains($b, 'Engineering')))
            ->willReturn($this->createMockResponse($groupData));

        $result = $this->createService($connection)->createGroup('Engineering');

        $this->assertInstanceOf(Group::class, $result);
        $this->assertSame('189108', $result->getId());
        $this->assertSame('Engineering', $result->getName());
        $this->assertSame('2013-05-16T15:27:16-07:00', $result->getCreatedAt());
    }

    public function testGetGroupReturnsGroupResource(): void
    {
        $groupId = '189108';
        $groupData = BoxApiFixtures::groupResponse(['id' => $groupId]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(GroupService::ENDPOINT . '/' . $groupId)
            ->willReturn($this->createMockResponse($groupData));

        $result = $this->createService($connection)->getGroup($groupId);

        $this->assertInstanceOf(Group::class, $result);
        $this->assertSame($groupId, $result->getId());
        $this->assertSame('All employees', $result->getName());
        $this->assertSame('2013-05-16T15:27:16-07:00', $result->getModifiedAt());
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
            ->with(GroupService::ENDPOINT . '/189108')
            ->willReturn($deleteResponse);

        $this->createService($connection)->deleteGroup('189108');
        $this->addToAssertionCount(1);
    }

    public function testAddGroupMemberReturnsGroupMembership(): void
    {
        $membershipData = BoxApiFixtures::groupMembershipResponse();

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(
                GroupService::MEMBERSHIP_ENDPOINT,
                $this->callback(fn($b) => str_contains($b, '755492') && str_contains($b, '189108'))
            )
            ->willReturn($this->createMockResponse($membershipData));

        $result = $this->createService($connection)->addGroupMember('189108', '755492');

        $this->assertInstanceOf(GroupMembership::class, $result);
        $this->assertSame('1560354', $result->getId());
        $this->assertSame('member', $result->getRole());
    }

    public function testGetGroupMembershipListReturnsPagedResult(): void
    {
        $listData = BoxApiFixtures::groupMembershipListResponse();

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with($this->stringContains('/189108/memberships'))
            ->willReturn($this->createMockResponse($listData));

        $result = $this->createService($connection)->getGroupMembershipList('189108');

        $this->assertInstanceOf(PagedResult::class, $result);
        $this->assertSame(2, $result->totalCount);
        $this->assertCount(2, $result->entries);
        $this->assertContainsOnlyInstancesOf(GroupMembership::class, $result->entries);
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
            ->with(GroupService::MEMBERSHIP_ENDPOINT . '/1560354')
            ->willReturn($deleteResponse);

        $this->createService($connection)->removeGroupMember('1560354');
        $this->addToAssertionCount(1);
    }
}
