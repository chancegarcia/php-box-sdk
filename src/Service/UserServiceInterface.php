<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Resource\User;

interface UserServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * List all users in the enterprise.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listUsers(int $limit = 100, int $offset = 0): array;

    /**
     * Get the current user's details.
     *
     * @return User
     */
    public function getCurrentUser(): User;

    /**
     * Get a user's details by ID.
     *
     * @param string $userId
     * @return User
     */
    public function getUser(string $userId): User;
}
