<?php

namespace Box\Model\Event\Collection;

@trigger_error('Box\Model\Event\Collection\EventCollectionInterface is deprecated. Use Box\Event\Collection\EventCollectionInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\EventCollectionInterface', __NAMESPACE__ . '\EventCollectionInterface');
