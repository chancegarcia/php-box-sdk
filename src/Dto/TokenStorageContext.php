<?php

declare(strict_types=1);

namespace Box\Dto;

/**
 * Class TokenStorageContext
 *
 * Formal context for identifying a token in storage.
 * Supports one-active-token-per-context behavior.
 */
final class TokenStorageContext
{
    public function __construct(
        private readonly ?string $userId = null,
        private readonly ?string $enterpriseId = null,
        private readonly ?string $clientId = null,
    ) {
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getEnterpriseId(): ?string
    {
        return $this->enterpriseId;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * Generates a canonical key for this context.
     * Useful for in-memory or filesystem storage indexing.
     */
    public function getCanonicalKey(): string
    {
        return sprintf(
            'user:%s|enterprise:%s|client:%s',
            $this->userId ?? 'none',
            $this->enterpriseId ?? 'none',
            $this->clientId ?? 'none'
        );
    }

    /**
     * Compares two contexts for equality.
     */
    public function equals(TokenStorageContext $other): bool
    {
        return $this->getCanonicalKey() === $other->getCanonicalKey();
    }
}
