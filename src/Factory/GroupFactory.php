<?php

namespace Box\Factory;

use Box\Group\Group;
use Box\Group\GroupInterface;

class GroupFactory implements GroupFactoryInterface
{
    public function createGroup(?array $options = null): GroupInterface
    {
        return new Group($options);
    }
}
