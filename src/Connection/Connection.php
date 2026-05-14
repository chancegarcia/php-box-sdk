<?php

/**
 * @package     Box
 * @subpackage  Box_Connection
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013 Chance Garcia, chancegarcia.com
 *
 * connection assumes a valid access token
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2016 Chance Garcia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Box\Connection;

use Box\Exception\BoxException;
use Box\Factory\AuthenticationResponseFactory;
use Box\Factory\AuthenticationResponseFactoryInterface;
use Box\Exception\ApiException;
use Box\Exception\ConflictException;
use Box\Exception\ForbiddenException;
use Box\Exception\NotFoundException;
use Box\Exception\RateLimitException;
use Box\Exception\UnauthorizedException;
use Box\Http\FileStream;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Transport\GuzzleTransport;
use Box\Http\Transport\TransportInterface;
use Box\Mapper\Hydrator;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxApiErrorTrait;
use Psr\Log\LoggerInterface;

/**
 * Class Connection
 * @package Box\Model
 * @todo v1: remove client credential synchronization and make Connection the source of truth
 */
class Connection implements ConnectionInterface
{
    use LoggerAwareTrait;
    use BoxApiErrorTrait;

    public const TRANSPORT_GUZZLE = 'guzzle';

    protected mixed $responseType = "code";
    protected mixed $state = null;
    protected string $requestType = "GET";

    protected ?string $accessToken = null;
    protected array $headers = [];
    protected string $transportName = self::TRANSPORT_GUZZLE;
    protected ?TransportInterface $transport = null;
    protected array $guzzleOptions = [];

    protected mixed $authenticationResponse = null;
    protected AuthenticationResponseFactoryInterface $authenticationResponseFactory;

    private bool $disableSslVerification = false;

    public function __construct(?array $options = null, ?AuthenticationResponseFactoryInterface $authenticationResponseFactory = null)
    {
        if (is_array($options)) {
            $transport = $options['transport'] ?? null;
            if ($transport) {
                unset($options['transport']);
            }

            (new Hydrator())->hydrate($this, $options);

            if ($transport) {
                $this->setTransportName($transport);
            }

            if (array_key_exists('disableSslVerification', $options) && is_bool($options['disableSslVerification'])) {
                $this->disableSslVerification = $options['disableSslVerification'];
            }
            if (isset($options['accessToken'])) {
                $this->setAccessToken($options['accessToken']);
            }
        }

        $this->authenticationResponseFactory = $authenticationResponseFactory ?? new AuthenticationResponseFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $uri): BoxResponseInterface
    {
        return $this->request('GET', $uri);
    }

    public function request(string $method, string $uri, array $options = []): BoxResponseInterface
    {
        $transport = $this->getTransport();

        $headers = array_merge($this->getHeaders(), $options['headers'] ?? []);

        if ($this->getAccessToken()) {
            $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
        }

        $options['headers'] = $headers;

        if (self::TRANSPORT_GUZZLE === $this->getTransportName()) {
            $options = array_merge($this->getGuzzleOptions(), $options);
            $options['verify'] = !$this->getDisableSslVerification();
        }

        $response = $transport->request($method, $uri, $options);

        if (true === ($options['throw_on_error'] ?? false) && !$response->isSuccessful()) {
            throw $this->createApiException($response);
        }

        return $response;
    }

    /**
     * Create an appropriate ApiException based on the response status code.
     */
    protected function createApiException(BoxResponseInterface $response): ApiException
    {
        $statusCode = $response->getStatusCode();
        $message = sprintf('Box API error [%d]', $statusCode);

        return match ($statusCode) {
            401 => new UnauthorizedException($message, $statusCode, null, $response),
            403 => new ForbiddenException($message, $statusCode, null, $response),
            404 => new NotFoundException($message, $statusCode, null, $response),
            409 => new ConflictException($message, $statusCode, null, $response),
            429 => new RateLimitException($message, $statusCode, null, $response),
            default => new ApiException($message, $statusCode, null, $response),
        };
    }

    public function delete(string $uri): BoxResponseInterface
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug("delete uri: " . $uri, [__METHOD__ . ":" . __LINE__]);
        }

        return $this->request('DELETE', $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $uri, array|string $params = []): BoxResponseInterface
    {
        if (is_array($params)) {
            $postParams = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            @trigger_error(
                'the `params` value as an array will be deprecated in the future. Please provide a json encoded string',
                E_USER_DEPRECATED
            );
        } else {
            $postParams = $params;
        }

        return $this->request('PUT', $uri, ['body' => $postParams]);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $uri, array|string $params = [], bool $nameValuePair = false): BoxResponseInterface
    {
        if ($nameValuePair) {
            $params = json_encode($params);
            @trigger_error(
                'the `nameValuePair` switch will be deprecated in the future; all values will be json encoded values',
                E_USER_DEPRECATED
            );
        }

        if (is_array($params)) {
            $postParams = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            @trigger_error(
                'the `params` value as an array will be deprecated in the future. Please provide a json encoded string',
                E_USER_DEPRECATED
            );
        } else {
            $postParams = $params;
        }

        return $this->request('POST', $uri, ['body' => $postParams]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(string $file): mixed
    {
        $fInfo = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($fInfo, $file);
    }

    /**
     * @param string $uri
     * @param string|FileStream $file
     * @param string|int $parentId
     * @return BoxResponseInterface
     * @throws \Box\Exception\BoxException
     */
    public function postFile(string $uri, string|FileStream $file, string|int $parentId = 0): BoxResponseInterface
    {
        // @todo allow Content-MD5 header to be set

        if (empty($parentId) && $parentId !== 0 && $parentId !== '0') {
            throw new BoxException("Invalid parent ID. Parent ID cannot be empty.", BoxException::INVALID_INPUT);
        }

        if ($file instanceof FileStream) {
            $resource = $file->getResource();
            if (!is_resource($resource)) {
                throw new BoxException("Invalid FileStream resource.", BoxException::INVALID_INPUT);
            }
            $filename = $file->getFilename();
            if (empty($filename)) {
                throw new BoxException("FileStream must have a filename.", BoxException::INVALID_INPUT);
            }
            $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        } else {
            if (empty($file)) {
                throw new BoxException("File path cannot be empty.", BoxException::INVALID_INPUT);
            }
            if (!file_exists($file)) {
                throw new BoxException("File does not exist: " . $file, BoxException::INVALID_INPUT);
            }
            if (!is_readable($file)) {
                throw new BoxException("File is not readable: " . $file, BoxException::INVALID_INPUT);
            }
            $filename = basename($file);
            $mimeType = $this->getMimeType($file);
            $resource = fopen($file, 'rb');
            if (!$resource) {
                throw new BoxException("Failed to open file: " . $file, BoxException::INVALID_INPUT);
            }
        }

        return $this->request('POST', $uri, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => $resource,
                    'filename' => $filename,
                    'headers' => [
                        'Content-Type' => $mimeType
                    ]
                ],
                [
                    'name' => 'parent_id',
                    'contents' => (string)$parentId
                ]
            ]
        ]);
    }

    /**
     * @param mixed $requestType
     */
    public function setRequestType(mixed $requestType = null): void
    {
        $this->requestType = $requestType;
    }

    public function getRequestType(): string
    {
        return $this->requestType;
    }

    /**
     * @param mixed $authenticationResponse
     */
    public function setAuthenticationResponse(mixed $authenticationResponse = null): void
    {
        $this->authenticationResponse = $authenticationResponse;
    }

    public function getAuthenticationResponse(): mixed
    {
        return $this->authenticationResponse;
    }

    /**
     * @param mixed $responseType
     */
    public function setResponseType(mixed $responseType = null): void
    {
        $this->responseType = $responseType;
    }

    public function getResponseType(): mixed
    {
        return $this->responseType;
    }

    /**
     * @param mixed $state
     */
    public function setState(mixed $state = null): void
    {
        $this->state = $state;
    }

    public function getState(): mixed
    {
        return $this->state;
    }

    public function getDisableSslVerification(): ?bool
    {
        return $this->disableSslVerification;
    }

    public function setAccessToken(?string $accessToken = null): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setHeaders(array $headers = []): void
    {
        $this->headers = $headers;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function setTransportName(string $transportName): void
    {
        $this->transportName = $transportName;
        $this->transport = null; // reset cached transport
    }

    public function getTransportName(): string
    {
        return $this->transportName;
    }

    public function setTransport(TransportInterface $transport): void
    {
        $this->transport = $transport;
    }

    public function getTransport(): TransportInterface
    {
        if (null === $this->transport) {
            $this->transport = new GuzzleTransport();
        }
        return $this->transport;
    }

    public function setGuzzleOptions(array $options = []): void
    {
        $this->guzzleOptions = $options;
    }

    public function getGuzzleOptions(): array
    {
        return $this->guzzleOptions;
    }
}
