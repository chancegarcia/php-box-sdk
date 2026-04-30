<?php

namespace Box\Model\Event\Collection\Entry;

@trigger_error('Box\Model\Event\Collection\Entry\AdminEntryInterface is deprecated. Use Box\Event\Collection\Entry\AdminEntryInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\AdminEntryInterface', __NAMESPACE__ . '\AdminEntryInterface');
