<?php

namespace Box\Model\Service;

@trigger_error('Box\Model\Service\ServiceInterface is deprecated. Use Box\Service\ServiceInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Service\ServiceInterface', __NAMESPACE__ . '\ServiceInterface');
