<?php

namespace Box\Model\Item\SharedLink\Permissions;

$msg = 'Box\Model\Item\SharedLink\Permissions\PermissionsInterface i' . 
 's deprecated . Use Box\Item\SharedLink\Permissions\PermissionsInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Item\SharedLink\Permissions\PermissionsInterface', __NAMESPACE__ . '\PermissionsInterface');
