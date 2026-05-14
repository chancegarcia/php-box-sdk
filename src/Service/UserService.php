<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Resource\User;

class UserService extends Service implements UserServiceInterface
{
    public const ENDPOINT = 'https://api.box.com/2.0/users';
    public const CURRENT_USER_ENDPOINT = 'https://api.box.com/2.0/users/me';

    /**
     * List all users in the enterprise.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listUsers(int $limit = 100, int $offset = 0): array
    {
        $uri = self::ENDPOINT . '?limit=' . $limit . '&offset=' . $offset;

        return $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');
    }

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
