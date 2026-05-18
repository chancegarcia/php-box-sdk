<?php

declare(strict_types=1);

namespace Box\Tests\Factory;

use Box\Factory\GroupFactory;
use Box\Resource\Group;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;

class GroupFactoryTest extends TestCase
{
    public function testCreateGroupReturnsEmptyResourceWhenOptionsIsNull(): void
    {
        $factory = new GroupFactory();
        $group = $factory->createGroup(null);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertNull($group->getId());
    }

    public function testCreateGroupHydratesRealisticApiResponse(): void
    {
        $factory = new GroupFactory();
        $group = $factory->createGroup(BoxApiFixtures::groupResponse());

        $this->assertInstanceOf(Group::class, $group);
        $this->assertSame('189108', $group->getId());
        $this->assertSame('All employees', $group->getName());
        $this->assertSame('2013-05-16T15:27:16-07:00', $group->getCreatedAt());
        $this->assertSame('2013-05-16T15:27:16-07:00', $group->getModifiedAt());
    }

    public function testCreateGroupSupportsOverrides(): void
    {
        $factory = new GroupFactory();
        $group = $factory->createGroup(BoxApiFixtures::groupResponse(['id' => '200001', 'name' => 'Managers']));

        $this->assertSame('200001', $group->getId());
        $this->assertSame('Managers', $group->getName());
    }
}
