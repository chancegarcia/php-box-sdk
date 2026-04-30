<?php

namespace Box\Model\Service\Event;

@trigger_error('Box\Model\Service\Event\UserEventService is deprecated. Use Box\Service\Event\UserEventService instead.', E_USER_DEPRECATED);

class_alias('\Box\Service\Event\UserEventService', __NAMESPACE__ . '\UserEventService');
