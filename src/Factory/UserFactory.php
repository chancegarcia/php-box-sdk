<?php

namespace Box\Factory;

use Box\Resource\User;

class UserFactory implements UserFactoryInterface
{
    public function createUser(?array $options = null): User
    {
        return new User();
    }
}
