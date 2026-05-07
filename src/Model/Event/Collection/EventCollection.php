<?php

namespace Box\Model\Event\Collection;

$msg = 'Box\Model\Event\Collection\EventCollection is deprecated . Us' . 
 'e Box\Event\Collection\EventCollection instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\EventCollection', __NAMESPACE__ . '\EventCollection');
