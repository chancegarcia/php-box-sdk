<?php

namespace Box\Model\Event\User;

$msg = 'Box\Model\Event\User\UserEvent is deprecated. Use Box\Event\User\UserEvent instead.';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\User\UserEvent', __NAMESPACE__ . '\UserEvent');
