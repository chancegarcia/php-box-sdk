<?php

namespace Box\Factory;

use Box\User\UserInterface;

interface UserFactoryInterface
{
    public function createUser(?array $options = null): UserInterface;
}
