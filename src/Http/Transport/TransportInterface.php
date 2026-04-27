<?php

namespace Box\Http\Transport;

use Box\Http\Response\BoxResponseInterface;

interface TransportInterface
{
    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return BoxResponseInterface
     */
    public function request(string $method, string $uri, array $options = []): BoxResponseInterface;
}
