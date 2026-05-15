<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/17/15
 * Time: 5:31 PM
 *
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
 */

namespace Box\Resource;

use Box\Mapper\Hydrator;
use stdClass;

class AdminEvent extends Event
{
    private mixed $streamType = "admin_logs";

    protected mixed $limit = 100;
    protected mixed $streamPosition = null;
    protected mixed $createdAfter = null;
    protected mixed $createdBefore = null;

    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            (new Hydrator())->hydrate($this, $options);
        }
    }

    /**
     * @param array|stdClass $aData
     * @deprecated Use Hydrator::hydrate() instead.
     */
    public function mapBoxToClass(array|stdClass $aData): void
    {
        if (is_array($aData)) {
            unset($aData['stream_type']);
        } elseif (is_object($aData)) {
            unset($aData->stream_type);
        }

        (new Hydrator())->hydrate($this, $aData);
    }

    /**
     * @return mixed
     */
    public function getStreamType(): mixed
    {
        return $this->streamType;
    }

    /**
     * @return mixed
     */
    public function getLimit(): mixed
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     * @return void
     */
    public function setLimit(mixed $limit = null): void
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getStreamPosition(): mixed
    {
        return $this->streamPosition;
    }

    /**
     * @param mixed $streamPosition
     * @return void
     */
    public function setStreamPosition(mixed $streamPosition = null): void
    {
        $this->streamPosition = $streamPosition;
    }

    /**
     * @return mixed
     */
    public function getCreatedAfter(): mixed
    {
        return $this->createdAfter;
    }

    /**
     * @param mixed $createdAfter
     * @return void
     */
    public function setCreatedAfter(mixed $createdAfter = null): void
    {
        $this->createdAfter = $createdAfter;
    }

    /**
     * @return mixed
     */
    public function getCreatedBefore(): mixed
    {
        return $this->createdBefore;
    }

    /**
     * @param mixed $createdBefore
     * @return void
     */
    public function setCreatedBefore(mixed $createdBefore = null): void
    {
        $this->createdBefore = $createdBefore;
    }
}
