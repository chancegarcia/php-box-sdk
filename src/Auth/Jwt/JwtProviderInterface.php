<?php

namespace Box\Auth\Jwt;

use Box\Auth\AuthProviderInterface;
use Box\Connection\Token\TokenInterface;

interface JwtProviderInterface extends AuthProviderInterface
{
    /**
     * Exchange JWT assertion for an enterprise token.
     *
     * @throws \JsonException
     */
    public function exchangeForEnterpriseToken(): TokenInterface;

    /**
     * Exchange JWT assertion for an app user token.
     *
     * @throws \JsonException
     */
    public function exchangeForAppUserToken(string $userId): TokenInterface;
}
