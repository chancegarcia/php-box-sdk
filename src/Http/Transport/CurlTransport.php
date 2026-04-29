<?php

namespace Box\Http\Transport;

use Box\Http\Response\BoxResponse;
use Box\Http\Response\BoxResponseInterface;
use Box\Model\Connection\ConnectionInterface;

class CurlTransport implements TransportInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function request(string $method, string $uri, array $options = []): BoxResponseInterface
    {
        $ch = $this->connection->initCurl();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (isset($options['headers'])) {
            $headers = [];
            foreach ($options['headers'] as $name => $value) {
                $headers[] = "$name: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (isset($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }

        if (isset($options['multipart'])) {
            // curl_setopt handles array for multipart
            $fields = [];
            foreach ($options['multipart'] as $part) {
                $fields[$part['name']] = $part['contents'];
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }

        return $this->connection->getCurlData($ch);
    }
}
