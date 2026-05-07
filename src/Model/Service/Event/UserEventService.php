<?php

namespace Box\Model\Service\Event;

$msg = 'Box\Model\Service\Event\UserEventService is deprecated . Use ' . 
 'Box\Service\Event\UserEventService instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Service\Event\UserEventService', __NAMESPACE__ . '\UserEventService');
