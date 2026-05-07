<?php

namespace Box\Model\Connection\Token;

$msg = 'Box\Model\Connection\Token\Token is deprecated . Use Box\Conn' . 
 'ection\Token\Token instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Connection\Token\Token', __NAMESPACE__ . '\Token');
