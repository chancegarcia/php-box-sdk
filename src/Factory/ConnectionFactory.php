<?php

namespace Box\Factory;

use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxException;
use Box\Mapper\Hydrator;
use ReflectionException;

class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @throws ReflectionException
     */
    public function createConnection(?array $options = null): ConnectionInterface
    {
        $connection = new Connection();

        if (is_array($options)) {
            $transport = $options['transport'] ?? null;
            if ($transport) {
                unset($options['transport']);
            }

            new Hydrator()->hydrate($connection, $options);

            if ($transport) {
                $connection->setTransportName($transport);
            }
        }

        return $connection;
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
        $accessToken = $token->getAccessToken();
        if (!is_string($accessToken)) {
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

        $connection = $this->createConnection($options);
        $connection->setAccessToken($accessToken);

        if (is_array($additionalHeaders)) {
            foreach ($additionalHeaders as $name => $value) {
                if (is_int($name)) {
                    if (str_contains($value, ': ')) {
                        [$headerName, $headerValue] = explode(': ', $value, 2);
                        $connection->addHeader($headerName, $headerValue);
                    }
                } else {
                    $connection->addHeader($name, $value);
                }
            }
        }

        return $connection;
    }
}
