<?php

namespace Box\Model\Event\Admin;

@trigger_error('Box\Model\Event\Admin\AdminEventInterface is deprecated. Use Box\Event\Admin\AdminEventInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Admin\AdminEventInterface', __NAMESPACE__ . '\AdminEventInterface');
