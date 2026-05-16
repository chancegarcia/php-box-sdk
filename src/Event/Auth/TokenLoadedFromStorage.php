<?php

namespace Box\Event\Auth;

use Box\Connection\Token\TokenInterface;

readonly class TokenLoadedFromStorage
{
    public function __construct(
        public TokenInterface $token,
    ) {
    }
}
