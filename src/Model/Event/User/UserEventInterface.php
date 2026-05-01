<?php

namespace Box\Model\Event\User;

@trigger_error('Box\Model\Event\User\UserEventInterface is deprecated. Use Box\Event\User\UserEventInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\User\UserEventInterface', __NAMESPACE__ . '\UserEventInterface');
