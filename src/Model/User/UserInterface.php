<?php

namespace Box\Model\User;

@trigger_error('Box\Model\User\UserInterface is deprecated. Use Box\User\UserInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\User\UserInterface', __NAMESPACE__ . '\UserInterface');
