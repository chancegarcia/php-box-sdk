<?php

namespace Box\Model\Connection\Response;

$msg = 'Box\Model\Connection\Response\AuthenticationResponseInterfac' . 
 'e is deprecated . Use Box\Connection\Response\AuthenticationResponseInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Connection\Response\AuthenticationResponseInterface', __NAMESPACE__ . '\AuthenticationResponseInterface');
