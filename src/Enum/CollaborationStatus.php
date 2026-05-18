<?php

declare(strict_types=1);

namespace Box\Enum;

enum CollaborationStatus: string
{
    case Accepted = 'accepted';
    case Pending  = 'pending';
    case Rejected = 'rejected';
}
