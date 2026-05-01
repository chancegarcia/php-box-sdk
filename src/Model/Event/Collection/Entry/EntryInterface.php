<?php

namespace Box\Model\Event\Collection\Entry;

@trigger_error('Box\Model\Event\Collection\Entry\EntryInterface is deprecated. Use Box\Event\Collection\Entry\EntryInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\EntryInterface', __NAMESPACE__ . '\EntryInterface');
