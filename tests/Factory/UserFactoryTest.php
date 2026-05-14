<?php

declare(strict_types=1);

namespace Box\Tests\Factory;

use Box\Enum\UserStatus;
use Box\Factory\UserFactory;
use Box\Resource\User;
use Box\Tests\Fixtures\BoxApiFixtures;
use DateTimeImmutable;
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

    public function testCreateUserHydratesRealisticApiResponse(): void
    {
        $factory = new UserFactory();
        $user = $factory->createUser(BoxApiFixtures::userResponse());

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('17738362', $user->getId());
        $this->assertSame('Sean Rose', $user->getName());
        $this->assertSame('sean@example.com', $user->getLogin());
        $this->assertSame('en', $user->getLanguage());
        $this->assertSame('Africa/Banjul', $user->getTimezone());
        $this->assertSame(5368709120, $user->getSpaceAmount());
        $this->assertSame(2377016, $user->getSpaceUsed());
        $this->assertSame(UserStatus::Active, $user->getStatus());
        $this->assertSame('Employee', $user->getJobTitle());
        $this->assertSame('555 Box Lane', $user->getAddress());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getModifiedAt());
    }

    public function testCreateUserSupportsOverrides(): void
    {
        $factory = new UserFactory();
        $user = $factory->createUser(BoxApiFixtures::userResponse([
            'id'    => '99999',
            'name'  => 'Override User',
            'login' => 'override@example.com',
        ]));

        $this->assertSame('99999', $user->getId());
        $this->assertSame('Override User', $user->getName());
        $this->assertSame('override@example.com', $user->getLogin());
    }
}
