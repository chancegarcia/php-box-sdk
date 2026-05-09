<?php

declare(strict_types=1);

namespace Box\Tests\Resource;

use Box\Enum\UserStatus;
use Box\Mapper\Hydrator;
use Box\Resource\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserResourceAccessors(): void
    {
        $user = new User();

        $user->setId('12345');
        $this->assertSame('12345', $user->getId());

        $user->setName('John Doe');
        $this->assertSame('John Doe', $user->getName());

        $user->setLogin('john@example.com');
        $this->assertSame('john@example.com', $user->getLogin());

        $createdAt = new DateTimeImmutable('2024-05-01T10:00:00Z');
        $user->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $user->getCreatedAt());

        $modifiedAt = new DateTimeImmutable('2024-05-02T11:00:00Z');
        $user->setModifiedAt($modifiedAt);
        $this->assertSame($modifiedAt, $user->getModifiedAt());

        $user->setStatus(UserStatus::Active);
        $this->assertSame(UserStatus::Active, $user->getStatus());

        $user->setLanguage('en');
        $this->assertSame('en', $user->getLanguage());

        $user->setTimezone('America/Los_Angeles');
        $this->assertSame('America/Los_Angeles', $user->getTimezone());

        $user->setSpaceAmount(1000000);
        $this->assertSame(1000000, $user->getSpaceAmount());

        $user->setSpaceUsed(500000);
        $this->assertSame(500000, $user->getSpaceUsed());

        $user->setMaxUploadSize(100000);
        $this->assertSame(100000, $user->getMaxUploadSize());

        $user->setCanSeeManagedUsers(true);
        $this->assertTrue($user->getCanSeeManagedUsers());

        $user->setIsSyncEnabled(true);
        $this->assertTrue($user->getIsSyncEnabled());

        $user->setIsExemptFromDeviceLimits(false);
        $this->assertFalse($user->getIsExemptFromDeviceLimits());

        $user->setIsExemptFromLoginVerification(true);
        $this->assertTrue($user->getIsExemptFromLoginVerification());

        $user->setIsExternalCollabRestricted(false);
        $this->assertFalse($user->getIsExternalCollabRestricted());

        $user->setEnterprise('Test Enterprise');
        $this->assertSame('Test Enterprise', $user->getEnterprise());

        $user->setJobTitle('Developer');
        $this->assertSame('Developer', $user->getJobTitle());

        $user->setPhone('555-1212');
        $this->assertSame('555-1212', $user->getPhone());

        $user->setAddress('123 Main St');
        $this->assertSame('123 Main St', $user->getAddress());

        $user->setAvatarUrl('https://example.com/avatar.png');
        $this->assertSame('https://example.com/avatar.png', $user->getAvatarUrl());

        $user->setRole('user');
        $this->assertSame('user', $user->getRole());

        $trackingCodes = ['code1' => 'value1'];
        $user->setTrackingCodes($trackingCodes);
        $this->assertSame($trackingCodes, $user->getTrackingCodes());

        $user->setType('user');
        $this->assertSame('user', $user->getType());
    }

    public function testUserHydrationFromScalarStatus(): void
    {
        $hydrator = new Hydrator();
        $data = [
            'id' => '12345',
            'status' => 'active',
            'name' => 'John Doe'
        ];

        $user = new User();
        $hydrator->hydrate($user, $data);

        $this->assertSame('12345', $user->getId());
        $this->assertSame('John Doe', $user->getName());
        $this->assertSame(UserStatus::Active, $user->getStatus());
    }
}
