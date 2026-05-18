<?php

namespace Box\Factory;

use Box\Connection\Response\AuthenticationResponseInterface;

interface AuthenticationResponseFactoryInterface
{
    public function createAuthenticationResponse(?array $options = null): AuthenticationResponseInterface;
}
