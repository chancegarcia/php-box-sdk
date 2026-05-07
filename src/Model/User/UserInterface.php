<?php

namespace Box\Model\User;

$msg = 'Box\Model\User\UserInterface is deprecated . Use Box\User\Use' . 
 'rInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\User\UserInterface', __NAMESPACE__ . '\UserInterface');
