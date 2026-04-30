<?php

namespace Box\Model\User;

@trigger_error('Box\Model\User\User is deprecated. Use Box\User\User instead.', E_USER_DEPRECATED);

class_alias('\Box\User\User', __NAMESPACE__ . '\User');
