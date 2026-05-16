<?php

declare(strict_types=1);

namespace Box\Tests\Enum;

use Box\Enum\BoxItemType;
use Box\Enum\CollaborationRole;
use Box\Enum\CollaborationStatus;
use Box\Enum\SharedLinkAccess;
use Box\Enum\UserStatus;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testBoxItemTypeValues(): void
    {
        $this->assertSame('file', BoxItemType::File->value);
        $this->assertSame('folder', BoxItemType::Folder->value);
        $this->assertSame('user', BoxItemType::User->value);
        $this->assertSame('group', BoxItemType::Group->value);
        $this->assertSame('collaboration', BoxItemType::Collaboration->value);
        $this->assertSame('event', BoxItemType::Event->value);
        $this->assertSame('collection', BoxItemType::Collection->value);
        $this->assertSame('web_link', BoxItemType::WebLink->value);
    }

    public function testUserStatusValues(): void
    {
        $this->assertSame('active', UserStatus::Active->value);
        $this->assertSame('inactive', UserStatus::Inactive->value);
        $this->assertSame('cannot_delete_edit', UserStatus::CannotDeleteEdit->value);
        $this->assertSame('cannot_delete_edit_upload', UserStatus::CannotDeleteEditUpload->value);
    }

    public function testCollaborationRoleValues(): void
    {
        $this->assertSame('editor', CollaborationRole::Editor->value);
        $this->assertSame('viewer', CollaborationRole::Viewer->value);
        $this->assertSame('previewer', CollaborationRole::Previewer->value);
        $this->assertSame('uploader', CollaborationRole::Uploader->value);
        $this->assertSame('previewer uploader', CollaborationRole::PreviewerUploader->value);
        $this->assertSame('viewer uploader', CollaborationRole::ViewerUploader->value);
        $this->assertSame('co-owner', CollaborationRole::CoOwner->value);
        $this->assertSame('owner', CollaborationRole::Owner->value);
    }

    public function testSharedLinkAccessValues(): void
    {
        $this->assertSame('open', SharedLinkAccess::Open->value);
        $this->assertSame('company', SharedLinkAccess::Company->value);
        $this->assertSame('collaborators', SharedLinkAccess::Collaborators->value);
    }

    public function testCollaborationStatusValues(): void
    {
        $this->assertSame('accepted', CollaborationStatus::Accepted->value);
        $this->assertSame('pending', CollaborationStatus::Pending->value);
        $this->assertSame('rejected', CollaborationStatus::Rejected->value);
    }

    public function testEnumFromValue(): void
    {
        $this->assertSame(BoxItemType::File, BoxItemType::from('file'));
        $this->assertSame(UserStatus::Active, UserStatus::from('active'));
        $this->assertSame(CollaborationRole::Editor, CollaborationRole::from('editor'));
        $this->assertSame(CollaborationStatus::Accepted, CollaborationStatus::from('accepted'));
        $this->assertSame(SharedLinkAccess::Open, SharedLinkAccess::from('open'));
    }
}
