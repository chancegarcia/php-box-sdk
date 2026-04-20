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

class ArrayCollection implements ArrayCollectionInterface
{
    protected $entries = array();

    /**
     * Initializes a new ArrayCollectionInterface compatible class.
     *
     * @param array $elements
     *
     * @return ArrayCollectionInterface|ArrayCollection
     */
    public function __construct(array $elements = array())
    {
        $this->entries = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->entries;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->entries;
    }

    /**
     * @param array $elements
     *
     * @return ArrayCollectionInterface|ArrayCollection
     */
    public function setElements($elements = null)
    {
        $this->entries = $elements;

    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return reset($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if (!isset($this->entries[$key]) && !array_key_exists($key, $this->entries))
        {
            return null;
        }

        $removed = $this->entries[$key];
        unset($this->entries[$key]);

        return $removed;
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        $key = array_search($element, $this->entries, true);

        if ($key === false)
        {
            return false;
        }

        unset($this->entries[$key]);

        return true;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset))
        {
            return $this->add($value);
        }

        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return isset($this->entries[$key]) || array_key_exists($key, $this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return in_array($element, $this->entries, true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        foreach ($this->entries as $key => $element)
        {
            if ($p($key, $element))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return array_search($element, $this->entries, true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return isset($this->entries[$key]) ? $this->entries[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->entries[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->entries[] = $value;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return empty($this->entries);
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entries);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        return new static(array_map($func, $this->entries));
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static(array_filter($this->entries, $p));
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p)
    {
        foreach ($this->entries as $key => $element)
        {
            if (!$p($key, $element))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        $matches = $noMatches = array();

        foreach ($this->entries as $key => $element)
        {
            if ($p($key, $element))
            {
                $matches[$key] = $element;
            }
            else
            {
                $noMatches[$key] = $element;
            }
        }

        return array(
            new static($matches),
            new static($noMatches)
        );
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->entries = array();
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        return array_slice($this->entries, $offset, $length, true);
    }
}