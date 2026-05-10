<?php

namespace Box\Tests\Model;

use Box\Mapper\Hydrator;
use Box\File\File;
use Box\Folder\Folder;
use Box\User\User;
use PHPUnit\Framework\TestCase;

class ModelPropertyTest extends TestCase
{
    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }
    public function testFileNewProperties(): void
    {
        $file = new File();

        $file->setIsExternallyOwned(true);
        $this->assertTrue($file->getIsExternallyOwned());

        $roles = ['editor', 'viewer'];
        $file->setAllowedInviteRoles($roles);
        $this->assertEquals($roles, $file->getAllowedInviteRoles());

        $file->setHasCollaborations(false);
        $this->assertFalse($file->getHasCollaborations());

        $metadata = ['enterprise_123' => ['key' => 'value']];
        $file->setMetadata($metadata);
        $this->assertEquals($metadata, $file->getMetadata());
    }

    public function testUserNewProperties(): void
    {
        $user = new User();

        $user->setTimezone('America/Los_Angeles');
        $this->assertEquals('America/Los_Angeles', $user->getTimezone());

        $user->setIsExternalCollabRestricted(true);
        $this->assertTrue($user->getIsExternalCollabRestricted());
    }

    public function testFolderNewProperties(): void
    {
        $folder = new Folder();

        $folder->setCanNonOwnersInvite(true);
        $this->assertTrue($folder->getCanNonOwnersInvite());
    }

    public function testFileHydrationOfNewProperties(): void
    {
        $file = new File();
        $data = [
            'is_externally_owned' => true,
            'allowed_invite_roles' => ['editor'],
            'has_collaborations' => true,
            'metadata' => ['foo' => 'bar']
        ];

        $this->hydrator->hydrate($file, $data);

        $this->assertTrue($file->getIsExternallyOwned());
        $this->assertEquals(['editor'], $file->getAllowedInviteRoles());
        $this->assertTrue($file->getHasCollaborations());
        $this->assertEquals(['foo' => 'bar'], $file->getMetadata());
    }
}
