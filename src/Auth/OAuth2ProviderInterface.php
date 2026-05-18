<?php

namespace Box\Auth;

interface OAuth2ProviderInterface extends AuthProviderInterface
{
    public function setClientId(?string $clientId): void;

    public function setClientSecret(?string $clientSecret): void;

    public function setRedirectUri(?string $redirectUri): void;
}
