<?php

namespace Box\Model\User;

$msg = 'Box\Model\User\User is deprecated . Use Box\User\User instead' . 
 ' . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\User\User', __NAMESPACE__ . '\User');
