<?php

namespace Box\Factory;

use Box\Resource\Collaboration;
use Box\Mapper\Hydrator;
use ReflectionException;

class CollaborationFactory
{
    /**
     * @throws ReflectionException
     */
    public function createCollaboration(?array $options = null): Collaboration
    {
        $collaboration = new Collaboration();
        if (null !== $options) {
            new Hydrator()->hydrate($collaboration, $options);
        }

        return $collaboration;
    }
}
