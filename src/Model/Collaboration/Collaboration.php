<?php

namespace Box\Model\Collaboration;

@trigger_error('Box\Model\Collaboration\Collaboration is deprecated. Use Box\Collaboration\Collaboration instead.', E_USER_DEPRECATED);

class_alias('\Box\Collaboration\Collaboration', __NAMESPACE__ . '\Collaboration');
