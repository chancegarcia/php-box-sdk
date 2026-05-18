<?php

namespace Box\Connection\Token;

class Token implements TokenInterface
{
    protected mixed $accessToken = null;
    protected mixed $refreshToken = null;
    protected mixed $grantType = "authorization_code";
    protected mixed $expiresIn = null;
    protected mixed $tokenType = null;
    protected array $restrictedTo = [];
    protected ?int $receivedAt = null;

    public function setExpiresIn(mixed $expiresIn = null): void
    {
        $this->expiresIn = $expiresIn;
        if (null !== $expiresIn && null === $this->receivedAt) {
            $this->receivedAt = time();
        }
    }

    public function getExpiresIn(): mixed
    {
        return $this->expiresIn;
    }

    public function setTokenType(mixed $tokenType = null): void
    {
        $this->tokenType = $tokenType;
    }

    public function getTokenType(): mixed
    {
        return $this->tokenType;
    }

    public function setAccessToken(mixed $accessToken = null): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): mixed
    {
        return $this->accessToken;
    }

    public function setGrantType(mixed $grantType = null): void
    {
        $this->grantType = $grantType;
    }

    public function getGrantType(): mixed
    {
        return $this->grantType;
    }

    public function setRefreshToken(mixed $refreshToken = null): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): mixed
    {
        return $this->refreshToken;
    }

    public function getRestrictedTo(): array
    {
        return $this->restrictedTo;
    }

    public function setRestrictedTo(?array $restrictedTo = null): void
    {
        $this->restrictedTo = $restrictedTo ?? [];
    }

    public function getReceivedAt(): ?int
    {
        return $this->receivedAt;
    }

    public function isExpired(): bool
    {
        if (null === $this->expiresIn || null === $this->receivedAt) {
            return false;
        }

        $now = time();
        $expirationTime = $this->receivedAt + (int) $this->expiresIn;

        return $now >= $expirationTime;
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in' => $this->expiresIn,
            'token_type' => $this->tokenType,
            'restricted_to' => $this->restrictedTo,
        ];
    }

    // all parameters must be url encoded
}
