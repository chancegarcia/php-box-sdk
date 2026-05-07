<?php

namespace Box\Factory;

use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;

class ConnectionFactory implements ConnectionFactoryInterface
{
    public function createConnection(?array $options = null): ConnectionInterface
    {
        return new Connection($options);
    }
}
