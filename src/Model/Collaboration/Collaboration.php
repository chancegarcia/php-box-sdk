<?php

namespace Box\Model\Collaboration;

$msg = 'Box\Model\Collaboration\Collaboration is deprecated . Use Box' . 
 '\Collaboration\Collaboration instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Collaboration\Collaboration', __NAMESPACE__ . '\Collaboration');
