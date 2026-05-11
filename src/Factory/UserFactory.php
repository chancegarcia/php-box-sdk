<?php

namespace Box\Factory;

use Box\Resource\User;

class UserFactory
{
    public function createUser(?array $options = null): User
    {
        return new User();
    }
}
