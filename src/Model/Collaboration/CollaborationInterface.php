<?php

namespace Box\Model\Collaboration;

@trigger_error('Box\Model\Collaboration\CollaborationInterface is deprecated. Use Box\Collaboration\CollaborationInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Collaboration\CollaborationInterface', __NAMESPACE__ . '\CollaborationInterface');
