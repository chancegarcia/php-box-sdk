<?php

namespace Box\Model\Event\Collection\Entry\Source;

@trigger_error('Box\Model\Event\Collection\Entry\Source\SourceInterface is deprecated. Use Box\Event\Collection\Entry\Source\SourceInterface instead.', E_USER_DEPRECATED);

class_alias('\Box\Event\Collection\Entry\Source\SourceInterface', __NAMESPACE__ . '\SourceInterface');
