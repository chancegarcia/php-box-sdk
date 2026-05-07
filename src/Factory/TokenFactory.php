<?php

namespace Box\Factory;

use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;

class TokenFactory implements TokenFactoryInterface
{
    public function createToken(?array $options = null): TokenInterface
    {
        return new Token($options);
    }
}
