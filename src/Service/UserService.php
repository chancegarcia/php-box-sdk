<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Resource\User;

class UserService extends Service implements UserServiceInterface
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
        return $this->getResourceFromBox(self::CURRENT_USER_ENDPOINT, User::class);
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

        return $this->getResourceFromBox($uri, User::class);
    }
}
