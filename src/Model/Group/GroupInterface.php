<?php

namespace Box\Model\Group;

@trigger_error('Box\Model\Group\GroupInterface is deprecated. Use Box\Group\GroupInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Group\GroupInterface', __NAMESPACE__ . '\GroupInterface');
