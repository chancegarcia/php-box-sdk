<?php

namespace Box\Model\Group;

@trigger_error('Box\Model\Group\Group is deprecated. Use Box\Group\Group instead.', E_USER_DEPRECATED);

class_alias('\Box\Group\Group', __NAMESPACE__ . '\Group');
