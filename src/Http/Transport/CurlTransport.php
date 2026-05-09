<?php

namespace Box\Http\Transport;

use Box\Http\Response\BoxResponseInterface;
use Box\Connection\ConnectionInterface;

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

        if (isset($options['query'])) {
            $uri .= (str_contains($uri, '?') ? '&' : '?') . http_build_query($options['query']);
        }

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (isset($options['headers'])) {
            $headers = [];
            foreach ($options['headers'] as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $headers[] = "$name: $v";
                    }
                } else {
                    $headers[] = "$name: $value";
                }
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (isset($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }

        if (isset($options['multipart'])) {
            $fields = [];
            foreach ($options['multipart'] as $part) {
                $name = $part['name'];
                $contents = $part['contents'];

                if (is_resource($contents)) {
                    // Curl supports resources in PHP 8.1+ but for consistency we might need to handle it.
                    // However, Box SDK usually uses FileStream or paths.
                    // For now, assume contents is string or resource that curl handles.
                }

                $fields[$name] = $contents;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }

        $this->connection->initAdditionalCurlOpts($ch);

        return $this->connection->getCurlData($ch);
    }
}
