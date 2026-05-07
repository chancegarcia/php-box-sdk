<?php

namespace Box\Model\Service\File;

$msg = 'Box\Model\Service\File\FileServiceInterface is deprecated . U' . 
 'se Box\Service\File\FileServiceInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Service\File\FileServiceInterface', __NAMESPACE__ . '\FileServiceInterface');
