<?php

namespace Box\Model\Group;

$msg = 'Box\Model\Group\Group is deprecated . Use Box\Group\Group ins' . 
 'tead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Group\Group', __NAMESPACE__ . '\Group');
