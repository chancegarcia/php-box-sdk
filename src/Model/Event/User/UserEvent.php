<?php

namespace Box\Model\Event\User;

@trigger_error('Box\Model\Event\User\UserEvent is deprecated. Use Box\Event\User\UserEvent instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\User\UserEvent', __NAMESPACE__ . '\UserEvent');
