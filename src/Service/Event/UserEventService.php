<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 6:22 PM
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

namespace Box\Service\Event;

use Box\Exception\BoxException;
use Box\Event\Collection\EventCollection,

Box\Event\Collection\EventCollectionInterface;
use Box\Event\User\UserEventInterface;
use Box\Service\Service;
use Box\Event\EventInterface, Box\Event\Event;
use Box\Model\ModelInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UserEventService
 * @package Box\Service
 *
 * can use the @method for letting IDEs know that they will get back and EventInterface
 */
class UserEventService extends Service implements UserEventServiceInterface
{
    protected $validStreamTypes = array(
        'all'
        /* returns everything */
        ,
        'changes'
        /* returns tree changes */
        ,
        'sync'
        /* returns tree changes only for sync folders */
    );

    protected $streamType = "all";
    protected $streamPosition = 0;
    protected $limit = self::LIMIT_DEFAULT;

    /**
     * {@inheritdoc}
     */
    public function getValidStreamTypes()
    {
        return $this->validStreamTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamType()
    {
        return $this->streamType;
    }

    /**
     * {@inheritdoc}
     */
    public function setStreamType($streamType = null)
    {
        $validStreamTypes = $this->getValidStreamTypes();
        if (!in_array($streamType, $validStreamTypes)) {
            throw new BoxException("unexpect type ("
                                   . var_export($streamType, true)
                                   . ") valid types include: "
                                   . implode(", ", $validStreamTypes));
        }

        $this->streamType = $streamType;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit = null)
    {
        if (null === $limit) {
            $limit = self::LIMIT_DEFAULT;
        }

        if (!$this->isInt($limit)) {
            throw new BoxException('limit must be a valid integer value, (' . var_export($limit, true) . ') given');
        }

        if ($limit > self::LIMIT_MAX) {
            $limit = self::LIMIT_MAX;
        }

        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamPosition()
    {
        return $this->streamPosition;
    }

    /**
     * {@inheritdoc}
     * @throws \Box\Exception\BoxException
     */
    public function setStreamPosition($streamPosition = null)
    {
        if ("now" !== $streamPosition && !$this->isInt($streamPosition)) {
            throw new BoxException('limit must be a valid integer value or "now", ('
                                   . var_export($streamPosition, true)
                                   . ') given');
        }

        $this->streamPosition = $streamPosition;
    }

    public function getEvents($type = 'decoded', EventCollectionInterface $eventCollection = null)
    {
        $uri = $this->getEventsUri();

        $eventsData = $this->getFromBox($uri, $type);
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug(
                'events data: ' . var_export($eventsData, true),
                array(
                                          __METHOD__ . ":" . __LINE__,
                )
            );
        }

        $returnData = null;

        if ($eventCollection instanceof ModelInterface) {
            $returnData = $eventCollection->mapBoxToClass($eventsData);
        } else {
            $returnData = $this->getLastResult($type);
        }

        return $returnData;
    }

    public function getEventsUri()
    {
        $uri = UserEventInterface::URI . "?"
               . "stream_type=" . $this->getStreamType()
               . "&"
               . "stream_position=" . $this->getStreamPosition()
               . "&"
               . "limit=" . $this->getLimit();

        return $uri;
    }
}
