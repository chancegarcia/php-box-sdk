<?php

namespace Box\Model\Service;

@trigger_error('Box\Model\Service\Service is deprecated. Use Box\Service\Service instead.', E_USER_DEPRECATED);

class_alias('\Box\Service\Service', __NAMESPACE__ . '\Service');
