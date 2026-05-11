<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 10/9/15
 * Time: 5:32 PM
 * @package     Box
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

namespace Box\Service\File;

use Box\Dto\File\Request\CreateSharedLinkRequest;
use Box\Http\FileStream;
use Box\Resource\File;
use Box\Resource\SharedLink;
use Box\Service\AuthenticatedServiceInterface;

interface FileServiceInterface extends AuthenticatedServiceInterface
{
    /**
     * @param File $file
     * @param SharedLink|CreateSharedLinkRequest|array|null $sharedLink shared link object used to set box permissions
     *
     * @return File
     */
    public function createSharedLink(File $file, SharedLink|CreateSharedLinkRequest|array|null $sharedLink = null): File;

    /**
     * @param string|FileStream $file
     * @param string|int $parentId
     * @return array
     */
    public function uploadFile(string|FileStream $file, string|int $parentId): array;

    /**
     * @return File
     */
    public function createNewFile(): File;
}
