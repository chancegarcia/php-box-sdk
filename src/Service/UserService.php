<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Resource\User;
use Box\Mapper\Hydrator;

class UserService extends Service
{
    public const ENDPOINT = 'https://api.box.com/2.0/users';
    public const CURRENT_USER_ENDPOINT = 'https://api.box.com/2.0/users/me';

    /**
     * Get the current user's details.
     *
     * @return User
     */
    public function getCurrentUser(): User
    {
        $userData = $this->getFromBox(self::CURRENT_USER_ENDPOINT, 'decoded');

        return (new Hydrator())->hydrate(User::class, $userData);
    }

    /**
     * Get a user's details by ID.
     *
     * @param string $userId
     * @return User
     */
    public function getUser(string $userId): User
    {
        $uri = self::ENDPOINT . '/' . $userId;
        $userData = $this->getFromBox($uri, 'decoded');

        return (new Hydrator())->hydrate(User::class, $userData);
    }
}
