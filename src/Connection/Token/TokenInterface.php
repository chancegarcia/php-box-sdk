<?php

namespace Box\Connection\Token;

interface TokenInterface
{
    public function getGrantType(): mixed;

    public function getAccessToken(): mixed;

    public function setAccessToken(mixed $accessToken = null): void;

    public function getRefreshToken(): mixed;

    public function setRefreshToken(mixed $refreshToken = null): void;

    public function getExpiresIn(): mixed;

    public function setExpiresIn(mixed $expiresIn = null): void;

    public function getTokenType(): mixed;

    public function setTokenType(mixed $tokenType = null): void;

    public function getReceivedAt(): ?int;

    public function getRestrictedTo(): array;

    /**
     * @param array|null $restrictedTo
     */
    public function setRestrictedTo(?array $restrictedTo = null): void;

    public function isExpired(): bool;

    public function toArray(): array;
}
