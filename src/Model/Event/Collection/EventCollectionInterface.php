<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 12:26 PM
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

use Box\Collection\ArrayCollectionInterface;
use Box\Model\ModelInterface;

interface EventCollectionInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getChunkSize();

    /**
     * @param mixed $chunkSize
     *
     * @return EventCollectionInterface|EventCollection
     */
    public function setChunkSize($chunkSize = null);

    /**
     * @return mixed
     */
    public function getNextStreamPosition();

    /**
     * @param mixed $nextStreamPosition
     *
     * @return EventCollectionInterface|EventCollection
     */
    public function setNextStreamPosition($nextStreamPosition = null);

    /**
     * @return ArrayCollectionInterface
     */
    public function getEntries();

    /**
     * @param ArrayCollectionInterface|array $entries
     *
     * @return EventCollectionInterface
     */
    public function setEntries($entries = null);

    /**
     * @return mixed
     */
    public function getOriginalEntries();

    /**
     * @param mixed $originalEntries
     *
     * @return EventCollectionInterface
     */
    public function setOriginalEntries($originalEntries = null);
}