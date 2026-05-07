<?php

declare(strict_types=1);

namespace Box\Enum;

/**
 * Common Box API item types.
 */
enum BoxItemType: string
{
    case File = 'file';
    case Folder = 'folder';
    case User = 'user';
    case Group = 'group';
    case Collaboration = 'collaboration';
    case Event = 'event';
    case Collection = 'collection';
    case WebLink = 'web_link';
}
