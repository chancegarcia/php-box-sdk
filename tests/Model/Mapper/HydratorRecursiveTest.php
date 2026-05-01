<?php

namespace Box\Tests\Model\Mapper;

use Box\Mapper\Hydrator;
use Box\Tests\Model\Mapper\Fixtures\Address;
use Box\Tests\Model\Mapper\Fixtures\User;
use PHPUnit\Framework\TestCase;

class HydratorRecursiveTest extends TestCase
{
    public function testRecursiveHydration(): void
    {
        $hydrator = new Hydrator();
        $user = new User();
        $data = [
            'name' => 'John',
            'address' => ['street' => 'Main St']
        ];

        $hydrator->hydrate($user, $data);

        $this->assertEquals('John', $user->name);
        $this->assertInstanceOf(Address::class, $user->address);
        $this->assertEquals('Main St', $user->address->street);
    }
}
