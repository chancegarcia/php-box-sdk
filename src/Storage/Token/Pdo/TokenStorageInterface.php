<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/22/15
 * Time: 2:17 PM
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

use Box\Storage\Token\BaseTokenStorageInterface;
use PDO;

interface TokenStorageInterface extends BaseTokenStorageInterface
{
    public function getDsn();

    public function setDsn($dsn = null);

    public function getUsername();

    public function setUsername($username = null);

    public function getPassword();

    public function setPassword($password = null);

    public function getOptions();

    public function setOptions(array $options = null);

    public function getPdo();

    public function setPdo(PDO $pdo = null);

    public function getTokenTableName();

    public function setTokenTableName($tokenTableName = null);

    /**
     * @param null $tokenTableId
     *
     * @return TokenStorageInterface;
     */
    public function setTokenTableId($tokenTableId = null);

    public function getAdditionalTokenTableData();

    /**
     * @param array|\Traversable $additionalTokenTableData
     *
     * @return TokenStorageInterface
     */
    public function setAdditionalTokenTableData(array $additionalTokenTableData = null);

    /**
     * @return boolean
     */
    public function isUseCompositeKey();

    /**
     * @param boolean $useCompositeKey
     *
     * @return TokenStorageInterface
     */
    public function setUseCompositeKey($useCompositeKey = null);

    /**
     * @return array|\Traversable
     */
    public function getTokenCompositeKeyMap();

    /**
     * @param array|\Traversable $tokenCompositeKeyMap
     *
     * @return TokenStorageInterface
     */
    public function setTokenCompositeKeyMap($tokenCompositeKeyMap = null);

    /**
     * @return array|\Traversable
     */
    public function getTokenMap();

    /**
     * @param array|\Traversable $tokenMap
     *
     * @return TokenStorageInterface
     */
    public function setTokenMap($tokenMap = null);
}