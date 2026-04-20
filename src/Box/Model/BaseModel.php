<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 5:23 PM
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

namespace Box\Model;

use Box\Logger\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class BaseModel implements BaseModelInterface, LoggerAwareInterface
{
    protected ?LoggerInterface $logger = null;

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }

    public function toClassVar(string $str): string
    {
        $aTokens = explode("_", $str);
        $sFirst = array_shift($aTokens);
        $aTokens = array_map('ucfirst', $aTokens);
        array_unshift($aTokens, $sFirst);

        return implode("", $aTokens);
    }

    public function toBoxVar(string $str): string
    {
        $aTokens = preg_split('/(?<=\\w)(?=[A-Z])/', $str);
        $sFirst = array_shift($aTokens);
        $aTokens = array_map('lcfirst', $aTokens);
        array_unshift($aTokens, $sFirst);

        return implode("_", $aTokens);
    }

    /**
     * @param array|\stdClass $aData
     *
     * @return $this
     */
    public function mapBoxToClass(array|\stdClass $aData): self
    {
        if ($this->getLogger() instanceof LoggerInterface)
        {
            $this->getLogger()->debug('map data: ' . var_export($aData, true), array(__METHOD__ . ":" . __LINE__));
        }
        // check if value is object or array and map
        // or maybe have a map array of properties/keys that call new classes to map to
        foreach ($aData as $k => $v)
        {
            $sClassProp = $this->toClassVar($k);
            $sSetterMethod = "set" . ucfirst($sClassProp);
            if (method_exists($this, $sSetterMethod))
            {
                $this->{$sSetterMethod}($v);
            }
            elseif (property_exists($this, $sClassProp))
            {
                $this->{$sClassProp} = $v;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInt(mixed $number = null): bool
    {
        if (!is_numeric($number))
        {
            return false;
        }

        if (is_string($number) && str_contains($number, "."))
        {
            return false;
        }

        if (!is_int($number) && !is_string($number))
        {
            return false;
        }

        if (is_string($number) && !is_int((int)$number))
        {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeEmpty(array $haystack = []): array
    {
        foreach ($haystack as $k => $v)
        {
            if (is_array($v))
            {
                $haystack[$k] = $this->removeEmpty($v);
            }

            if (is_string($v))
            {
                $v = trim($v);
            }

            if (empty($v))
            {
                unset($haystack[$k]);
            }
        }

        return $haystack;
    }
}