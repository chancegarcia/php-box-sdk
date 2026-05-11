<?php

namespace Box\Factory;

use Box\Resource\Group;

class GroupFactory
{
    public function createGroup(?array $options = null): Group
    {
        return new Group();
    }
}
