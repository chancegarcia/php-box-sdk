<?php

namespace Box\Tests\Model\Mapper;

use Box\Mapper\Hydrator;
use Box\Tests\Model\Mapper\Fixtures\Group;
use Box\Tests\Model\Mapper\Fixtures\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class HydratorComplexTest extends TestCase
{
    public function testCollectionHydration(): void
    {
        $hydrator = new Hydrator();
        $group = new Group();
        $data = [
            'name' => 'Admins',
            'users' => [
                ['name' => 'Alice', 'address' => ['street' => '1st Ave']],
                ['name' => 'Bob', 'address' => ['street' => '2nd Ave']]
            ]
        ];

        $hydrator->hydrate($group, $data);

        $this->assertEquals('Admins', $group->name);
        $this->assertInstanceOf(Collection::class, $group->users);
        $this->assertCount(2, $group->users);

        $this->assertInstanceOf(User::class, $group->users[0]);
        $this->assertEquals('Alice', $group->users[0]->name);
        $this->assertEquals('1st Ave', $group->users[0]->address->street);

        $this->assertInstanceOf(User::class, $group->users[1]);
        $this->assertEquals('Bob', $group->users[1]->name);
        $this->assertEquals('2nd Ave', $group->users[1]->address->street);
    }
}
