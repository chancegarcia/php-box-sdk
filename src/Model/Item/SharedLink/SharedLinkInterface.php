<?php

namespace Box\Model\Item\SharedLink;

$msg = 'Box\Model\Item\SharedLink\SharedLinkInterface is deprecated . ' . 
 ' Use Box\Item\SharedLink\SharedLinkInterface instead . ';
@trigger_error($msg, E_USER_DEPRECATED);

class_alias('\Box\Item\SharedLink\SharedLinkInterface', __NAMESPACE__ . '\SharedLinkInterface');
