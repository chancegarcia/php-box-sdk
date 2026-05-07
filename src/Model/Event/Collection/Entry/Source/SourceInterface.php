<?php

namespace Box\Model\Event\Collection\Entry\Source;

$msg = 'Box\Model\Event\Collection\Entry\Source\SourceInterface is d' . 
 'eprecated . Use Box\Event\Collection\Entry\Source\SourceInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\Source\SourceInterface', __NAMESPACE__ . '\SourceInterface');
