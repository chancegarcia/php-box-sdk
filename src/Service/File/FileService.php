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

namespace Box\Service\File;

use Box\Dto\File\Request\CreateSharedLinkRequest;
use Box\File\File;
use Box\File\FileInterface;
use Box\Item\SharedLink\SharedLinkInterface;
use Box\Service\Service;

class FileService extends Service implements FileServiceInterface
{
    protected $sharedLink;
    protected $access;

    /**
     * {@inheritdoc}
     * @param FileInterface|null $file
     * @param SharedLinkInterface|CreateSharedLinkRequest|array|null $sharedLink
     */
    public function createSharedLink(?FileInterface $file = null, SharedLinkInterface|CreateSharedLinkRequest|array|null $sharedLink = null): FileInterface
    {
        $uri = $file::URI . "/" . $file->getId();

        if (is_array($sharedLink)) {
            // Normalize array to DTO
            $sharedLink = $this->hydrate(CreateSharedLinkRequest::class, $sharedLink);
        }

        $params = [
            'shared_link' => $this->normalizeSharedLinkPayload($sharedLink),
        ];

        return $this->sendUpdateAndHydrate($uri, $params, File::class);
    }

    /**
     * @param SharedLinkInterface|CreateSharedLinkRequest|null $sharedLink
     * @return array
     */
    private function normalizeSharedLinkPayload(SharedLinkInterface|CreateSharedLinkRequest|null $sharedLink): array
    {
        if (null === $sharedLink) {
            return [];
        }

        if ($sharedLink instanceof CreateSharedLinkRequest) {
            return $sharedLink->toArray();
        }

        // Fallback for legacy models that implement SharedLinkInterface but might not be fully hydrated to DTOs yet
        if (method_exists($sharedLink, 'toArray')) {
            return $sharedLink->toArray();
        }


        return [];
    }

    /**
     * @return FileInterface
     */
    public function createNewFile(): FileInterface
    {
        $file = new File();

        return $file;
    }
}
