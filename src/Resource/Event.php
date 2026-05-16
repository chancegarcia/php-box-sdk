<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/17/15
 * Time: 5:29 PM
 *
 * @package     Box
 * @subpackage  Box_Model
 *
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

class Event
{
    protected ?string $type = null;

    protected string|int|null $eventId = null;

    // mixed: hydrator may deliver a User array or User object depending on API response shape
    protected mixed $createdBy = null;

    protected ?string $eventType = null;

    protected ?string $sessionId = null;

    // mixed: source can be a File, Folder, User, Comment, or other resource type depending on the event
    protected mixed $source = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type = null): void
    {
        $this->type = $type;
    }

    public function getEventId(): string|int|null
    {
        return $this->eventId;
    }

    public function setEventId(string|int|null $eventId = null): void
    {
        $this->eventId = $eventId;
    }

    public function getCreatedBy(): mixed
    {
        return $this->createdBy;
    }

    public function setCreatedBy(mixed $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType = null): void
    {
        $this->eventType = $eventType;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId = null): void
    {
        $this->sessionId = $sessionId;
    }

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function setSource(mixed $source = null): void
    {
        $this->source = $source;
    }
}
