<?php

namespace Box\Tests\Factory;

use Box\Factory\UserFactory;
use Box\Resource\User;
use PHPUnit\Framework\TestCase;

class UserFactoryTest extends TestCase
{
    public function testCreateUserReturnsEmptyResourceWhenOptionsIsNull(): void
    {
        $factory = new UserFactory();
        $user = $factory->createUser(null);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->getId());
    }

    public function testCreateUserHydratesWhenOptionsIsProvided(): void
    {
        $factory = new UserFactory();
        $options = [
            'id' => '123',
            'name' => 'test user',
            'login' => 'test@example.com'
        ];
        $user = $factory->createUser($options);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('123', $user->getId());
        $this->assertEquals('test user', $user->getName());
        $this->assertEquals('test@example.com', $user->getLogin());
    }
}
