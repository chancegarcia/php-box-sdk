<?php

namespace Box\Model\File;

@trigger_error('Box\Model\File\File is deprecated. Use Box\File\File instead.', E_USER_DEPRECATED);

class_alias('\Box\File\File', __NAMESPACE__ . '\File');
