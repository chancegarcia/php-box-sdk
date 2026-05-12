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

namespace Box\Service;

use Box\Http\Response\BoxResponseInterface;
use Box\Connection\Token\TokenInterface;
use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\Token;
use RuntimeException;
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
 * @package Box\Service
 */
interface ServiceInterface
{
    public const TOKEN_URI = "https://www.box.com/api/oauth2/token";
    public const REVOKE_URI = "https://www.box.com/api/oauth2/revoke";

    /**
     * @return Connection|ConnectionInterface
     */
    public function getConnection();

    /**
     * @param Connection|ConnectionInterface $connection
     * @return void
     */
    public function setConnection($connection = null);

    /**
     * @return Connection|ConnectionInterface
     * @throws RuntimeException
     */
    public function getAuthorizedConnection();

    /**
     * @param Connection|ConnectionInterface $authorizedConnection
     * @return void
     */
    public function setAuthorizedConnection($authorizedConnection = null);

    /**
     * @return Token|TokenInterface
     */
    public function getToken();

    /**
     * @param Token|TokenInterface $token
     * @return void
     */
    public function setToken($token = null);

    /**
     * @return mixed
     */
    public function getClientId();

    /**
     * @param string|null $clientId
     * @return void
     */
    public function setClientId($clientId = null);

    /**
     * @param string|null $clientSecret
     * @return void
     */
    public function setClientSecret($clientSecret = null);

    /**
     * @return string|null
     */
    public function getClientSecret();

    /**
     * @return string|null
     */
    public function getDeviceId();

    /**
     * @param string|null $deviceId
     * @return void
     */
    public function setDeviceId($deviceId = null);

    /**
     * @return string|null
     */
    public function getDeviceName();

    /**
     * @param string|null $deviceName
     * @return void
     */
    public function setDeviceName($deviceName = null);

    /**
     * used to throw exceptions that need to contain error information returned from Box
     *
     * @param array $data containing error and error_description keys
     * @param string|null $message
     * @param BoxResponseInterface|null $boxResponse
     *
     */
    public function error(array $data, ?string $message = null, ?BoxResponseInterface $boxResponse = null): void;

    /**
     * @deprecated since v0.11.0, will be removed in v1.0.0. Use Connection::getAuthorizationHeader() via connection.
     * @return string
     */
    public function getAuthorizationHeader();

    /**
     * @deprecated since v0.11.0, will be removed in v1.0.0. Connection now automatically applies auth headers when an access token is set.
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
    public function handleBoxResponse(?BoxResponseInterface $response = null, $returnType = 'decoded');


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
    public function putIntoBox($uri = null, $params = [], $returnType = 'decoded');

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
     * @param null $uri box uri to query
     * @param string $type valid types are:
     *                              'original' (the return from the connection query {@see Connection::query()}),
     *                              'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                              'flat' (associative array json decode of the connection query [json_decode(original, true)])
     *                              'mapped' map json data to provided ModelInterface
     * @param object|string|null $class class to map the box data to, the mapped data is the decoded results of the the box
     *                              query {@see queryBox()}; if none provided, the specified type will be returned
     *
     * @return object|stdClass|array|string
     */
    public function getFromBox($uri = null, $type = 'original', ?object $class = null);

    /**
     * @param null $uri box uri to query
     * @param array $params array of params to be converted to json encoded string
     * @param string $type valid types are:
     *                              'original' (the return from the connection query {@see Connection::query()}),
     *                              'decoded' (normal json decode of the connection query [json_decode(original)]),
     *                              'flat' (associative array json decode of the connection query
     *                              [json_decode(original, true)])
     * @param object|null $class class to map the box data to, the mapped data is the decoded results of the the box
     *                              query {@see queryBox()}; if none provided, the specified type will be returned
     *
     * @return object|stdClass|array|string
     * @throws \Box\Exception\BoxException
     * @throws \Exception
     */
    public function sendUpdateToBox($uri = null, $params = [], $type = 'original', ?object $class = null);

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
}
