<?php
/**
 * @package        Box
 * @subpackage     Box_Model
 * @author         Chance Garcia
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

namespace Box\Model;

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Psr\Log\LoggerInterface;

class Model extends BaseModel implements ModelInterface
{

    // @todo add curl history on info/error/errno properties for child classes to access for recording
    // @todo add last curl info/error/errno properties as well

    public function __construct(?array $options = null)
    {

        if (null !== $options)
        {
            $this->mapBoxToClass($options);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function classArray(): array
    {
        $aModel = get_object_vars($this);
        $aArray = array();

        foreach ($aModel as $k => $v)
        {
            $sKey = $this->toBoxVar($k);
            $aArray[ $sKey ] = $v;
        }

        return $aArray;
    }

    /**
     * {@inheritdoc}
     */
    public function toBoxArray(): array
    {
        $arr = $this->classArray();

        return $this->removeEmpty($arr);
    }

    /**
     * used to throw exceptions that need to contain error information returned from Box
     *
     * @param array $data containing error and error_description keys
     * @param string|null $message
     * @param BoxResponseInterface|null $boxResponse
     *
     * @throws \Box\Exception\BoxException
     */
    public function error(array $data, ?string $message = null, ?BoxResponseInterface $boxResponse = null)
    {
        $error = $data['error'];
        if (null === $message)
        {
            $message = $error;
        }

        $exception = new BoxException($message);
        $exception->setError($error);
        $exception->setErrorDescription($data['error_description']);

        $context = [];
        if ($boxResponse instanceof BoxResponseInterface) {
            $exception->setBoxResponse($boxResponse);
            $context = [$boxResponse->getContent(), $boxResponse->getStatusCode()];
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $loggerMessage = $error . "\n" . $exception->getTraceAsString() . "\n";

            $this->getLogger()->error($loggerMessage, $context);
        }

        throw $exception;
    }

    public function debug(string $message, array $context = []): void
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug($message, $context);
        }
    }

    /**
     * @param string $class
     * @param string $classType
     *
     * @throws \Box\Exception\BoxException
     * @return bool returns true if validation passes. Throws exception if unable to validate or validation doesn't pass
     */
    public function validateClass(string $class, string $classType): bool
    {
        if (!class_exists($class))
        {
            throw new BoxException("Unable to find class", BoxException::UNKNOWN_CLASS);
        }

        $oClass = new $class();

        if ($this->logger && method_exists($oClass, 'setLogger')) {
            $oClass->setLogger($this->logger);
        }

        if (!$oClass instanceof $classType)
        {
            throw new BoxException("Invalid Connection Class", BoxException::INVALID_CLASS_TYPE);
        }

        return true;
    }

    /**
     * @param array $params
     * @param string $numericPrefix
     *
     * @return string
     */
    public function buildQuery(array $params, string $numericPrefix = ''): string
    {
        return http_build_query($params, $numericPrefix, '&', PHP_QUERY_RFC3986);
    }

    public function getNewClass(?string $className = null, mixed $classConstructorOptions = null): mixed
    {
        if (null === $className)
        {
            throw new BoxException('undefined class name', BoxException::INVALID_INPUT);
        }

        $sMethod = 'get' . ucfirst($className) . 'Class';

        $sClass = $this->$sMethod();

        $instance = new $sClass($classConstructorOptions);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }
}
