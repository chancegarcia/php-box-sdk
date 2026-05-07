<?php

namespace Box\Model\Connection;

$msg = 'Box\Model\Connection\Connection is deprecated . Use Box\Conne' . 
 'ction\Connection instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Connection\Connection', __NAMESPACE__ . '\Connection');
