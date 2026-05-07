<?php

namespace Box\Model\Folder;

$msg = 'Box\Model\Folder\Folder is deprecated . Use Box\Folder\Folder' . 
 ' instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Folder\Folder', __NAMESPACE__ . '\Folder');
