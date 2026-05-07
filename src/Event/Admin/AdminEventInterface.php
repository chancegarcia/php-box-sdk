<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/17/15
 * Time: 5:31 PM
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

namespace Box\Event\Admin;

use Box\Event\EventInterface;

/**
 * Interface AdminEventInterface
 *
 * defined only for the admin_logs stream_type
 *
 * @package Box\Event\Admin
 */
interface AdminEventInterface extends EventInterface
{
    public const URI = "https://api.box.com/2.0/events?stream_type=admin_logs";
    public const STREAM_TYPE = "admin_logs";

    /**
     * Added user to group
     */
    public const GROUP_ADD_USER = "GROUP_ADD_USER";
    /**
     * Created user
     */
    public const NEW_USER = "NEW_USER";
    /**
     * Created new group
     */
    public const GROUP_CREATION = "GROUP_CREATION";
    /**
     * Deleted group
     */
    public const GROUP_DELETION = "GROUP_DELETION";
    /**
     * Deleted user
     */
    public const DELETE_USER = "DELETE_USER";
    /**
     * Edited group
     */
    public const GROUP_EDITED = "GROUP_EDITED";
    /**
     * Edited user
     */
    public const EDIT_USER = "EDIT_USER";
    /**
     * Granted folder access
     */
    public const GROUP_ADD_FOLDER = "GROUP_ADD_FOLDER";
    /**
     * Removed from group
     */
    public const GROUP_REMOVE_USER = "GROUP_REMOVE_USER";
    /**
     * Removed folder access
     */
    public const GROUP_REMOVE_FOLDER = "GROUP_REMOVE_FOLDER";
    /**
     * Admin login
     */
    public const ADMIN_LOGIN = "ADMIN_LOGIN";
    /**
     * Added device association
     */
    public const ADD_DEVICE_ASSOCIATION = "ADD_DEVICE_ASSOCIATION";
    /**
     * Failed login
     */
    public const FAILED_LOGIN = "FAILED_LOGIN";
    /**
     * Login
     */
    public const LOGIN = "LOGIN";
    /**
     * OAuth2 token was refreshed for this user
     */
    public const USER_AUTHENTICATE_OAUTH2_TOKEN_REFRESH = "USER_AUTHENTICATE_OAUTH2_TOKEN_REFRESH";
    /**
     * Removed device association
     */
    public const REMOVE_DEVICE_ASSOCIATION = "REMOVE_DEVICE_ASSOCIATION";
    /**
     * Agreed to terms
     */
    public const TERMS_OF_SERVICE_AGREE = "TERMS_OF_SERVICE_AGREE";
    /**
     * Rejected terms
     */
    public const TERMS_OF_SERVICE_REJECT = "TERMS_OF_SERVICE_REJECT";
    /**
     * Copied
     */
    public const COPY = "COPY";
    /**
     * Deleted
     */
    public const DELETE = "DELETE";
    /**
     * Downloaded
     */
    public const DOWNLOAD = "DOWNLOAD";
    /**
     * Edited
     */
    public const EDIT = "EDIT";
    /**
     * Locked
     */
    public const LOCK = "LOCK";
    /**
     * Moved
     */
    public const MOVE = "MOVE";
    /**
     * Previewed
     */
    public const PREVIEW = "PREVIEW";
    /**
     * Renamed
     */
    public const RENAME = "RENAME";
    /**
     * Set file auto-delete
     */
    public const STORAGE_EXPIRATION = "STORAGE_EXPIRATION";
    /**
     * Undeleted
     */
    public const UNDELETE = "UNDELETE";
    /**
     * Unlocked
     */
    public const UNLOCK = "UNLOCK";
    /**
     * Uploaded
     */
    public const UPLOAD = "UPLOAD";
    /**
     * Enabled shared links
     */
    public const SHARE = "SHARE";
    /**
     * Share links settings updated
     */
    public const ITEM_SHARED_UPDATE = "ITEM_SHARED_UPDATE";
    /**
     * Extend shared link expiration
     */
    public const UPDATE_SHARE_EXPIRATION = "UPDATE_SHARE_EXPIRATION";
    /**
     * Set shared link expiration
     */
    public const SHARE_EXPIRATION = "SHARE_EXPIRATION";
    /**
     * Unshared links
     */
    public const UNSHARE = "UNSHARE";
    /**
     * Accepted invites
     */
    public const COLLABORATION_ACCEPT = "COLLABORATION_ACCEPT";
    /**
     * Changed user roles
     */
    public const COLLABORATION_ROLE_CHANGE = "COLLABORATION_ROLE_CHANGE";
    /**
     * Extend collaborator expiration
     */
    public const UPDATE_COLLABORATION_EXPIRATION = "UPDATE_COLLABORATION_EXPIRATION";
    /**
     * Removed collaborators
     */
    public const COLLABORATION_REMOVE = "COLLABORATION_REMOVE";
    /**
     * Invited
     */
    public const COLLABORATION_INVITE = "COLLABORATION_INVITE";
    /**
     * Set collaborator expiration
     */
    public const COLLABORATION_EXPIRATION = "COLLABORATION_EXPIRATION";
    /**
     * Synced folder
     */
    public const ITEM_SYNC = "ITEM_SYNC";
    /**
     * Un-synced folder
     */
    public const ITEM_UNSYNC = "ITEM_UNSYNC";

    /**
     * @return mixed
     */
    public function getStreamType(): mixed;

    /**
     * @return mixed
     */
    public function getLimit(): mixed;

    /**
     * @param mixed $limit
     */
    public function setLimit(mixed $limit = null): void;

    /**
     * @return mixed
     */
    public function getStreamPosition(): mixed;

    /**
     * @param mixed $streamPosition
     */
    public function setStreamPosition(mixed $streamPosition = null): void;

    /**
     * @return mixed
     */
    public function getCreatedAfter(): mixed;

    /**
     * @param mixed $createdAfter
     */
    public function setCreatedAfter(mixed $createdAfter = null): void;

    /**
     * @return mixed
     */
    public function getCreatedBefore(): mixed;

    /**
     * @param mixed $createdBefore
     */
    public function setCreatedBefore(mixed $createdBefore = null): void;
}
