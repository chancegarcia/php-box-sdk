<?php

namespace Box\Model\Event\Collection\Entry;

$msg = 'Box\Model\Event\Collection\Entry\UserEntryInterface is depre' . 
 'cated . Use Box\Event\Collection\Entry\UserEntryInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\UserEntryInterface', __NAMESPACE__ . '\UserEntryInterface');
