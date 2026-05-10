<?php

namespace Box\Factory;

use Box\Resource\Group;

class GroupFactory implements GroupFactoryInterface
{
    public function createGroup(?array $options = null): Group
    {
        return new Group($options);
    }
}
