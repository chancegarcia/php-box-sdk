<?php

namespace Box\Model\Event;

$msg = 'Box\Model\Event\EventInterface is deprecated. Use Box\Event\EventInterface instead.';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\EventInterface', __NAMESPACE__ . '\EventInterface');
