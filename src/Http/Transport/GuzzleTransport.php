<?php

namespace Box\Http\Transport;

use Box\Http\Response\BoxResponse;
use Box\Http\Response\BoxResponseInterface;
use Box\Exception\TransportException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class GuzzleTransport implements TransportInterface
{
    private GuzzleClientInterface $client;

    public function __construct(?GuzzleClientInterface $client = null)
    {
        $this->client = $client ?? new GuzzleClient(['http_errors' => false]);
    }

    public function request(string $method, string $uri, array $options = []): BoxResponseInterface
    {
        if (!isset($options['http_errors'])) {
            $options['http_errors'] = false;
        }

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->convertResponse($response);
    }

    protected function convertResponse(PsrResponseInterface $psrResponse): BoxResponseInterface
    {
        return new BoxResponse(psrResponse: $psrResponse);
    }
}
