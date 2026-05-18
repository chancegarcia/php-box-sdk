<?php

namespace Box\Storage\Token\Container;

use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\TokenStorageInterface;

class TokenStorageContainer implements TokenStorageInterface
{
    /**
     * @var array<string, TokenInterface>
     */
    protected array $tokens = [];

    public function retrieveToken(TokenStorageContext $context): ?TokenInterface
    {
        return $this->tokens[$context->getCanonicalKey()] ?? null;
    }

    public function storeToken(TokenInterface $token, TokenStorageContext $context): void
    {
        $this->tokens[$context->getCanonicalKey()] = $token;
    }

    public function updateToken(TokenInterface $token, TokenStorageContext $context): void
    {
        $this->tokens[$context->getCanonicalKey()] = $token;
    }

    public function removeToken(TokenStorageContext $context): void
    {
        unset($this->tokens[$context->getCanonicalKey()]);
    }

    public function clear(): void
    {
        $this->tokens = [];
    }
}
