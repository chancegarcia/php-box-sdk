<?php

namespace Box\Model\Service\File;

$msg = 'Box\Model\Service\File\FileService is deprecated . Use Box\Se' . 
 'rvice\File\FileService instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Service\File\FileService', __NAMESPACE__ . '\FileService');
