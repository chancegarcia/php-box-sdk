<?php

namespace Box\Model\Event;

$msg = 'Box\Model\Event\Event is deprecated . Use Box\Event\Event ins' . 
 'tead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Event', __NAMESPACE__ . '\Event');
