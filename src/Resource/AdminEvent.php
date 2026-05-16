<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/17/15
 * Time: 5:31 PM
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

class AdminEvent extends Event
{
    private string $streamType = "admin_logs";

    protected int $limit = 100;
    protected ?string $streamPosition = null;
    protected ?string $createdAfter = null;
    protected ?string $createdBefore = null;

    public function getStreamType(): string
    {
        return $this->streamType;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit = 100): void
    {
        $this->limit = $limit;
    }

    public function getStreamPosition(): ?string
    {
        return $this->streamPosition;
    }

    public function setStreamPosition(?string $streamPosition = null): void
    {
        $this->streamPosition = $streamPosition;
    }

    public function getCreatedAfter(): ?string
    {
        return $this->createdAfter;
    }

    public function setCreatedAfter(?string $createdAfter = null): void
    {
        $this->createdAfter = $createdAfter;
    }

    public function getCreatedBefore(): ?string
    {
        return $this->createdBefore;
    }

    public function setCreatedBefore(?string $createdBefore = null): void
    {
        $this->createdBefore = $createdBefore;
    }
}
