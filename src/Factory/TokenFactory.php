<?php

namespace Box\Factory;

use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;
use Box\Mapper\Hydrator;
use ReflectionException;

class TokenFactory implements TokenFactoryInterface
{
    /**
     * @throws ReflectionException
     */
    public function createToken(?array $options = null): TokenInterface
    {
        $token = new Token();

        if (is_array($options)) {
            new Hydrator()->hydrate($token, $options);
        }

        return $token;
    }
}
