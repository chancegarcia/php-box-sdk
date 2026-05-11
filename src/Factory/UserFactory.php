<?php

namespace Box\Factory;

use Box\Resource\User;
use Box\Mapper\Hydrator;

class UserFactory
{
    public function createUser(?array $options = null): User
    {
        $user = new User();
        if (null !== $options) {
            (new Hydrator())->hydrate($user, $options);
        }

        return $user;
    }
}
