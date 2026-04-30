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
 *
 */

namespace Box\Event\Admin;

use Box\Event\Event;

/**
 * Class AdminEvent
 * @package Box\Event\Admin
 */
class AdminEvent extends Event implements AdminEventInterface
{
    private mixed $streamType = null;

    protected mixed $limit = 100;
    protected mixed $streamPosition = null;
    protected mixed $createdAfter = null;
    protected mixed $createdBefore = null;

    public function __construct(?array $options = null)
    {
        $this->streamType = self::STREAM_TYPE;
        if (null === $options) {
            $options = [];
        }
        parent::__construct($options);
    }

    /**
     * remove any attempt to map to the private property
     *
     * {@inheritdoc}
     * @param array|\stdClass $aData
     */
    public function mapBoxToClass(array|\stdClass $aData): void
    {
        // @todo need to refactor base model to explicitly take an array as the argument
        if (is_array($aData) && array_key_exists('stream_type', $aData)) {
            unset($aData['stream_type']);
        } else {
            if (is_object($aData)) {
                unset($aData->stream_type);
            }
        }

        parent::mapBoxToClass($aData);
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamType(): mixed
    {
        return $this->streamType;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(): mixed
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(mixed $limit = null): void
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamPosition(): mixed
    {
        return $this->streamPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function setStreamPosition(mixed $streamPosition = null): void
    {
        $this->streamPosition = $streamPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAfter(): mixed
    {
        return $this->createdAfter;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAfter(mixed $createdAfter = null): void
    {
        $this->createdAfter = $createdAfter;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBefore(): mixed
    {
        return $this->createdBefore;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedBefore(mixed $createdBefore = null): void
    {
        $this->createdBefore = $createdBefore;
    }

    // GET
}
