<?php

namespace Box\Factory;

use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxException;

class ConnectionFactory implements ConnectionFactoryInterface
{
    public function createConnection(?array $options = null): ConnectionInterface
    {
        return new Connection($options);
    }

    public function createAuthorizedConnection(array $options): ConnectionInterface
    {
        if (!array_key_exists('token', $options)) {
            throw new BoxException('token expected to create an authorized connection');
        }

        if (!$options['token'] instanceof TokenInterface) {
            throw new BoxException('instance of ' . TokenInterface::class . ' expected');
        }

        /**
         * @var TokenInterface $token
         */
        $token = $options['token'];

        unset($options['token']);
        if (!is_string($token->getAccessToken())) {
            throw new BoxException('TokenInterface::getAccessToken() does not contain a string access token');
        }

        $additionalHeaders = null;
        if (array_key_exists('additionalHeaders', $options)) {
            $additionalHeaders = $options['additionalHeaders'];
            unset($options['additionalHeaders']);
            if (!is_array($additionalHeaders)) {
                throw new BoxException('additionalHeaders option must be an array');
            }
        }

        $headers = ["Authorization: Bearer " . $token->getAccessToken()];

        if (is_array($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        $connection = $this->createConnection($options);
        foreach ($headers as $header) {
            if (str_contains($header, ': ')) {
                [$name, $value] = explode(': ', $header, 2);
                $connection->addHeader($name, $value);
            }
        }

        return $connection;
    }
}
