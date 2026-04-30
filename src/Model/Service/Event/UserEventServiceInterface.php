<?php

namespace Box\Model\Service\Event;

@trigger_error('Box\Model\Service\Event\UserEventServiceInterface is deprecated. Use Box\Service\Event\UserEventServiceInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Service\Event\UserEventServiceInterface', __NAMESPACE__ . '\UserEventServiceInterface');
