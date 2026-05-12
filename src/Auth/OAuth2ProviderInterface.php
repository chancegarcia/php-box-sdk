<?php

namespace Box\Auth;

interface OAuth2ProviderInterface extends AuthProviderInterface
{
    /**
     * @param string|null $clientId
     */
    public function setClientId(?string $clientId): void;

    /**
     * @param string|null $clientSecret
     */
    public function setClientSecret(?string $clientSecret): void;

    /**
     * @param string|null $redirectUri
     */
    public function setRedirectUri(?string $redirectUri): void;
}
