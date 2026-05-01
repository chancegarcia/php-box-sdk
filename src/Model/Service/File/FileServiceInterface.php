<?php

namespace Box\Model\Service\File;

@trigger_error('Box\Model\Service\File\FileServiceInterface is deprecated. Use Box\Service\File\FileServiceInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Service\File\FileServiceInterface', __NAMESPACE__ . '\FileServiceInterface');
