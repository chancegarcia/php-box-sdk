<?php

namespace Box\Factory;

use Box\Connection\ConnectionInterface;

interface ConnectionFactoryInterface
{
    public function createConnection(?array $options = null): ConnectionInterface;

    /**
     * @param array $options required key/values: 'token' => TokenInterface
     */
    public function createAuthorizedConnection(array $options): ConnectionInterface;
}
