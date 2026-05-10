<?php

declare(strict_types=1);

namespace Box\Tests\Resource;

use Box\Resource\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UserMigrationTest extends TestCase
{
    public function testUserResourceIsIndependentOfLegacyModels(): void
    {
        $reflection = new ReflectionClass(User::class);

        // Assert it doesn't extend legacy BaseModel or other models
        $parent = $reflection->getParentClass();
        $this->assertFalse($parent, 'User resource should not have a parent class.');

        // Assert it doesn't use legacy traits
        $traits = $reflection->getTraitNames();
        $this->assertEmpty($traits, 'User resource should not use any traits.');
    }

    public function testUserResourceDoesNotImplementRemovedInterfaces(): void
    {
        $reflection = new ReflectionClass(User::class);
        $interfaces = $reflection->getInterfaceNames();

        if (empty($interfaces)) {
            $this->assertEmpty($interfaces);
            return;
        }

        foreach ($interfaces as $interface) {
            $this->assertStringNotContainsString('UserInterface', $interface, 'User resource should not implement UserInterface.');
        }
    }

    public function testUserInterfaceIsRemoved(): void
    {
        // Check if UserInterface exists in common locations
        $this->assertFalse(interface_exists('Box\User\UserInterface'));
        $this->assertFalse(interface_exists('Box\Model\User\UserInterface'));
    }

    public function testLegacyUserClassIsRemoved(): void
    {
        $this->assertFalse(class_exists('Box\User\User'), 'Legacy Box\User\User class should be removed in v1.0.');
    }
}
