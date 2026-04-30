<?php
/**
 * @package     Box
 * @subpackage  Box_Exception
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

namespace Box\Exception;

use Box\Http\Response\BoxResponseInterface;

class BoxException extends \Exception
{

    const INVALID_CLASS_TYPE = "Invalid Class Type";
    const UNKNOWN_CLASS = "Unknown Class";
    const INVALID_CLASS = "Invalid Class";
    const INVALID_INPUT = "Invalid Input";
    const MISSING_ID = "Missing Id";
    const BOX_API_ERROR = "Box API Error";

    protected mixed $error = null;
    protected mixed $errorDescription = null;
    protected array $context = [];
    protected mixed $boxCode = null;
    protected mixed $status = null;

    /**
     * @param string $message
     * @param mixed|null $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", mixed $code = 0, ?\Throwable $previous = null)
    {
        if (is_int($code)) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, 0, $previous);
            $this->boxCode = $code;
        }
    }

    /**
     * @var null|BoxResponseInterface
     */
    protected ?BoxResponseInterface $boxResponse = null;

    /**
     * @return void
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setError(mixed $error = null): void {
        $this->error = $error;

    }

    public function getError(): mixed {
        return $this->error;
    }

    /**
     * @return void
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setErrorDescription(mixed $errorDescription = null): void {
        $this->errorDescription = $errorDescription;

    }

    public function getErrorDescription(): mixed {
        return $this->errorDescription;
    }

    public function addContext(mixed $contextInformation = null, ?string $key = null): void {
        if (is_string($key)) {
            $finalKey = $key;
            // if we have duplicate key for some reason, make it unique
            if (array_key_exists($key, $this->context)) {
                do {
                    $finalKey = uniqid($key."_", true);
                } while (array_key_exists($finalKey, $this->context));
            }

            $this->context[$finalKey] = $contextInformation;
        } else {
            $this->context[] = $contextInformation;
        }
    }

    public function getContext(?string $key = null): mixed {
        // make sure we have a key value and avoid false negative; allow null to returned on non-existent key
        if (!is_null($key)) {
            if (array_key_exists($key, $this->context)) {
                return $this->context[$key];
            }

            return null;
        }

        // if provided a null key, we return full context
        return $this->context;
    }

    /**
     * @return mixed
     */
    public function getBoxCode(): mixed {
        return $this->boxCode;
    }

    /**
     * @param mixed $boxCode
     * @return self
     */
    /**
     * @return void
     * @deprecated since 0.11.0, use non-fluent setter instead.
     */
    public function setBoxCode(mixed $boxCode = null): void {
        $this->boxCode = $boxCode;

    }

    /**
     * @return BoxResponseInterface|null
     */
    public function getBoxResponse(): ?BoxResponseInterface
    {
        return $this->boxResponse;
    }

    /**
     * @param BoxResponseInterface $boxResponse
     */
    public function setBoxResponse(BoxResponseInterface $boxResponse): void
    {
        $this->boxResponse = $boxResponse;
    }

    public function setStatus(mixed $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }
}
