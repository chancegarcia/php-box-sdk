<?php

namespace Box\Model\Service;

$msg = 'Box\Model\Service\ServiceInterface is deprecated . Use Box\Se' . 
 'rvice\ServiceInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Service\ServiceInterface', __NAMESPACE__ . '\ServiceInterface');
