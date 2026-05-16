<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 6:48 PM
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

namespace Box\Service\Event;

use Box\Dto\Event\EventResponse;
use Box\Service\ServiceInterface;
use Box\Mapper\EventResponseMapper;

interface UserEventServiceInterface extends ServiceInterface
{
    public const int LIMIT_MAX = 800;
    public const int LIMIT_DEFAULT = 100;

    /**
     * @return array<string>
     */
    public function getValidStreamTypes(): array;

    public function getStreamType(): string;

    public function setStreamType(?string $streamType = null): void;

    public function getLimit(): int;

    /**
     * @param string|int|null $limit set null to reset to default value
     */
    public function setLimit(string|int|null $limit = null): void;

    public function getStreamPosition(): string|int;

    public function setStreamPosition(string|int|null $streamPosition = null): void;

    public function getEvents(): EventResponse;

    public function getEventsUri(): string;

    public function setEventResponseMapper(EventResponseMapper $mapper): void;
}
