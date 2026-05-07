<?php

namespace Box\Model\Connection\Response;

$msg = 'Box\Model\Connection\Response\AuthenticationResponse is depr' . 
 'ecated . Use Box\Connection\Response\AuthenticationResponse instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Connection\Response\AuthenticationResponse', __NAMESPACE__ . '\AuthenticationResponse');
