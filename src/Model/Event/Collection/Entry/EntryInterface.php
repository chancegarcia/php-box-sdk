<?php

namespace Box\Model\Event\Collection\Entry;

$msg = 'Box\Model\Event\Collection\Entry\EntryInterface is deprecate' . 
 'd . Use Box\Event\Collection\Entry\EntryInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\EntryInterface', __NAMESPACE__ . '\EntryInterface');
