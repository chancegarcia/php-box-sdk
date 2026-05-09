<?php

namespace Box\Factory;

use Box\Resource\User;

interface UserFactoryInterface
{
    public function createUser(?array $options = null): User;
}
