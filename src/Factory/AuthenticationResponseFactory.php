<?php

namespace Box\Factory;

use Box\Connection\Response\AuthenticationResponse;
use Box\Connection\Response\AuthenticationResponseInterface;
use Box\Mapper\Hydrator;

class AuthenticationResponseFactory implements AuthenticationResponseFactoryInterface
{
    public function createAuthenticationResponse(?array $options = null): AuthenticationResponseInterface
    {
        $response = new AuthenticationResponse();

        if (is_array($options)) {
            (new Hydrator())->hydrate($response, $options);
        }

        return $response;
    }
}
