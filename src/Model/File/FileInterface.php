<?php

namespace Box\Model\File;

$msg = 'Box\Model\File\FileInterface is deprecated . Use Box\File\Fil' . 
 'eInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\File\FileInterface', __NAMESPACE__ . '\FileInterface');
