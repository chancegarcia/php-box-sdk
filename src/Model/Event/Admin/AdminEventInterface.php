<?php

namespace Box\Model\Event\Admin;

$msg = 'Box\Model\Event\Admin\AdminEventInterface is deprecated . Use' . 
 ' Box\Event\Admin\AdminEventInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Admin\AdminEventInterface', __NAMESPACE__ . '\AdminEventInterface');
