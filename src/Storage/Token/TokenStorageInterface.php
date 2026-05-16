<?php

namespace Box\Storage\Token;

use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;

interface TokenStorageInterface
{
    /**
     * Store token to storage.
     * Implementation should handle one-active-token-per-context behavior.
     */
    public function storeToken(TokenInterface $token, TokenStorageContext $context): void;

    /**
     * Update token in storage for a given context.
     */
    public function updateToken(TokenInterface $token, TokenStorageContext $context): void;

    /**
     * Retrieve token from storage for a given context.
     *
     * @return TokenInterface|null Returns null if no token is found for the context.
     */
    public function retrieveToken(TokenStorageContext $context): ?TokenInterface;

    /**
     * Remove token from storage for a given context.
     */
    public function removeToken(TokenStorageContext $context): void;

    /**
     * Clear all tokens from storage.
     * Optional implementation for clearing the entire storage backend.
     */
    public function clear(): void;
}
