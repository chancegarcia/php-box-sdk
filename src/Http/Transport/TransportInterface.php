<?php

namespace Box\Http\Transport;

use Box\Http\Response\BoxResponseInterface;

interface TransportInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $uri, array $options = []): BoxResponseInterface;
}
