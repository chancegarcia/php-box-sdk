<?php

namespace Box\Factory;

use Box\Resource\Group;
use Box\Mapper\Hydrator;

class GroupFactory
{
    public function createGroup(?array $options = null): Group
    {
        $group = new Group();
        if (null !== $options) {
            (new Hydrator())->hydrate($group, $options);
        }

        return $group;
    }
}
