<?php

namespace Box\Model\Connection\Token;

$msg = 'Box\Model\Connection\Token\TokenInterface is deprecated . Use' . 
 ' Box\Connection\Token\TokenInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Connection\Token\TokenInterface', __NAMESPACE__ . '\TokenInterface');
