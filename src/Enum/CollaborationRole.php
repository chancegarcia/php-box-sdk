<?php

declare(strict_types=1);

namespace Box\Enum;

/**
 * Box Collaboration roles.
 */
enum CollaborationRole: string
{
    case Editor = 'editor';
    case Viewer = 'viewer';
    case Previewer = 'previewer';
    case Uploader = 'uploader';
    case PreviewerUploader = 'previewer uploader';
    case ViewerUploader = 'viewer uploader';
    case CoOwner = 'co-owner';
    case Owner = 'owner';
}
