<?php

namespace Box\Model\Item\SharedLink;

@trigger_error('Box\Model\Item\SharedLink\SharedLinkInterface is deprecated. Use Box\Item\SharedLink\SharedLinkInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Item\SharedLink\SharedLinkInterface', __NAMESPACE__ . '\SharedLinkInterface');
