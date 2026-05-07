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
use Box\Exception\TokenStorageException;
use Box\Http\Response\BoxResponseInterface;
use Box\Model\BaseModel;
use Box\Exception\BoxException;
use Box\Connection\Connection,

Box\Connection\ConnectionInterface;
use Box\Connection\Token\Token, Box\Connection\Token\TokenInterface;
use Box\Model\ModelInterface;
use Box\Storage\Token\BaseTokenStorageInterface;
use OutOfBoundsException;
use RuntimeException;
use InvalidArgumentException;
use BadMethodCallException;
use stdClass;
use Psr\Log\LoggerInterface;

class Service extends BaseModel implements ServiceInterface
{
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

    /**
     * @var BaseTokenStorageInterface
     */
    protected $tokenStorage;

    protected $tokenStorageContext;

    protected $clientId;
    protected $clientSecret;
    protected $deviceId = null;
    protected $deviceName = null;

    protected $lastResultOriginal;
    protected $lastResultDecoded;
    protected $lastResultFlat;
    protected $defaultReturnType = 'decoded';
    private $allowedReturnTypes = [
        'decoded',
        'original',
        'flat',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefaultReturnType()
    {
        return $this->defaultReturnType;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultReturnType($defaultReturnType = 'decoded')
    {
        $this->validateReturnType($defaultReturnType);

        $this->defaultReturnType = $defaultReturnType;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastResult($type = 'decoded')
    {
        $this->validateReturnType($type);

        $prop = "lastResult" . ucfirst($type);

        return $this->{$prop};
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizedConnection()
    {
        if (!$this->authorizedConnection instanceof ConnectionInterface) {
            throw new RuntimeException("ConnectionInterface not found");
        }

        $headers = $this->getConnectionHeaders();

        $this->authorizedConnection->setCurlOpts(['CURLOPT_HTTPHEADER' => $headers]);

        return $this->authorizedConnection;
    }

    /**
     * {@inheritdoc}
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
     *
     * @return ServiceInterface|Service
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setClientId($clientId = null)
    {
        $this->clientId = $clientId;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientSecret($clientSecret = null)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     *{@inheritdoc}
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeviceId($deviceId = null)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeviceName()
    {
        return $this->deviceName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeviceName($deviceName = null)
    {
        $this->deviceName = $deviceName;
    }

    /**
     *{@inheritdoc}
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenStorage(?BaseTokenStorageInterface $tokenStorage = null)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenStorageContext()
    {
        return $this->tokenStorageContext;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenStorageContext($tokenStorageContext = null)
    {
        $this->tokenStorageContext = $tokenStorageContext;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Box\Exception\BoxException
     */
    public function error($data, $message = null)
    {
        $error = $data['error'];
        unset($data['error']);
        if (null === $message || !is_string($message)) {
            $message = $error;
        }
        $errorDescription = $data['error_description'];
        unset($data['error_description']);

        $exception = new BoxException($message);
        $exception->setError($error);
        $exception->setErrorDescription($errorDescription);

        if (array_key_exists('code', $data)) {
            $code = $data['code'];
            $exception->setBoxCode($code);
        }

        foreach ($data as $k => $v) {
            $exception->addContext($v, $k);
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->error(
                $message,
                [
                    __METHOD__ . ":" . __LINE__,
                    $data,
                    $error,
                    $errorDescription,
                    $exception->getTraceAsString(),
                ]
            );
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
        ?ModelInterface $class = null
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

        $errorCheck = $this->getLastResult('flat');

        /**
         * error decoding json data
         */
        if (null === $errorCheck) {
            $errorData['error'] = "unable to decode json data";
            $errorData['error_description'] = 'please check the logs for more details';
            $this->error($errorData);
        }

        $returnData = null;
        if ($class instanceof ModelInterface) {
            $returnData = $class->mapBoxToClass($this->getLastResult('decoded'));
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
     * this will attempt to retrieve from box and refresh the token if necessary then update the token storage
     *
     * {@inheritdoc}
     * @throws \Box\Exception\BoxException|\Box\Exception\TokenStorageException for TokenStorageException, we will set
     *     previous token information here if it isn't set already from the TokenStorageException. then rethrow; Token
     *     storage is expected to set all other context values for information.
     */
    final public function getFromBox($uri = null, $type = 'original', ?ModelInterface $class = null)
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

        $errorCheck = $this->getLastResult('flat');
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'error check: ' . var_export($errorCheck, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        /**
         * error decoding json data
         */
        if (null === $errorCheck) {
            $errorData['error'] = "unable to decode json data";
            $errorData['error_description'] = 'try refreshing the token';
            $this->error($errorData);
        }

        $returnData = null;
        $decodedBoxData = $this->getLastResult('decoded');

        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'final box data: ' . var_export($decodedBoxData, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        if ($class instanceof ModelInterface) {
            $returnData = $class->mapBoxToClass($boxData);
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
     * @throws \Box\Exception\BoxException
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
     * @throws \Box\Exception\BoxException
     */
    public function refreshToken()
    {
        $token = $this->getToken();

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
            $this->getLogger()->debug(
                'refresh token params: ' . var_export($params, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        $response = $connection->post(self::TOKEN_URI, $params);
        $json = $response->getContent();
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'raw refresh return: ' . var_export($json, true),
                [
                    __METHOD__ . ":" . __LINE__,
                ]
            );
        }

        // need to handle stdclass vs forced array?
        $this->lastResultOriginal = $json;
        $this->lastResultDecoded = json_decode($json);
        $this->lastResultFlat = json_decode($json, true);

        $data = $this->getLastResult($this->getDefaultReturnType());
        $errorCheck = $this->getLastResult('flat');

        if (null === $errorCheck) {
            $errorCheck['error'] = "sdk_json_decode";
            $errorCheck['error_description'] = "unable to decode or recursion level too deep";
            $this->error($errorCheck);
        } else {
            if (array_key_exists('error', $errorCheck)) {
                $this->error($errorCheck);
            }
        }

        $this->setTokenData($token, $data);

        $this->setToken($token);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenData(TokenInterface $token, $data)
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'token data: ' . var_export($data, true),
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

        $response = $connection->post(self::REVOKE_URI, $params);
        $json = $response->getContent();
        // @todo add error handling for null data
        $this->lastResultOriginal = $json;
        $this->lastResultDecoded = json_decode($json);
        $this->lastResultFlat = json_decode($json, true);

        $data = $this->getLastResult($returnType);

        // remove token from storage
        $this->getTokenStorage()->removeToken($token, $this->getTokenStorageContext());

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function validateReturnType($type = null)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('string type expected');
        }

        if (!in_array($type, $this->allowedReturnTypes)) {
            $validTypes = explode(",", $this->allowedReturnTypes);
            throw new OutOfBoundsException($type . " is not a valid result type. valid types: " . $validTypes);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function handleBoxResponse(?BoxResponseInterface $response = null, $returnType = 'decoded')
    {
        if (!$response instanceof BoxResponseInterface) {
            throw new BadMethodCallException("expecting instance of Box\\Http\\BoxResponseInterface. received: " . gettype($response));
        }

        // here is where we decide to throw exceptions based on response
        if (!$response->isSuccessful()) {
            $e = new BoxResponseException("Box Response was unsuccessful. ", $response->getStatusCode(), null, $response);

            // Handle Retry-After header
            if ($response->getStatusCode() === 429 && $response->hasHeader('Retry-After')) {
                $retryAfter = $response->getHeaderLine('Retry-After');
                $delay = 0;
                if (is_numeric($retryAfter)) {
                    $delay = (int)$retryAfter;
                } else {
                    $retryTime = strtotime($retryAfter);
                    if ($retryTime !== false) {
                        $delay = max(0, $retryTime - time());
                    }
                }
                $e->addContext($retryAfter, 'retry_after_header');
                $e->addContext($delay, 'retry_after_seconds');
            }

            throw $e;
        }

        $json = $response->getContent();

        $data = $this->handleResponseContent($returnType, $json);

        return $data;
    }

    /**
     * @param array $callback
     * @param array $params
     * @param BoxResponseException|BoxException $be
     * @return array|mixed|stdClass|string
     * @throws BoxException
     * @throws TokenStorageException
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
            // set previous token information for token storage to use if needed
            $this->getTokenStorage()->setPreviousToken($currentToken);
            $refreshedToken = $this->refreshToken();
            $tokenStorageContext = $this->getTokenStorageContext();

            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->debug(
                    'token storage context: ' . var_export($tokenStorageContext, true),
                    [
                        __METHOD__ . ":" . __LINE__,
                    ]
                );
            }

            $this->getTokenStorage()->updateToken($refreshedToken, $tokenStorageContext);
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
            $this->getTokenStorage()->setPreviousToken(null);
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
        } catch (TokenStorageException $tse) {
            // add some context if none already given and rethrow
            if (!$tse->getPreviousToken() instanceof TokenInterface) {
                $tse->setPreviousToken($currentToken);
            }

            if ($this->getLogger() instanceof LoggerInterface) {
                $this->getLogger()->error(
                    "token storage exception: " . $tse->getMessage(),
                    [
                        __METHOD__ . ":" . __LINE__,
                        $tse->getTraceAsString(),
                        var_export($tse, true),
                    ]
                );
            }

            throw $tse;
        }

        return $boxData;
    }

    /**
     * @param $returnType
     * @param $json
     * @return mixed
     * @throws BoxException
     */
    public function handleResponseContent($returnType, $json)
    {
        $this->validateReturnType($returnType);

        $this->lastResultOriginal = $json;
        $this->lastResultDecoded = json_decode($json);
        $this->lastResultFlat = json_decode($json, true);

        $data = $this->getLastResult($returnType);
        $errorData = [];

        if (null === $this->lastResultFlat) {
            $errorData['error'] = "sdk_json_decode";
            $errorData['error_description'] = "unable to decode or recursion level too deep";
            $this->error($errorData);
        } else {
            if (array_key_exists('error', $this->lastResultFlat)) {
                $this->error($this->lastResultFlat);
            } else {
                if (array_key_exists('type', $this->lastResultFlat) && 'error' == $this->lastResultFlat['type']) {
                    $errorData['error'] = "sdk_unknown";
                    $ditto = $errorData;
                    $errorData['error_description'] = $ditto;
                    $errorData['result_data'] = $this->lastResultOriginal;

                    if (array_key_exists('code', $this->lastResultFlat)) {
                        $errorData['code'] = $this->lastResultFlat['code'];
                    }

                    $this->error($errorData);
                }
            }
        }

        return $data;
    }
}
