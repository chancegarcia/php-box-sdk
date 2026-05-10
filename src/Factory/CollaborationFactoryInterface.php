<?php

namespace Box\Factory;

use Box\Resource\Collaboration;

interface CollaborationFactoryInterface
{
    public function createCollaboration(?array $options = null): Collaboration;
}
