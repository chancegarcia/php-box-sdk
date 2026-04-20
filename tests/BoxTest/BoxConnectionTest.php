<?php
/**
 * @package
 * @subpackage
 * @author      Chance Garcia <chance@garcia.codes>
 * @copyright   (C)Copyright 2013-2017 Chance Garcia, chancegarcia.com
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2017 Chance Garcia
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

namespace BoxTest;

use Box\Model\Connection\Connection;
use Box\Model\Connection\ConnectionInterface;
use PHPUnit\Framework\TestCase;

class BoxConnectionTest extends TestCase
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = new Connection();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->connection = null;
    }

    public function testInitCurlReturnsResource()
    {
        $this->assertInstanceOf(\CurlHandle::class, $this->connection->initCurl());
    }

    public function testInitAdditionalCurlOptsReturnsResource()
    {
        $ch = $this->connection->initCurl();
        $this->assertInstanceOf(\CurlHandle::class, $this->connection->initAdditionalCurlOpts($ch));
    }

    // test query
    // test query calls init curl
    // test query calls init curl opts
    // test query calls init additional curl opts
    // test query calls get curl data

    // public function testPost()
    // {
    //
    // }
    //
    // public function testBuildQuery()
    // {
    //
    // }
    //
    // public function testPostFile()
    // {
    //
    // }
    //
    // public function testGetMimeType()
    // {
    //
    // }
    //
    // public function testCreateCurlFile()
    // {
    //
    // }
}