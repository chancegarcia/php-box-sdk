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
use Box\Exception\TransportException;
use Box\Exception\UnauthorizedException;
use Box\Http\FileStream;
use Box\Http\Response\BoxResponse;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Transport\CurlTransport;
use Box\Http\Transport\GuzzleTransport;
use Box\Http\Transport\TransportInterface;
use Box\Mapper\Hydrator;
use Box\Logger\LoggerAwareInterface;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxLoggerTrait;
use CURLFile;
use Psr\Log\LoggerInterface;
use CurlHandle;

/**
 * Class Connection
 * @package Box\Model
 * @todo add in method to access last curl info, error and error number for debugging
 * @todo v1: remove cURL-specific methods from this class or move to transport-specific interface
 * @todo v1: make transport selection the primary way to configure HTTP execution
 * @todo v1: remove client credential synchronization and make Connection the source of truth
 */
class Connection implements ConnectionInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use BoxLoggerTrait;

    public const TRANSPORT_CURL = 'curl';
    public const TRANSPORT_GUZZLE = 'guzzle';

    protected mixed $responseType = "code";
    protected mixed $clientId = null;
    protected mixed $clientSecret = null;
    protected mixed $redirectUri = null;
    protected mixed $state = null;
    protected string $requestType = "GET";

    protected ?string $accessToken = null;
    protected array $headers = [];
    protected string $transportName = self::TRANSPORT_CURL;
    protected ?TransportInterface $transport = null;
    protected array $guzzleOptions = [];

    protected mixed $authenticationResponse = null;
    protected AuthenticationResponseFactoryInterface $authenticationResponseFactory;

    /**
     * @var array array of options with the options as the key and the option values as the value
     */
    protected array $curlOpts = [];

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

    // relooking over auth flow, we have to assume app is already authorized externally.
    // rewrite to use tokens for connection
    // may need to store the tokens
    public function connect(): mixed
    {
        throw new BoxException('method not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function initCurl(): CurlHandle
    {
        $ch = curl_init();
        $this->initCurlOpts($ch);
        return $ch;
    }

    /**
     * {@inheritdoc}
     */
    public function initCurlOpts(CurlHandle $ch): CurlHandle
    {
        // figure out how to log to verbose output to file. maybe make a box logger? or do output buffer capture?
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        // get full response with headers
        // http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        // note: disable should only be used for development purposes.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$this->getDisableSslVerification());
        return $ch;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurlData(CurlHandle $ch): BoxResponseInterface
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug('before curl_exec curl opts', [
                __METHOD__ . ":" . __LINE__,
                var_export(curl_getinfo($ch), true),
            ]);
        }
        $sResponse = curl_exec($ch);

        if (false === $sResponse) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            throw new TransportException($error, $errno);
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug('curl_exec response: ' . $sResponse, [
                __METHOD__ . ":" . __LINE__,
            ]);
        }

        // split curl result into header and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($sResponse, 0, $header_size);
        $body = substr($sResponse, $header_size) ?: "";

        return new BoxResponse($body, $header);
    }

    /**
     * {@inheritdoc}
     */
    public function initAdditionalCurlOpts(CurlHandle $ch): CurlHandle
    {
        $opts = $this->getCurlOpts();
        if (0 != count($opts)) {
            foreach ($opts as $opt => $optValue) {
                // CURLOPT_HTTPHEADER, CURLOPT_QUOTE, CURLOPT_HTTP200ALIASES and CURLOPT_POSTQUOTE
                // require array or object arguments

                switch ($opt) {
                    case "CURLOPT_HTTPHEADER":
                    case "CURLOPT_QUOTE":
                    case "CURLOPT_HTTP200ALIASES":
                    case "CURLOPT_POSTQUOTE":
                        // throw exception so it doesn't throw a warning
                        if (!is_array($optValue)) {
                            $this->error(
                                [
                                    'error' => 'curl opt (' . $opt . ') needs to be an array or object',
                                    'error_description' => 'curl opt (' . $opt . ') needs to be an array or object'
                                ]
                            );
                        }
                        curl_setopt($ch, constant($opt), $optValue);
                        break;
                    default:
                        curl_setopt($ch, constant($opt), $optValue);
                        break;
                }
            }
        }
        return $ch;
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
            $headers['Authorization'] = $this->getAuthorizationHeader();
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
    public function createCurlFile(string $pathToFile, string $mimeType, string $filename): CURLFile
    {
        return new CURLFile($pathToFile, $mimeType, $filename);
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

        if (self::TRANSPORT_GUZZLE === $this->getTransportName()) {
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

        if ($file instanceof FileStream) {
            // For CurlTransport, we might need a temporary file if it only supports CURLFile with paths
            // But CurlTransport's request() method just passes $options['multipart']
            // to curl_setopt(..., CURLOPT_POSTFIELDS, $fields)
            // If we use Guzzle's approach of passing the resource, curl might not handle it correctly
            // in an array for multipart.
            // Actually, curl_setopt with an array for CURLOPT_POSTFIELDS expects CURLFile or string
            // (starting with @ is deprecated).

            // To be safe and compatible with CurlTransport's current implementation:
            $tmpFile = tempnam(sys_get_temp_dir(), 'box_upload_');
            $tmpResource = fopen($tmpFile, 'wb');
            if (!$tmpResource) {
                throw new BoxException("Failed to create temporary file for upload", BoxException::INVALID_INPUT);
            }
            stream_copy_to_stream($resource, $tmpResource);
            fclose($tmpResource);
            rewind($resource);
            // keep original resource state if possible, though it's probably better to just use the path now

            $curlFile = $this->createCurlFile($tmpFile, $mimeType, $filename);

            $response = $this->request('POST', $uri, [
                'multipart' => [
                    ['name' => 'file', 'contents' => $curlFile],
                    ['name' => 'parent_id', 'contents' => (string)$parentId]
                ]
            ]);

            unlink($tmpFile);
            return $response;
        }

        $curlFile = $this->createCurlFile($file, $mimeType, $filename);

        return $this->request('POST', $uri, [
            'multipart' => [
                ['name' => 'file', 'contents' => $curlFile],
                ['name' => 'parent_id', 'contents' => (string)$parentId]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    /**
     * @param array|null $curlOpts
     */
    public function setCurlOpts(?array $curlOpts = null): void
    {
        if (!is_array($curlOpts)) {
            $curlOpts = $curlOpts !== null ? [$curlOpts] : [];
        }
        $this->curlOpts = $curlOpts;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurlOpts(): array
    {
        return $this->curlOpts;
    }


    /**
     * @param mixed $clientId
     */
    public function setClientId(mixed $clientId = null): void
    {
        $this->clientId = $clientId;
    }

    public function getClientId(): mixed
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientSecret
     */
    public function setClientSecret(mixed $clientSecret = null): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getClientSecret(): mixed
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed $redirectUri
     */
    public function setRedirectUri(mixed $redirectUri = null): void
    {
        $this->redirectUri = $redirectUri;
    }

    public function getRedirectUri(): mixed
    {
        return $this->redirectUri;
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

    public function getAuthorizationHeader(): ?string
    {
        return $this->getAccessToken() ? 'Bearer ' . $this->getAccessToken() : null;
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
            if (self::TRANSPORT_GUZZLE === $this->getTransportName()) {
                $this->transport = new GuzzleTransport();
            } else {
                $this->transport = new CurlTransport($this);
            }
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
