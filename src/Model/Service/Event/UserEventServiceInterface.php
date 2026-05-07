<?php

namespace Box\Model\Service\Event;

$msg = 'Box\Model\Service\Event\UserEventServiceInterface is depreca' . 
 'ted . Use Box\Service\Event\UserEventServiceInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Service\Event\UserEventServiceInterface', __NAMESPACE__ . '\UserEventServiceInterface');
