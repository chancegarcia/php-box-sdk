<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 12:24 PM
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

namespace Box\Event\Collection;

use Box\Exception\BoxException;
use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;
use Doctrine\Common\Collections\Collection;

class EventCollection implements EventCollectionInterface
{
    protected $chunkSize;
    protected $nextStreamPosition;

    /**
     * @var Collection
     */
    protected $entries;
    protected $originalEntries;

    /**
     * {@inheritdoc}
     */
    public function getOriginalEntries(): mixed
    {
        return $this->originalEntries;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalEntries($originalEntries = null): void
    {
        $this->originalEntries = $originalEntries;
    }

    /**
     * {@inheritdoc}
     */
    public function getChunkSize(): mixed
    {
        return $this->chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function setChunkSize($chunkSize = null): void
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStreamPosition(): mixed
    {
        return $this->nextStreamPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextStreamPosition($nextStreamPosition = null): void
    {
        $this->nextStreamPosition = $nextStreamPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntries($entries = null): void
    {
        if (is_array($entries)) {
            $this->originalEntries = $entries;
            $entries = new DoctrineArrayCollection($entries);
        } else {
            if (!$entries instanceof Collection) {
                throw new BoxException('entries must be an array or instance of \Doctrine\Common\Collections\Collection');
            }
        }

        $this->entries = $entries;
    }
}
