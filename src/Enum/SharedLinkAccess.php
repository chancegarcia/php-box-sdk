<?php

declare(strict_types=1);

namespace Box\Enum;

/**
 * Box Shared Link access levels.
 */
enum SharedLinkAccess: string
{
    case Open = 'open';
    case Company = 'company';
    case Collaborators = 'collaborators';
}
