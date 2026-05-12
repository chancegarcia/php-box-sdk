<?php

namespace Box\Auth;

use Box\Connection\Token\TokenInterface;

interface AuthProviderInterface
{
    /**
     * Build the authorization URL.
     *
     * @param array $options
     * @return string
     */
    public function buildAuthorizationUrl(array $options = []): string;

    /**
     * Exchange an authorization code for a token.
     *
     * @param string $code
     * @return TokenInterface
     */
    public function exchangeAuthorizationCode(string $code): TokenInterface;

    /**
     * Refresh an existing token.
     *
     * @param TokenInterface $token
     * @param array $options Additional options (e.g., device_id, device_name)
     * @return TokenInterface
     */
    public function refreshToken(TokenInterface $token, array $options = []): TokenInterface;

    /**
     * Revoke a token.
     *
     * @param TokenInterface $token
     * @return void
     */
    public function revokeToken(TokenInterface $token): void;
}
