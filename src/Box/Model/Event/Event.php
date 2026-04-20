<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/17/15
 * Time: 5:29 PM
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

namespace Box\Model\Event;

use Box\Model\Model;

class Event extends Model implements EventInterface
{
    protected mixed $type = null;

    protected mixed $eventId = null;

    protected mixed $createdBy = null;

    protected mixed $eventType = null;

    protected mixed $sessionId = null;

    protected mixed $source = null;

    /**
     * @return mixed
     */
    public function getType(): mixed
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return EventInterface
     */
    public function setType(mixed $type = null): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventId(): mixed
    {
        return $this->eventId;
    }

    /**
     * @param mixed $eventId
     *
     * @return EventInterface
     */
    public function setEventId(mixed $eventId = null): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     *
     * @return EventInterface
     */
    public function setCreatedBy(mixed $createdBy = null): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventType(): mixed
    {
        return $this->eventType;
    }

    /**
     * @param mixed $eventType
     *
     * @return EventInterface
     */
    public function setEventType(mixed $eventType = null): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionId(): mixed
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     *
     * @return EventInterface
     */
    public function setSessionId(mixed $sessionId = null): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource(): mixed
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     *
     * @return EventInterface
     */
    public function setSource(mixed $source = null): self
    {
        $this->source = $source;

        return $this;
    }
}