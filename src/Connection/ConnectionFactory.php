<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/30/15
 * Time: 10:05 AM
 * @package     Box
 * @subpackage  Box_Connection
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

namespace Box\Connection;

use Box\Exception\BoxException;
use Box\Factory\AbstractFactory;
use Box\Model\Connection\ConnectionInterface;
use Box\Model\Connection\Token\TokenInterface;

class ConnectionFactory extends AbstractFactory
{
    /**
     * @param null $options
     *
     * @return ConnectionInterface
     */
    static public function getConnection($options = null)
    {
        return parent::get('Box\Model\Connection\Connection', $options);
    }

    /**
     * @param null|array $options
     *                   required key/values:
     *                   'token' => TokenInterface
     *
     *                   optional key/values:
     *                   'additionalHeaders' => array
     *
     * @return ConnectionInterface
     * @throws \Box\Exception\BoxException
     */
    static public function getAuthorizedConnection($options = null)
    {
        if (!array_key_exists('token', $options))
        {
            throw new BoxException('token expected to create an authorized connection');
        }

        if (!$options['token'] instanceof TokenInterface)
        {
            throw new BoxException('instance of Box\Model\Connection\Token\TokenInterface expected');
        }

        /**
         * @var TokenInterface $token
         */
        $token = $options['token'];

        unset($options['token']);
        if (!is_string($token->getAccessToken()))
        {
            throw new BoxException('TokenInterface::getAccessToken() does not contain a string access token');
        }

        $additionalHeaders = null;
        if (array_key_exists('additionalHeaders', $options))
        {
            $additionalHeaders = $options['additionalHeaders'];
            unset($options['additionalHeaders']);
            if (!is_array($additionalHeaders))
            {
                throw new BoxException('additionalHeaders option must be an array');
            }
        }

        $headers = array("Authorization: Bearer " . $token->getAccessToken());

        if (is_array($additionalHeaders))
        {
            $headers = array_merge($headers, $additionalHeaders);
        }

        $connection = static::getConnection($options);
        $connection->setCurlOpts(array('CURLOPT_HTTPHEADER' => $headers));

        return $connection;
    }
}