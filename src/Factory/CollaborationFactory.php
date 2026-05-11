<?php

namespace Box\Factory;

use Box\Resource\Collaboration;
use Box\Mapper\Hydrator;

class CollaborationFactory
{
    public function createCollaboration(?array $options = null): Collaboration
    {
        $collaboration = new Collaboration();
        if (null !== $options) {
            (new Hydrator())->hydrate($collaboration, $options);
        }

        return $collaboration;
    }
}
