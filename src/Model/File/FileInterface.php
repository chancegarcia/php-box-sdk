<?php

namespace Box\Model\File;

@trigger_error('Box\Model\File\FileInterface is deprecated. Use Box\File\FileInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\File\FileInterface', __NAMESPACE__ . '\FileInterface');
