<?php

namespace Box\Factory;

use Box\Connection\Response\AuthenticationResponse;
use Box\Connection\Response\AuthenticationResponseInterface;

class AuthenticationResponseFactory implements AuthenticationResponseFactoryInterface
{
    public function createAuthenticationResponse(?array $options = null): AuthenticationResponseInterface
    {
        return new AuthenticationResponse($options);
    }
}
