<?php

namespace Box\Model\Item\SharedLink\Permissions;

@trigger_error('Box\Model\Item\SharedLink\Permissions\PermissionsInterface is deprecated. Use Box\Item\SharedLink\Permissions\PermissionsInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Item\SharedLink\Permissions\PermissionsInterface', __NAMESPACE__ . '\PermissionsInterface');
