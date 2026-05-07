<?php

namespace Box\Model\Group;

$msg = 'Box\Model\Group\GroupInterface is deprecated . Use Box\Group\\' . 
 'GroupInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Group\GroupInterface', __NAMESPACE__ . '\GroupInterface');
