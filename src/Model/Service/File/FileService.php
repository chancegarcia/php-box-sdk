<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 10/9/15
 * Time: 5:32 PM
 *
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

namespace Box\Model\Service\File;

use Box\Model\File\File;
use Box\Model\File\FileInterface;
use Box\Model\Item\SharedLink\SharedLinkInterface;
use Box\Model\Service\Service;

class FileService extends Service implements FileServiceInterface
{
    protected $sharedLink;
    protected $access;

    public function createSharedLink(FileInterface $file = null, SharedLinkInterface $sharedLink = null)
    {
        $uri = $file::URI . "/" . $file->getId();

        $params = array(
            'shared_link' => $sharedLink->toBoxArray()
        );

        $updatedFile = $this->createNewFile();

        return $this->sendUpdateToBox($uri, $params, 'decoded', $updatedFile);
    }

    /**
     * @return FileInterface
     */
    public function createNewFile()
    {
        $file = new File();

        return $file;
    }
}