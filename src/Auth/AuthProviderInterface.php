<?php

namespace Box\Auth;

use Box\Connection\Token\TokenInterface;

interface AuthProviderInterface
{
    public const string TOKEN_URI  = 'https://api.box.com/oauth2/token';
    public const string REVOKE_URI = 'https://api.box.com/oauth2/revoke';

    /**
     * Build the authorization URL.
     */
    public function buildAuthorizationUrl(array $options = []): string;

    /**
     * Exchange an authorization code for a token.
     *
     * @throws \JsonException
     */
    public function exchangeAuthorizationCode(string $code): TokenInterface;

    /**
     * Refresh an existing token.
     *
     * @param array $options Additional options (e.g., device_id, device_name)
     *
     * @throws \JsonException
     */
    public function refreshToken(TokenInterface $token, array $options = []): TokenInterface;

    /**
     * Revoke a token.
     */
    public function revokeToken(TokenInterface $token): void;
}
