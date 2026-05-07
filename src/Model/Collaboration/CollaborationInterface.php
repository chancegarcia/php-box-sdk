<?php

namespace Box\Model\Collaboration;

$msg = 'Box\Model\Collaboration\CollaborationInterface is deprecated . ' . 
 'Use Box\Collaboration\CollaborationInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Collaboration\CollaborationInterface', __NAMESPACE__ . '\CollaborationInterface');
