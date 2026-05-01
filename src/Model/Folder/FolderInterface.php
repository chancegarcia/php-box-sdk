<?php

namespace Box\Model\Folder;

@trigger_error('Box\Model\Folder\FolderInterface is deprecated. Use Box\Folder\FolderInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Folder\FolderInterface', __NAMESPACE__ . '\FolderInterface');
