<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 2:58 PM
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

namespace Box\Model\Service;

use Box\Http\Response\BoxResponseInterface;
use Box\Model\BaseModelInterface;
use Box\Model\Connection\Connection;
use Box\Model\Connection\ConnectionInterface;
use Box\Model\Connection\Token\Token;
use Box\Model\Connection\Token\TokenInterface;
use Box\Model\Model;
use Box\Model\ModelInterface;
use Box\Storage\Token\BaseTokenStorageInterface;
use OutOfBoundsException;
use RuntimeException;
use InvalidArgumentException;
use BadMethodCallException;
use stdClass;

/**
 * basic service interface expects a valid authorized token;
 * it has the ability to refresh or revoke but not do the initial authorization.
 * see the authorize service for that ability
 *
 *
 *
 * Interface ServiceInterface
 * @package Box\Model\Service
 */
interface ServiceInterface extends BaseModelInterface
{
    const TOKEN_URI = "https://www.box.com/api/oauth2/token";
    const REVOKE_URI = "https://www.box.com/api/oauth2/revoke";

    /**
     * @return Connection|ConnectionInterface
     */
    public function getConnection();

    /**
     * @param Connection|ConnectionInterface $connection
     *
     * @return ServiceInterface|Service
     */
    public function setConnection($connection = null);

    /**
     * @return Connection|ConnectionInterface
     * @throws RuntimeException
     */
    public function getAuthorizedConnection();

    /**
     * @param Connection|ConnectionInterface $authorizedConnection
     *
     * @return ServiceInterface|Service
     */
    public function setAuthorizedConnection($authorizedConnection = null);

    /**
     * @return array
     */
    public function getAdditionalConnectionHeaders();

    /**
     * @param array $additionalConnectionHeaders
     *
     * @return ServiceInterface|Service
     */
    public function setAdditionalConnectionHeaders($additionalConnectionHeaders = null);

    /**
     * @return Token|TokenInterface
     */
    public function getToken();

    /**
     * @param Token|TokenInterface $token
     *
     * @return ServiceInterface|Service
     */
    public function setToken($token = null);

    /**
     * @return mixed
     */
    public function getClientId();

    /**
     * @param mixed $clientId
     *
     * @return ServiceInterface|Service
     */
    public function setClientId($clientId = null);

    /**
     * @param mixed $clientSecret
     *
     * @return ServiceInterface|Service
     */
    public function setClientSecret($clientSecret = null);

    /**
     * @return mixed
     */
    public function getClientSecret();

    /**
     * @return mixed
     */
    public function getDeviceId();

    /**
     * @param mixed $deviceId
     *
     * @return ServiceInterface|Service
     */
    public function setDeviceId($deviceId = null);

    /**
     * @return mixed
     */
    public function getDeviceName();

    /**
     * @param mixed $deviceName
     *
     * @return ServiceInterface|Service
     */
    public function setDeviceName($deviceName = null);

    /**
     * used to throw exceptions that need to contain error information returned from Box
     *
     * @param $data    array containing error and error_description keys
     * @param $message string exception message
     *
     */
    public function error($data, $message = null);

    /**
     * @return string
     */
    public function getAuthorizationHeader();

    /**
     * @return array
     */
    public function getConnectionHeaders();

    /**
     * Handle BoxResponse by throwing an exception on error or returning the JSON return content
     *
     * @param BoxResponseInterface $response
     * @param string $returnType valid types are:
     *                           'original' (the return from the connection query {@see Connection::query()}),
     *                           'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                           'flat' (associative array json decode of the connection query [json_decode(original,
     *                           true)])
     *
     * @return mixed
     * @throws \Box\Exception\BoxException
     * @throws BadMethodCallException
     */
    public function handleBoxResponse(BoxResponseInterface $response = null, $returnType = 'decoded');

    /**
     * @param null $json
     * @param string $returnType valid types are:
     *                           'original' (the return from the connection query {@see Connection::query()}),
     *                           'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                           'flat' (associative array json decode of the connection query [json_decode(original,
     *                           true)])
     * @param array $errorData
     * @return mixed
     *
     * @deprecated
     */
    public function getFinalConnectionResult($json = null, $returnType = 'decoded', $errorData = array());

    /**
     * @param null $uri
     * @param array $params name/value array pairs that will be json_encoded to send to box
     * @param string $returnType valid types are:
     *                           'original' (the return from the connection query {@see Connection::query()}),
     *                           'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                           'flat' (associative array json decode of the connection query [json_decode(original,
     *                           true)])
     *
     * @return string|array|stdClass
     *
     * @throws BadMethodCallException
     */
    public function putIntoBox($uri = null, $params = array(), $returnType = 'decoded');

    /**
     *
     * use box connection object to send a query to box
     *
     * @param string $uri
     * @param string $returnType valid types are:
     *                           'original' (the return from the connection query {@see Connection::query()}),
     *                           'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                           'flat' (associative array json decode of the connection query [json_decode(original,
     *                           true)])
     *
     * @return string|array|stdClass
     *
     * @throws BadMethodCallException
     */
    public function queryBox($uri = null, $returnType = 'decoded');

    /**
     * query box and map return values to a given class
     *
     * @param null $uri box uri to query
     * @param string $type valid types are:
     *                              'original' (the return from the connection query {@see Connection::query()}),
     *                              'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                              'flat' (associative array json decode of the connection query [json_decode(original, true)])
     *                              'mapped' map json data to provided ModelInterface
     * @param ModelInterface $class class to map the box data to, the mapped data is the decoded results of the the box
     *                              query {@see queryBox()}; if none provided, the specified type will be returned
     *
     * @return ModelInterface|Model
     */
    public function getFromBox($uri = null, $type = 'original', ModelInterface $class = null);

    /**
     * @param null $uri box uri to query
     * @param array $params array of params to be converted to json encoded string
     * @param string $type valid types are:
     *                              'original' (the return from the connection query {@see Connection::query()}),
     *                              'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                              'flat' (associative array json decode of the connection query
     *                              [json_decode(original, true)])
     * @param ModelInterface $class class to map the box data to, the mapped data is the decoded results of the the box
     *                              query {@see queryBox()}; if none provided, the specified type will be returned
     *
     * @return ModelInterface|stdClass|array|string
     * @throws \Box\Exception\BoxException
     * @throws \Box\Exception\TokenStorageException
     * @throws \Exception
     */
    public function sendUpdateToBox($uri = null, $params = array(), $type = 'original', ModelInterface $class = null);

    /**
     * refreshes the token and returns new token; it is up to the application to persist the new token data
     *
     * @return Token|TokenInterface
     */
    public function refreshToken();

    /**
     * @param $token TokenInterface|Token
     * @param $data  array|stdClass data must be a flat result or stdClass
     *
     * @return TokenInterface|Token
     */
    public function setTokenData(TokenInterface $token, $data);

    /**
     * @param $token TokenInterface|Token
     *
     * @return mixed
     */
    public function destroyToken(TokenInterface $token);

    /**
     * @return BaseTokenStorageInterface
     */
    public function getTokenStorage();

    /**
     * @param BaseTokenStorageInterface|null $tokenStorage
     *
     * @return ServiceInterface
     */
    public function setTokenStorage(BaseTokenStorageInterface $tokenStorage = null);

    /**
     * @return mixed
     */
    public function getTokenStorageContext();

    /**
     * @param mixed $tokenStorageContext
     *
     * @return ServiceInterface
     */
    public function setTokenStorageContext($tokenStorageContext = null);

    /**
     * @param string $type
     *
     * @return mixed
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function getLastResult($type = 'decoded');

    /**
     * @return string
     */
    public function getDefaultReturnType();

    /**
     * @param string $defaultReturnType
     *
     * @return ServiceInterface
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function setDefaultReturnType($defaultReturnType = 'decoded');

    /**
     * @param string $type
     *
     * @return ServiceInterface
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function validateReturnType($type = null);
}