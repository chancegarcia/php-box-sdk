<?php

namespace Box\Factory;

use Box\Connection\ConnectionInterface;

interface ConnectionFactoryInterface
{
    public function createConnection(?array $options = null): ConnectionInterface;
}
