<?php

namespace Box\Factory;

use Box\Connection\Token\TokenInterface;

interface TokenFactoryInterface
{
    public function createToken(?array $options = null): TokenInterface;
}
