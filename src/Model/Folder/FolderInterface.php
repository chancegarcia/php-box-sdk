<?php

namespace Box\Model\Folder;

$msg = 'Box\Model\Folder\FolderInterface is deprecated . Use Box\Fold' . 
 'er\FolderInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Folder\FolderInterface', __NAMESPACE__ . '\FolderInterface');
