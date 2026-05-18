<?php

namespace Box\Factory;

use Box\Resource\Group;
use Box\Mapper\Hydrator;
use ReflectionException;

class GroupFactory
{
    /**
     * @throws ReflectionException
     */
    public function createGroup(?array $options = null): Group
    {
        $group = new Group();
        if (null !== $options) {
            new Hydrator()->hydrate($group, $options);
        }

        return $group;
    }
}
