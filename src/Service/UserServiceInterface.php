<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Dto\PagedResult;
use Box\Resource\User;

interface UserServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * List all users in the enterprise.
     *
     * @return PagedResult<User>
     */
    public function listUsers(int $limit = 100, int $offset = 0): PagedResult;

    /**
     * Get the current user's details.
     */
    public function getCurrentUser(): User;

    /**
     * Get a user's details by ID.
     */
    public function getUser(string $userId): User;
}
