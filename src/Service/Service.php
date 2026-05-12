<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 2:55 PM
 * @package     Box
 * @subpackage  Box_Model
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013 Chance Garcia, chancegarcia.com
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

namespace Box\Service;

use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Exception\BoxException;
use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;
use Box\Logger\LoggerAwareInterface;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxLoggerTrait;
use Box\Mapper\Hydrator;
use OutOfBoundsException;
use RuntimeException;
use InvalidArgumentException;
use BadMethodCallException;
use stdClass;
use Psr\Log\LoggerInterface;

class Service implements ServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use BoxLoggerTrait;

    /**
     * basic connection used in initial authorization to execute token refresh for authorized connection
     * @var Connection|ConnectionInterface
     */
    protected $connection;

    /**
     * separate connection object that contains the token and has set the auth headers {@see ConnectionFactory}
     * @var Connection|ConnectionInterface
     */
    protected $authorizedConnection;

    protected $additionalConnectionHeaders = [];

    /**
     * @var Token|TokenInterface
     */
    protected $token;

    protected $clientId;
    protected $clientSecret;
    protected $deviceId = null;
    protected $deviceName = null;

    /**
     * @var array
     */
    private $allowedReturnTypes = [
        'decoded',
        'original',
        'flat',
        'array',
    ];

    public function getAuthorizedConnection()
    {
        if (!$this->authorizedConnection instanceof ConnectionInterface) {
            throw new RuntimeException("ConnectionInterface not found");
        }

        $headers = $this->getConnectionHeaders();

        foreach ($headers as $header) {
            if (str_contains($header, ': ')) {
                [$name, $value] = explode(': ', $header, 2);
                $this->authorizedConnection->addHeader($name, $value);
            }
        }

        return $this->authorizedConnection;
    }

    /**
     * @param Connection|ConnectionInterface $authorizedConnection
     * @return void
     */
    public function setAuthorizedConnection($authorizedConnection = null)
    {
        $this->authorizedConnection = $authorizedConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        if (!$this->connection instanceof ConnectionInterface) {
            throw new \RuntimeException("ConnectionInterface not found");
        }

        return $this->connection;
    }

    /**
     * @param Connection|ConnectionInterface $connection
     * @return void
     */
    public function setConnection($connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalConnectionHeaders()
    {
        return $this->additionalConnectionHeaders;
    }

    /**
     * @param array $additionalConnectionHeaders
     * @return void
     */
    public function setAdditionalConnectionHeaders($additionalConnectionHeaders = null)
    {
        $this->additionalConnectionHeaders = $additionalConnectionHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        if (!$this->token instanceof TokenInterface) {
            throw new \RuntimeException('TokenInterface not found');
        }

        return $this->token;
    }

    /**
     * @param Token|TokenInterface $token
     * @return void
     */
    public function setToken($token = null)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientId
     * @return void
     */
    public function setClientId($clientId = null)
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string|null $clientSecret
     * @return void
     */
    public function setClientSecret($clientSecret = null)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @return string|null
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param string|null $deviceId
     * @return void
     */
    public function setDeviceId($deviceId = null)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return string|null
     */
    public function getDeviceName()
    {
        return $this->deviceName;
    }

    /**
     * @param string|null $deviceName
     * @return void
     */
    public function setDeviceName($deviceName = null)
    {
        $this->deviceName = $deviceName;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BoxException
     */
    public function error(array $data, ?string $message = null, ?BoxResponseInterface $boxResponse = null): void
    {
        $error = $data['error'] ?? 'unknown_error';
        if (null === $message || !is_string($message)) {
            $message = $error;
        }
        $errorDescription = $data['error_description'] ?? $message;

        $exception = new BoxException($message);
        $exception->setError($error);
        $exception->setErrorDescription($errorDescription);

        if (array_key_exists('code', $data)) {
            $code = $data['code'];
            $exception->setBoxCode($code);
        }

        foreach ($data as $k => $v) {
            if ($k !== 'error' && $k !== 'error_description') {
                $exception->addContext($v, $k);
            }
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $context = [
                __METHOD__ . ":" . __LINE__,
                $data,
                $error,
                $errorDescription,
                $exception->getTraceAsString(),
            ];

            if ($boxResponse instanceof BoxResponseInterface) {
                $context['http_status'] = $boxResponse->getStatusCode();
                $context['response_body'] = $boxResponse->getContent();
            }

            $redactedContext = $this->getRedactor()->redactArray($context);

            $this->getLogger()->error($message, $redactedContext);
        }

        throw $exception;
    }

    /**
     * {@inheritdoc}
     */
    final public function putIntoBox($uri = null, $params = [], $returnType = 'decoded')
    {
        $this->validateReturnType($returnType);

        if (false === is_string($uri)) {
            throw new BadMethodCallException("please provide a URI");
        }

        if (!is_string($params)) {
            $params = json_encode($params);
        }

        $response = $this->getAuthorizedConnection()->put($uri, $params);
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'raw return: ' . $response,
                [
                    __METHOD__ . ":" . __LINE__,
                    var_export($response, true),
                ]
            );
        }

        return $this->handleBoxResponse($response, $returnType);
    }

    /**
     * {@inheritdoc}
     * @throws BadMethodCallException
     */
    final public function queryBox($uri = null, $returnType = 'decoded')
    {
        $this->validateReturnType($returnType);

        if (false === is_string($uri)) {
            throw new BadMethodCallException("please provide a URI");
        }

        $connection = $this->getAuthorizedConnection();

        $response = $connection->query($uri);
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'raw return: ' . $response,
                [
                    __METHOD__ . ":" . __LINE__,
                    var_export($response, true),
                ]
            );
        }

        // refactor method below to work with Response class
        return $this->handleBoxResponse($response, $returnType);
    }

    /**
     * {@inheritdoc}
     */
    final public function sendUpdateToBox(
        $uri = null,
        $params = [],
        $type = 'original',
        ?object $class = null
    ) {
        $this->validateReturnType($type);
        try {
            $boxData = $this->putIntoBox($uri, $params, $type);
        } catch (BoxResponseException $bre) {
            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->error(
                    'box exception caught',
                    [
                        __METHOD__ . ":" . __LINE__,
                        $bre->getTraceAsString(),
                        $bre->getBoxCode(),
                        $bre->getError(),
                        $bre->getErrorDescription(),
                        implode("\n", $bre->getContext()),
                    ]
                );
            }

            $callBackParams = [
                $uri,
                $params,
                $type,
            ];

            switch ($bre->getCode()) {
                case 401:
                    $boxData = $this->refreshConnection([$this, 'putIntoBox'], $callBackParams, $bre);
                    break;
                default:
                    throw $bre;
            }
        } catch (BoxException $be) {
            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->error(
                    'box exception caught',
                    [
                        __METHOD__ . ":" . __LINE__,
                        $be->getTraceAsString(),
                        $be->getBoxCode(),
                        $be->getError(),
                        $be->getErrorDescription(),
                        implode("\n", $be->getContext()),
                    ]
                );
            }

            throw $be;
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'final box data: ' . var_export($boxData, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        $returnData = null;
        if (null !== $class) {
            (new Hydrator())->hydrate($class, $boxData);
            $returnData = $class;
        } else {
            $returnData = $boxData;
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'return data: ' . var_export($returnData, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        return $returnData;
    }

    /**
     * {@inheritdoc}
     * @throws BoxException
     */
    final public function getFromBox($uri = null, $type = 'original', ?object $class = null)
    {
        $this->validateReturnType($type);

        try {
            $boxData = $this->queryBox($uri, $type);
        } catch (BoxResponseException $bre) {
            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->error(
                    'box exception caught',
                    [
                        __METHOD__ . ":" . __LINE__,
                        $bre->getTraceAsString(),
                        $bre->getBoxCode(),
                        $bre->getError(),
                        $bre->getErrorDescription(),
                        implode("\n", $bre->getContext()),
                    ]
                );
            }

            $callBackParams = [
                $uri,
                $type,
            ];

            switch ($bre->getCode()) {
                case 401:
                    $boxData = $this->refreshConnection([$this, 'queryBox'], $callBackParams, $bre);
                    break;
                default:
                    throw $bre;
            }
        } catch (BoxException $be) {
            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->error(
                    'box exception caught',
                    [
                        __METHOD__ . ":" . __LINE__,
                        $be->getTraceAsString(),
                        $be->getBoxCode(),
                        $be->getError(),
                        $be->getErrorDescription(),
                        implode("\n", $be->getContext()),
                    ]
                );
            }

            throw $be;
        }

        $returnData = null;

        if (null !== $class) {
            (new Hydrator())->hydrate($class, $boxData);
            $returnData = $class;
        } else {
            $returnData = $boxData;
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'return data: ' . var_export($returnData, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        return $returnData;
    }

    /**
     * {@inheritdoc}
     * @throws BoxException
     */
    public function getConnectionHeaders()
    {
        $headers = [$this->getAuthorizationHeader()];

        $additionalConnectionHeaders = $this->getAdditionalConnectionHeaders();

        if (null !== $additionalConnectionHeaders && !is_array($additionalConnectionHeaders)) {
            throw new BoxException('additional headers must be in array format', BoxException::INVALID_INPUT);
        }

        if (is_array($additionalConnectionHeaders)) {
            $headers = array_merge($headers, $additionalConnectionHeaders);
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'connection headers: ' . var_export($headers, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationHeader()
    {
        $token = $this->getToken();

        $header = "Authorization: Bearer " . $token->getAccessToken();

        return $header;
    }

    /**
     * this does not update the token storage with the refreshed token; that action is handled by user or a wrapped
     * method
     * {@inheritdoc}
     *
     * @throws BoxException
     */
    public function refreshToken(): TokenInterface
    {
        $token = $this->getToken();

        $params = [];
        $params['refresh_token'] = $token->getRefreshToken();
        $params['client_id'] = $this->getClientId();
        $params['client_secret'] = $this->getClientSecret();
        $params['grant_type'] = 'refresh_token';

        $deviceId = $this->getDeviceId();
        if (null !== $deviceId) {
            $params['device_id'] = $deviceId;
        }

        $deviceName = $this->getDeviceName();
        if (null !== $deviceName) {
            $params['device_name'] = $deviceName;
        }

        $connection = $this->getConnection();
        if ($this->getLogger() instanceof LoggerInterface) {
            $redactedParams = $this->getRedactor()->redactArray($params);
            $this->getLogger()->debug(
                'refresh token params: ' . var_export($redactedParams, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        $response = $connection->post(self::TOKEN_URI, $params);
        $json = $response->getContent();

        if ($this->getLogger() instanceof LoggerInterface) {
            $redactedJson = $this->getRedactor()->redactString($json);
            $this->getLogger()->debug(
                'raw refresh return: ' . var_export($redactedJson, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        if (!$response->isSuccessful()) {
            throw new BoxResponseException('Token refresh failed', $response->getStatusCode(), null, $response);
        }

        try {
            $decoded = $response->json(false);
            $flat = $response->json(true);
        } catch (BoxException $e) {
            $errorCheck = [];
            $errorCheck['error'] = "sdk_json_decode";
            $errorCheck['error_description'] = "unable to decode: " . $e->getMessage();
            $this->error($errorCheck, null, $response);
        }

        if (is_array($flat) && array_key_exists('error', $flat)) {
            $this->error($flat, null, $response);
        }

        $this->setTokenData($token, $decoded);

        $this->setToken($token);

        return $token;
    }

    /**
     * @param TokenInterface $token
     * @param mixed $data
     * @return TokenInterface
     */
    public function setTokenData(TokenInterface $token, $data): TokenInterface
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $redactedData = $this->getRedactor()->redactArray((array)$data);
            $this->getLogger()->debug(
                'token data: ' . var_export($redactedData, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        if (is_array($data)) {
            $token->setAccessToken($data['access_token']);
            $token->setExpiresIn($data['expires_in']);
            $token->setTokenType($data['token_type']);
            $token->setRefreshToken($data['refresh_token']);
        } else {
            if ($data instanceof stdClass) {
                $token->setAccessToken($data->access_token);
                $token->setRefreshToken($data->refresh_token);
                $token->setExpiresIn($data->expires_in);
                $token->setTokenType($data->token_type);
            } else {
                throw new RuntimeException('unexpected token data. unable to set. given ('
                    . var_export($data, true)
                    . ')');
            }
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function destroyToken(TokenInterface $token, $returnType = 'decoded')
    {
        $this->validateReturnType($returnType);
        $params['client_id'] = $this->getClientId();
        $params['client_secret'] = $this->getClientSecret();
        // The access_token or refresh_token to be destroyed. Only one is required, though both will be destroyed.
        $params['token'] = $token->getAccessToken();

        $connection = $this->getConnection();

        if ($this->getLogger() instanceof LoggerInterface) {
            $redactedParams = $this->getRedactor()->redactArray($params);
            $this->getLogger()->debug(
                'destroy token params: ' . var_export($redactedParams, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        $response = $connection->post(self::REVOKE_URI, $params);

        if (!$response->isSuccessful()) {
            throw new BoxResponseException('Token destruction failed', $response->getStatusCode(), null, $response);
        }

        $json = $response->getContent();
        // @todo add error handling for null data

        try {
            $decoded = $response->json();
            $flat = $response->json(true);
        } catch (\JsonException $e) {
            // Revoke often returns empty body on success, so we might need to handle that
            $decoded = new stdClass();
            $flat = [];
        }

        $data = match ($returnType) {
            'decoded' => $decoded,
            'flat', 'array' => $flat,
            'original' => $json,
            default => throw new OutOfBoundsException($returnType . " is not a valid result type."),
        };

        // remove token from storage
        // (De-coupled in 12.4: storage removal is deferred to orchestration layer)

        return $data;
    }

    /**
     * @param string $type
     * @return void
     */
    protected function validateReturnType($type = null)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('string type expected');
        }

        if ('array' === $type) {
            return;
        }

        if (!in_array($type, $this->allowedReturnTypes)) {
            $validTypes = implode(", ", $this->allowedReturnTypes);
            throw new OutOfBoundsException($type . " is not a valid result type. valid types: " . $validTypes);
        }
    }


    /**
     * @param ?BoxResponseInterface $response
     * @param string $returnType
     * @return mixed
     * @throws BoxResponseException
     */
    public function handleBoxResponse(?BoxResponseInterface $response = null, $returnType = 'decoded')
    {
        if (!$response instanceof BoxResponseInterface) {
            throw new BadMethodCallException("expecting instance of " . BoxResponseInterface::class . ". received: " . gettype($response));
        }

        // here is where we decide to throw exceptions based on response
        if (!$response->isSuccessful()) {
            throw $this->processResponseError($response);
        }

        $data = $this->handleResponseContent($returnType, $response);

        return $data;
    }

    /**
     * @param BoxResponseInterface $response
     * @return BoxResponseException
     */
    protected function processResponseError(BoxResponseInterface $response): BoxResponseException
    {
        $e = new BoxResponseException("Box Response was unsuccessful. ", $response->getStatusCode(), null, $response);

        // Handle Retry-After header
        $delay = $response->getRetryAfter();
        if (null !== $delay) {
            $e->addContext($response->getHeaderLine('Retry-After'), 'retry_after_header');
            $e->addContext($delay, 'retry_after_seconds');
        }

        return $e;
    }

    /**
     * @param array $callback
     * @param array $params
     * @param BoxResponseException|BoxException $be
     * @return array|mixed|stdClass|string
     * @throws BoxException
     */
    public function refreshConnection($callback, $params, $be = null)
    {
        $currentToken = clone $this->getToken();
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'currentToken: ' . var_export($currentToken, true),
                [
                    __METHOD__ . ":" . __LINE__,
                    $be->getTraceAsString(),
                    $be->getBoxCode(),
                ]
            );
        }

        try {
            $refreshedToken = $this->refreshToken();

            // (De-coupled in 12.4: storage update is deferred to orchestration layer)
            $this->setToken($refreshedToken);

            // retry query
            $boxData = call_user_func_array($callback, $params);
            //            $boxData = $this->putIntoBox($uri, $params, $type);

            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->debug(
                    'retry put return: ' . var_export($boxData, true),
                    [
                        __METHOD__ . ":" . __LINE__,
                    ]
                );
            }
        } catch (BoxException $refreshException) {
            $refreshMessage = "encountered exception during refresh token attempt: " . $refreshException->getMessage();
            $finalException = new BoxException($refreshMessage, $refreshException->getCode(), $be);
            $finalException->addContext($refreshException);
            $finalException->addContext($be);
            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->error(
                    $refreshMessage,
                    [
                        __METHOD__ . ":" . __LINE__,
                        $finalException->getTraceAsString(),
                        $refreshException->getBoxCode(),
                        $refreshException->getError(),
                        $refreshException->getErrorDescription(),
                        implode("\n", $refreshException->getContext()),
                    ]
                );
            }
            throw $finalException;
        }

        return $boxData;
    }

    /**
     * @param string $returnType
     * @param BoxResponseInterface|string $response
     * @return mixed
     * @throws BoxException
     * @deprecated v0.11.0 use handleBoxResponse or process modern responses directly
     */
    protected function handleResponseContent($returnType, $response): mixed
    {
        $this->validateReturnType($returnType);

        if ($response instanceof BoxResponseInterface) {
            $json = $response->getContent();
            $decoded = $response->json(false);
            $flat = $response->json(true);
        } else {
            $json = $response;
            $decoded = json_decode((string)$json);
            $flat = json_decode((string)$json, true);
        }

        $data = match ($returnType) {
            'decoded' => $decoded,
            'flat', 'array' => $flat,
            'original' => $json,
            default => throw new OutOfBoundsException($returnType . " is not a valid result type."),
        };

        if (null === $flat && '' !== (string)$json) {
            $this->error([
                'error' => "sdk_json_decode",
                'error_description' => "unable to decode or recursion level too deep",
            ]);
        }

        if (is_array($flat)) {
            if (array_key_exists('error', $flat)) {
                $this->error($flat);
            }

            if (array_key_exists('type', $flat) && 'error' === $flat['type']) {
                $errorData = [
                    'error' => "sdk_unknown",
                    'error_description' => "sdk_unknown",
                    'result_data' => $json,
                ];

                if (array_key_exists('code', $flat)) {
                    $errorData['code'] = $flat['code'];
                }

                $this->error($errorData);
            }
        }

        return $data;
    }

    /**
     * Hydrate a decoded payload into a class.
     *
     * @template T of object
     * @param class-string<T> $targetClass
     * @param array|stdClass $data
     * @return T
     */
    protected function hydrate(string $targetClass, array|stdClass $data): object
    {
        /** @var T */
        return (new Hydrator())->hydrate($targetClass, $data);
    }

    /**
     * Helper to get and hydrate a resource from Box.
     *
     * @template T of object
     * @param string $uri
     * @param class-string<T> $resourceClass
     * @return T
     * @throws BoxException
     */
    protected function getResourceFromBox(string $uri, string $resourceClass): object
    {
        $data = $this->getFromBox($uri, 'decoded');

        return $this->hydrate($resourceClass, $data);
    }

    /**
     * Helper to send an update to Box and hydrate the response.
     *
     * @template T of object
     * @param string $uri
     * @param array|string $params
     * @param class-string<T> $resourceClass
     * @return T
     * @throws BoxException
     */
    protected function sendUpdateAndHydrate(string $uri, array|string $params, string $resourceClass): object
    {
        $data = $this->sendUpdateToBox($uri, $params, 'decoded');

        return $this->hydrate($resourceClass, $data);
    }
}
