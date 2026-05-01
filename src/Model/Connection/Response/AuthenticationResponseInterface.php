<?php

namespace Box\Model\Connection\Response;

@trigger_error('Box\Model\Connection\Response\AuthenticationResponseInterface is deprecated. Use Box\Connection\Response\AuthenticationResponseInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Connection\Response\AuthenticationResponseInterface', __NAMESPACE__ . '\AuthenticationResponseInterface');
