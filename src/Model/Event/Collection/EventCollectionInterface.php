<?php

namespace Box\Model\Event\Collection;

$msg = 'Box\Model\Event\Collection\EventCollectionInterface is depre' . 
 'cated . Use Box\Event\Collection\EventCollectionInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\EventCollectionInterface', __NAMESPACE__ . '\EventCollectionInterface');
