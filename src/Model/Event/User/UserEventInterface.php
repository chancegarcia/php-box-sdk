<?php

namespace Box\Model\Event\User;

$msg = 'Box\Model\Event\User\UserEventInterface is deprecated . Use B' . 
 'ox\Event\User\UserEventInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\User\UserEventInterface', __NAMESPACE__ . '\UserEventInterface');
