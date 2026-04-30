<?php

namespace Box\Model\Connection\Response;

@trigger_error('Box\Model\Connection\Response\AuthenticationResponse is deprecated. Use Box\Connection\Response\AuthenticationResponse instead.', E_USER_DEPRECATED);

class_alias('\Box\Connection\Response\AuthenticationResponse', __NAMESPACE__ . '\AuthenticationResponse');
