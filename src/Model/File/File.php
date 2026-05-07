<?php

namespace Box\Model\File;

$msg = 'Box\Model\File\File is deprecated . Use Box\File\File instead' . 
 ' . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\File\File', __NAMESPACE__ . '\File');
