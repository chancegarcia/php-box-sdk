<?php

namespace Box\Model\Event\Admin;

@trigger_error('Box\Model\Event\Admin\AdminEvent is deprecated. Use Box\Event\Admin\AdminEvent instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Admin\AdminEvent', __NAMESPACE__ . '\AdminEvent');
