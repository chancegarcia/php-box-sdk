<?php

namespace Box\Connection\Response;

use Box\Connection\Response\AuthenticationResponseInterface;

class AuthenticationResponse implements AuthenticationResponseInterface
{
    protected $responseType;
    protected $accessToken;
    protected $expiresIn;
    protected $tokenType;
    protected $refreshToken;
    protected $error;
    protected $errorDescription;
}
