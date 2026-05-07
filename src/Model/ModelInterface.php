<?php

/**
 * Created by PhpStorm . 
 * User: chance
 * Date: 9/17/15
 * Time: 5:36 PM
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

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;

interface ModelInterface extends BaseModelInterface
{
 public function __construct(?array $options = null);

 /**
 * class properties as an array
 *
 * @return array
 */
 public function classArray(): array;

 /**
 * same as class array except empty elements are filtered out
 * @return array
 */
 public function toBoxArray(): array;

 /**
 * used to throw exceptions that need to contain error information returned from Box
 *
 * @param array $data containing error and error_description keys
 * @param string|null $message
 * @param BoxResponseInterface|null $boxResponse
 *
 * @throws BoxException
 */
 public function error(array $data, ?string $message = null, ?BoxResponseInterface $boxResponse = null): void;

 /**
 * @param string $class
 * @param string $classType
 *
 * @throws BoxException
 * @return bool returns true if validation passes . Throws exception if unable to validate or validation doesn't pass
 */
 public function validateClass(string $class, string $classType): bool;

 /**
 * @param array $params
 * @param string $numericPrefix
 * @return string
 */
 public function buildQuery(array $params, string $numericPrefix = ''): string;

 /**
 * @param string|null $className
 * @param mixed $classConstructorOptions
 * @return mixed
 */
 public function getNewClass(?string $className = null, mixed $classConstructorOptions = null): mixed;
}
