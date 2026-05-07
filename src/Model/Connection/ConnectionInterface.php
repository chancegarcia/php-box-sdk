<?php

namespace Box\Model\Connection;

$msg = 'Box\Model\Connection\ConnectionInterface is deprecated . Use ' . 
 'Box\Connection\ConnectionInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Connection\ConnectionInterface', __NAMESPACE__ . '\ConnectionInterface');
