<?php
/**
 * We copied this file from Doctrine before we began using composer. Correctly attributing now.
 * will still need to keep this around for now due to other projects
 *
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 12:22 PM
 * @package     Box
 * @subpackage  Box_Collection
 *
 * Copyright (c) 2006-2013 Doctrine Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
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

namespace Box\Collection;

use Closure;
use ArrayIterator;

/**
 * @deprecated Use \Doctrine\Common\Collections\ArrayCollection instead.
 */
class ArrayCollection extends \Doctrine\Common\Collections\ArrayCollection implements ArrayCollectionInterface
{
    /**
     * Initializes a new ArrayCollectionInterface compatible class.
     *
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        parent::__construct($elements);
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->toArray();
    }

    /**
     * @param array $elements
     *
     * @return void
     */
    public function setElements($elements = null)
    {
        $this->clear();
        if (is_array($elements)) {
            foreach ($elements as $key => $value) {
                $this->set($key, $value);
            }
        }
    }
}