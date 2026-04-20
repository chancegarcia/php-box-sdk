<?php
/**
 * Created by PhpStorm.
 * User: chance
 * Date: 10/9/15
 * Time: 5:47 PM
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

namespace Box\Model\Item\SharedLink;

use Box\Model\ModelInterface;
use Box\Model\Item\SharedLink\Permissions\PermissionsInterface;

interface SharedLinkInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getEffectiveAccess();

    /**
     * @return mixed
     */
    public function getAccess();

    /**
     * @param mixed $access
     *
     * @return SharedLinkInterface
     */
    public function setAccess($access = null);

    /**
     * @return mixed
     */
    public function getUnsharedAt();

    /**
     * @param mixed $unsharedAt
     *
     * @return SharedLinkInterface
     */
    public function setUnsharedAt($unsharedAt = null);

    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @param mixed $password
     *
     * @return SharedLinkInterface
     */
    public function setPassword($password = null);

    /**
     * @return null|PermissionsInterface
     */
    public function getPermissions();

    /**
     * @param PermissionsInterface $permissions
     *
     * @return SharedLinkInterface
     */
    public function setPermissions(PermissionsInterface $permissions = null);
}