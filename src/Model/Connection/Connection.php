<?php

namespace Box\Model\Connection;

@trigger_error('Box\Model\Connection\Connection is deprecated. Use Box\Connection\Connection instead.', E_USER_DEPRECATED);

class_alias('\Box\Connection\Connection', __NAMESPACE__ . '\Connection');
