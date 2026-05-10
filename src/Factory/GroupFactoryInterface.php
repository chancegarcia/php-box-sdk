<?php

namespace Box\Factory;

use Box\Resource\Group;

interface GroupFactoryInterface
{
    public function createGroup(?array $options = null): Group;
}
