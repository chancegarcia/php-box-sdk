<?php

namespace Box\Factory;

use Box\Resource\Collaboration;

class CollaborationFactory implements CollaborationFactoryInterface
{
    public function createCollaboration(?array $options = null): Collaboration
    {
        return new Collaboration($options);
    }
}
