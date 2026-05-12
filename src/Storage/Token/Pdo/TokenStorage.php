<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/22/15
 * Time: 2:24 PM
 * @package     Box
 * @subpackage  Box_Storage
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

namespace Box\Storage\Token\Pdo;

use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\TokenStorageInterface;
use PDO;

/**
 * Class TokenStorage
 * @package Box\Storage\Pdo
 *
 * all string sql statements produced by this class auto-quote the columns and table
 * @todo    finish this class
 */
class TokenStorage implements TokenStorageInterface
{
    protected $dsn;
    protected $username;
    protected $password;
    protected $options = [];
    protected $pdo;

    protected $tokenTableName = 'box_token';
    protected $tokenTableId = [
        'access_token',
        'refresh_token',
    ];
    protected $previousToken;

    /**
     * {@inheritdoc}
     */
    public function getPreviousToken(): TokenInterface
    {
        throw new \RuntimeException('Method deprecated and removed in v1 TokenStorageInterface');
    }

    public function setPreviousToken(?TokenInterface $previousToken = null): void
    {
        throw new \RuntimeException('Method deprecated and removed in v1 TokenStorageInterface');
    }

    /**
     * map for persistence
     * @var array map contains the database column as the key and the token object getter method as the value.
     */
    protected $tokenMap = [
        'access_token' => 'getAccessToken',
        'refresh_token' => 'getRefreshToken',
        'grant_type' => 'getGrantType',
        'expires_in' => 'getExpiresIn',
        'token_type' => 'getTokenType',
        'restricted_to' => 'getRestrictedTo',
    ];
    protected $useCompositeKey = true;
    protected $tokenCompositeKeyMap = [
        'access_token' => 'getAccessToken',
        '',
    ];
    protected $additionalTokenTableData = [];

    /**
     * construct with pdo constructor arguments
     *
     * @param null $dsn
     * @param null $username
     * @param null $password
     * @param array $options
     */
    public function __construct($dsn = null, $username = null, $password = null, $options = [])
    {
        $this->setDsn($dsn);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setOptions($options);

        $pdo = new PDO($dsn, $username, $password, $options);

        $this->setPdo($pdo);
    }

    /**
     * @return mixed
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param mixed $dsn
     *
     * @return TokenStorage
     */
    public function setDsn($dsn = null)
    {
        $this->dsn = $dsn;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     *
     * @return TokenStorage
     */
    public function setUsername($username = null)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     *
     * @return TokenStorage
     */
    public function setPassword($password = null)
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return TokenStorage
     */
    public function setOptions(?array $options = null)
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param ?PDO $pdo
     *
     * @return TokenStorage
     */
    public function setPdo(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return mixed
     */
    public function getTokenTableName()
    {
        return $this->tokenTableName;
    }

    /**
     * @param mixed $tokenTableName
     *
     * @return TokenStorage
     */
    public function setTokenTableName($tokenTableName = null)
    {
        $this->tokenTableName = $tokenTableName;
    }

    /**
     * @return mixed
     */
    public function getTokenTableId()
    {
        return $this->tokenTableId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenTableId($tokenTableId = null)
    {
        $this->tokenTableId = $tokenTableId;
    }

    /**
     * @return mixed
     */
    public function getAdditionalTokenTableData()
    {
        return $this->additionalTokenTableData;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalTokenTableData(?array $additionalTokenTableData = null)
    {
        $this->additionalTokenTableData = $additionalTokenTableData;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseCompositeKey()
    {
        return $this->useCompositeKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseCompositeKey($useCompositeKey = null)
    {
        $this->useCompositeKey = $useCompositeKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenCompositeKeyMap()
    {
        return $this->tokenCompositeKeyMap;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenCompositeKeyMap($tokenCompositeKeyMap = null)
    {
        $this->tokenCompositeKeyMap = $tokenCompositeKeyMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenMap()
    {
        return $this->tokenMap;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenMap($tokenMap = null)
    {
        $this->tokenMap = $tokenMap;
    }

    /**
     * {@inheritdoc}
     */
    public function storeToken(TokenInterface $token, TokenStorageContext $context): void
    {
        // TODO: Implement storeToken() method in Step 12.3.
    }

    /**
     * {@inheritdoc}
     */
    public function updateToken(TokenInterface $token, TokenStorageContext $context): void
    {
        // TODO: Implement updateToken() method in Step 12.3.
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveToken(TokenStorageContext $context): ?TokenInterface
    {
        // TODO: Implement retrieveToken() method in Step 12.3.
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken(TokenStorageContext $context): void
    {
        // TODO: Implement removeToken() method in Step 12.3.
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        // TODO: Implement clear() method in Step 12.3.
    }
}
