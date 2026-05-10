<?php

/**
 * @package Box
 * @subpackage Box_Model
 * @author Chance Garcia
 * @copyright (C)Copyright 2013 Chance Garcia, chancegarcia . com
 *
 * The MIT License (MIT)
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
 * copies or substantial portions of the Software .
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT . IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE .
 *
 */

namespace Box\Model;

use Box\Mapper\ModelMapper;

class Model extends BaseModel implements ModelInterface
{
    /**
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        if (null !== $options) {
            $this->mapBoxToClass($options);
        }
    }

    public function toArray(): array
    {
        return $this->classArray();
    }

    public function classArray(): array
    {
        $aModel = get_object_vars($this);
        $aArray = [];

        foreach ($aModel as $k => $v) {
            $sKey = $this->toBoxVar($k);
            $aArray[ $sKey ] = $v;
        }

        return $aArray;
    }

    public function toBoxArray(): array
    {
        $arr = $this->classArray();

        return ModelMapper::removeEmpty($arr, true);
    }

    public function buildQuery(array $params, string $numericPrefix = ''): string
    {
        return http_build_query($params, $numericPrefix, '&', PHP_QUERY_RFC3986);
    }
}
