<?php

namespace Box\Tests\Factory;

use Box\Factory\GroupFactory;
use Box\Resource\Group;
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

    public function testCreateGroupHydratesWhenOptionsIsProvided(): void
    {
        $factory = new GroupFactory();
        $options = [
            'id' => '789',
            'name' => 'test group'
        ];
        $group = $factory->createGroup($options);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals('789', $group->getId());
        $this->assertEquals('test group', $group->getName());
    }
}
