<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 6:48 PM
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

namespace Box\Model\Service\Event;


use Box\Model\Event\Collection\EventCollectionInterface;
use Box\Model\Service\ServiceInterface;
use Box\Model\Event\User\UserEventInterface;
use Box\Model\ModelInterface;

/**
 * Interface UserEventServiceInterface
 * @package Box\Model\Service\Event
 *
 * use magic method doc for IDEs to know they're getting a specific ModelInterface
 */
interface UserEventServiceInterface extends ServiceInterface
{
    const LIMIT_MAX = 800;
    const LIMIT_DEFAULT = 100;

    /**
     * @return array
     */
    public function getValidStreamTypes();

    /**
     * @return string
     */
    public function getStreamType();

    /**
     * @param string $streamType
     *
     * @return UserEventServiceInterface
     */
    public function setStreamType($streamType = null);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int|null $limit set null to reset to default value
     *
     * @return UserEventServiceInterface
     */
    public function setLimit($limit = null);

    /**
     * @return int
     */
    public function getStreamPosition();

    /**
     * @param int $streamPosition
     * @return UserEventServiceInterface
     */
    public function setStreamPosition($streamPosition = null);

    public function getEvents($type = 'decoded', EventCollectionInterface $eventCollection = null);
    public function getEventsUri();
}