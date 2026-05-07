<?php

namespace Box\Factory;

use Box\Collaboration\Collaboration;
use Box\Collaboration\CollaborationInterface;

class CollaborationFactory implements CollaborationFactoryInterface
{
    public function createCollaboration(?array $options = null): CollaborationInterface
    {
        return new Collaboration($options);
    }
}
