<?php

namespace Box\Model\Connection;

@trigger_error('Box\Model\Connection\ConnectionInterface is deprecated. Use Box\Connection\ConnectionInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Connection\ConnectionInterface', __NAMESPACE__ . '\ConnectionInterface');
