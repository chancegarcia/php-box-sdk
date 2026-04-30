<?php

namespace Box\Model\Event;

@trigger_error('Box\Model\Event\EventInterface is deprecated. Use Box\Event\EventInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\EventInterface', __NAMESPACE__ . '\EventInterface');
