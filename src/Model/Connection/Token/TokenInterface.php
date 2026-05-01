<?php

namespace Box\Model\Connection\Token;

@trigger_error('Box\Model\Connection\Token\TokenInterface is deprecated. Use Box\Connection\Token\TokenInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Connection\Token\TokenInterface', __NAMESPACE__ . '\TokenInterface');
