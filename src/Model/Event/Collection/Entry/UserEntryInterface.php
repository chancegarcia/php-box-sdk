<?php

namespace Box\Model\Event\Collection\Entry;

@trigger_error('Box\Model\Event\Collection\Entry\UserEntryInterface is deprecated. Use Box\Event\Collection\Entry\UserEntryInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\UserEntryInterface', __NAMESPACE__ . '\UserEntryInterface');
