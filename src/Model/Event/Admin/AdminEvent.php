<?php

namespace Box\Model\Event\Admin;

$msg = 'Box\Model\Event\Admin\AdminEvent is deprecated . Use Box\Even' . 
 't\Admin\AdminEvent instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Admin\AdminEvent', __NAMESPACE__ . '\AdminEvent');
