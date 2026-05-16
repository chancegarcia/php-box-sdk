<?php

namespace Box\Event\Auth;

use Box\Connection\Token\TokenInterface;

readonly class JwtTokenGenerated
{
    public function __construct(
        public TokenInterface $token,
    ) {
    }
}
