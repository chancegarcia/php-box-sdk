<?php

namespace Box\Model\Event\Collection\Entry;

$msg = 'Box\Model\Event\Collection\Entry\AdminEntryInterface is depr' . 
 'ecated . Use Box\Event\Collection\Entry\AdminEntryInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\AdminEntryInterface', __NAMESPACE__ . '\AdminEntryInterface');
