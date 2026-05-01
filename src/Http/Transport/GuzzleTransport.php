<?php

namespace Box\Http\Transport;

use Box\Http\Response\BoxResponse;
use Box\Http\Response\BoxResponseInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
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
        $response = $this->client->request($method, $uri, $options);

        return $this->convertResponse($response);
    }

    protected function convertResponse(PsrResponseInterface $psrResponse): BoxResponseInterface
    {
        $statusLine = sprintf(
            "HTTP/%s %s %s",
            $psrResponse->getProtocolVersion(),
            $psrResponse->getStatusCode(),
            $psrResponse->getReasonPhrase()
        );

        $headerString = $statusLine . "\r\n";
        foreach ($psrResponse->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headerString .= "$name: $value\r\n";
            }
        }
        $headerString .= "\r\n";

        return new BoxResponse((string)$psrResponse->getBody(), $headerString, $psrResponse);
    }
}
