<?php

namespace Box\Model\Event\Collection\Entry\Source;

$msg = 'Box\Model\Event\Collection\Entry\Source\EntrySource is depre' . 
 'cated . Use Box\Event\Collection\Entry\Source\EntrySource instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\Source\EntrySource', __NAMESPACE__ . '\EntrySource');
