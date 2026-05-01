<?php

namespace Box\Model\Connection\Token;

@trigger_error('Box\Model\Connection\Token\Token is deprecated. Use Box\Connection\Token\Token instead.', E_USER_DEPRECATED);

class_alias('\Box\Connection\Token\Token', __NAMESPACE__ . '\Token');
