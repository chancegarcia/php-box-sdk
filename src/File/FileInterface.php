<?php

/**
 * @package     Box
 * @subpackage  Box_File
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

/**
 * @todo flush out this interface more. not needed for current project but starting stub for client injection
 */

namespace Box\File;


use Box\Item\SharedLink\SharedLinkInterface;
use Box\Model\ModelInterface;

interface FileInterface extends ModelInterface
{
    const URI = "https://api.box.com/2.0/files";
    const UPLOAD_URI = "https://upload.box.com/api/2.0/files/content";

    public function getId(): string|int|null;

    /**
     * @return \Box\Item\SharedLink\SharedLink|array|null
     */
    public function getSharedLink(): mixed;

    public function setSharedLink(mixed $sharedLink = null): void;

    /**
     * @return string
     */
    public function getExtension(): string;
}
