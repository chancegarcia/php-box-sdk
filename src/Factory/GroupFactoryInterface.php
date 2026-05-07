<?php

namespace Box\Factory;

use Box\Group\GroupInterface;

interface GroupFactoryInterface
{
    public function createGroup(?array $options = null): GroupInterface;
}
