<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Dto\PagedResult;
use Box\Exception\BoxException;
use Box\Exception\BoxResponseException;
use Box\Resource\User;

class UserService extends Service implements UserServiceInterface
{
    public const string ENDPOINT = 'https://api.box.com/2.0/users';
    public const string CURRENT_USER_ENDPOINT = 'https://api.box.com/2.0/users/me';

    /**
     * List all users in the enterprise.
     *
     * @throws BoxResponseException
     * @return PagedResult<User>
     */
    public function listUsers(int $limit = 100, int $offset = 0): PagedResult
    {
        $uri = self::ENDPOINT . '?limit=' . $limit . '&offset=' . $offset;
        $data = $this->handleBoxResponse($this->getConnection()->query($uri), 'flat');

        return $this->hydratePagedResult($data, User::class);
    }

    /**
     * Get the current user's details.
     *
     * @throws BoxException
     */
    public function getCurrentUser(): User
    {
        return $this->getResourceFromBox(self::CURRENT_USER_ENDPOINT, User::class);
    }

    /**
     * Get a user's details by ID.
     *
     * @throws BoxException
     */
    public function getUser(string $userId): User
    {
        $uri = self::ENDPOINT . '/' . $userId;

        return $this->getResourceFromBox($uri, User::class);
    }
}
