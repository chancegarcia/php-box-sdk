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

namespace Box\Model\Event\Collection;

use Box\Collection\ArrayCollection;
use Box\Collection\ArrayCollectionInterface;
use Box\Exception\BoxException;
use Box\Model\Model;
use Box\Model\ModelInterface;

class EventCollection extends Model implements EventCollectionInterface
{
    protected $chunkSize;
    protected $nextStreamPosition;

    /**
     * @var ArrayCollectionInterface
     */
    protected $entries;
    protected $originalEntries;

    /**
     * {@inheritdoc}
     */
    public function getOriginalEntries()
    {
        return $this->originalEntries;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalEntries($originalEntries = null)
    {
        $this->originalEntries = $originalEntries;

    }

    /**
     * {@inheritdoc}
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function setChunkSize($chunkSize = null)
    {
        $this->chunkSize = $chunkSize;

    }

    /**
     * {@inheritdoc}
     */
    public function getNextStreamPosition()
    {
        return $this->nextStreamPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextStreamPosition($nextStreamPosition = null)
    {
        $this->nextStreamPosition = $nextStreamPosition;

    }

    /**
     * {@inheritdoc}
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntries($entries = null)
    {
        if (is_array($entries))
        {
            $this->originalEntries = $entries;
            $entries = new ArrayCollection($entries);
        }
        else
        {
            if (!$entries instanceof ArrayCollectionInterface)
            {
                throw new BoxException('entries must be an array or instance of ArrayCollectionInterface');
            }
        }

        $this->entries = $entries;

    }

}