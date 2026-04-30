<?php

namespace Box\Model\Event\Collection;

@trigger_error('Box\Model\Event\Collection\EventCollection is deprecated. Use Box\Event\Collection\EventCollection instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\EventCollection', __NAMESPACE__ . '\EventCollection');
