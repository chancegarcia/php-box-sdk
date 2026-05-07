<?php

namespace Box\Model\Service;

$msg = 'Box\Model\Service\Service is deprecated . Use Box\Service\Ser' . 
 'vice instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Service\Service', __NAMESPACE__ . '\Service');
