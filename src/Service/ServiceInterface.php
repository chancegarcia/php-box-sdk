<?php

namespace Box\Service;

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use BadMethodCallException;

interface ServiceInterface
{
    public function getConnection(): ConnectionInterface;

    public function setConnection(?ConnectionInterface $connection = null): void;

    public function getToken(): TokenInterface;

    public function setToken(?TokenInterface $token = null): void;

    /**
     * @param string $returnType 'decoded', 'flat', 'array', or 'original'
     *
     * @throws BoxException
     * @throws BadMethodCallException
     */
    public function handleBoxResponse(?BoxResponseInterface $response = null, string $returnType = 'decoded'): mixed;
}
