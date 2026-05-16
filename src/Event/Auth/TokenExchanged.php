<?php

namespace Box\Event\Auth;

use Box\Connection\Token\TokenInterface;

readonly class TokenExchanged
{
    public function __construct(
        public TokenInterface $token,
    ) {
    }
}
