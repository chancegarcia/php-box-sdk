<?php

namespace Box\Event\Auth;

use Box\Connection\Token\TokenInterface;

readonly class TokenRevoked
{
    public function __construct(
        public TokenInterface $token,
    ) {
    }
}
