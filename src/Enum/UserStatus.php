<?php

declare(strict_types=1);

namespace Box\Enum;

/**
 * Box User status values.
 */
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case CannotDeleteEdit = 'cannot_delete_edit';
    case CannotDeleteEditUpload = 'cannot_delete_edit_upload';
}
