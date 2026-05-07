<?php

namespace Box\Factory;

use Box\User\User;
use Box\User\UserInterface;

class UserFactory implements UserFactoryInterface
{
    public function createUser(?array $options = null): UserInterface
    {
        return new User($options);
    }
}
