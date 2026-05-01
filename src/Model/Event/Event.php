<?php

namespace Box\Model\Event;

@trigger_error('Box\Model\Event\Event is deprecated. Use Box\Event\Event instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Event', __NAMESPACE__ . '\Event');
