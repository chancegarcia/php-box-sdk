<?php

namespace Box\Model\Service\File;

@trigger_error('Box\Model\Service\File\FileService is deprecated. Use Box\Service\File\FileService instead.', E_USER_DEPRECATED);

class_alias('\Box\Service\File\FileService', __NAMESPACE__ . '\FileService');
