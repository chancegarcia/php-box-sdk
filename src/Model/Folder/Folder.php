<?php

namespace Box\Model\Folder;

@trigger_error('Box\Model\Folder\Folder is deprecated. Use Box\Folder\Folder instead.', E_USER_DEPRECATED);

class_alias('\Box\Folder\Folder', __NAMESPACE__ . '\Folder');
