<?php

namespace Box\Factory;

use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;
use Box\Mapper\Hydrator;

class TokenFactory implements TokenFactoryInterface
{
    public function createToken(?array $options = null): TokenInterface
    {
        $token = new Token();

        if (is_array($options)) {
            (new Hydrator())->hydrate($token, $options);
        }

        return $token;
    }
}
