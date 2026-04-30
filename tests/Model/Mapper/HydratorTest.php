<?php

namespace Box\Tests\Model\Mapper;

use Box\Mapper\Hydrator;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    public function testSimpleScalarHydration(): void
    {
        $target = new class {
            public string $name;
            public int $age;
        };

        $data = ['name' => 'John', 'age' => 30];
        $this->hydrator->hydrate($target, $data);

        $this->assertEquals('John', $target->name);
        $this->assertEquals(30, $target->age);
    }

    public function testSnakeToCamelHydration(): void
    {
        $target = new class {
            public string $firstName;
        };

        $data = ['first_name' => 'John'];
        $this->hydrator->hydrate($target, $data);

        $this->assertEquals('John', $target->firstName);
    }

    public function testSetterHydration(): void
    {
        $target = new class {
            private string $name;
            public function setName(string $name): void { $this->name = $name; }
            public function getName(): string { return $this->name; }
        };

        $data = ['name' => 'John'];
        $this->hydrator->hydrate($target, $data);

        $this->assertEquals('John', $target->getName());
    }

    public function testCollectionHydration(): void
    {
        $hydrator = new Hydrator();
        $group = new Group();
        $data = [
            'name' => 'Admins',
            'users' => [
                ['name' => 'Alice'],
                ['name' => 'Bob']
            ]
        ];

        $hydrator->hydrate($group, $data);

        $this->assertEquals('Admins', $group->name);
        $this->assertCount(2, $group->users);
        $this->assertInstanceOf(User::class, $group->users[0]);
        $this->assertEquals('Alice', $group->users[0]->name);
        $this->assertEquals('Bob', $group->users[1]->name);
    }
}

class Address { public string $street; }
class User {
    public string $name;
    public ?Address $address = null;
}
class Group {
    public string $name;
    /** @var \Box\Tests\Model\Mapper\User[] */
    public \Doctrine\Common\Collections\Collection $users;
}

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
