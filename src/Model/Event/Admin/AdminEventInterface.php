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

namespace Box\Model\Event\Admin;

use Box\Model\Event\EventInterface;

/**
 * Interface AdminEventInterface
 *
 * defined only for the admin_logs stream_type
 *
 * @package Box\Model\Event\Admin
 */
interface AdminEventInterface extends EventInterface
{
    const URI = "https://api.box.com/2.0/events?stream_type=admin_logs";
    const STREAM_TYPE = "admin_logs";

    /**
     * Added user to group
     */
    const GROUP_ADD_USER = "GROUP_ADD_USER";
    /**
     * Created user
     */
    const NEW_USER = "NEW_USER";
    /**
     * Created new group
     */
    const GROUP_CREATION = "GROUP_CREATION";
    /**
     * Deleted group
     */
    const GROUP_DELETION = "GROUP_DELETION";
    /**
     * Deleted user
     */
    const DELETE_USER = "DELETE_USER";
    /**
     * Edited group
     */
    const GROUP_EDITED = "GROUP_EDITED";
    /**
     * Edited user
     */
    const EDIT_USER = "EDIT_USER";
    /**
     * Granted folder access
     */
    const GROUP_ADD_FOLDER = "GROUP_ADD_FOLDER";
    /**
     * Removed from group
     */
    const GROUP_REMOVE_USER = "GROUP_REMOVE_USER";
    /**
     * Removed folder access
     */
    const GROUP_REMOVE_FOLDER = "GROUP_REMOVE_FOLDER";
    /**
     * Admin login
     */
    const ADMIN_LOGIN = "ADMIN_LOGIN";
    /**
     * Added device association
     */
    const ADD_DEVICE_ASSOCIATION = "ADD_DEVICE_ASSOCIATION";
    /**
     * Failed login
     */
    const FAILED_LOGIN = "FAILED_LOGIN";
    /**
     * Login
     */
    const LOGIN = "LOGIN";
    /**
     * OAuth2 token was refreshed for this user
     */
    const USER_AUTHENTICATE_OAUTH2_TOKEN_REFRESH = "USER_AUTHENTICATE_OAUTH2_TOKEN_REFRESH";
    /**
     * Removed device association
     */
    const REMOVE_DEVICE_ASSOCIATION = "REMOVE_DEVICE_ASSOCIATION";
    /**
     * Agreed to terms
     */
    const TERMS_OF_SERVICE_AGREE = "TERMS_OF_SERVICE_AGREE";
    /**
     * Rejected terms
     */
    const TERMS_OF_SERVICE_REJECT = "TERMS_OF_SERVICE_REJECT";
    /**
     * Copied
     */
    const COPY = "COPY";
    /**
     * Deleted
     */
    const DELETE = "DELETE";
    /**
     * Downloaded
     */
    const DOWNLOAD = "DOWNLOAD";
    /**
     * Edited
     */
    const EDIT = "EDIT";
    /**
     * Locked
     */
    const LOCK = "LOCK";
    /**
     * Moved
     */
    const MOVE = "MOVE";
    /**
     * Previewed
     */
    const PREVIEW = "PREVIEW";
    /**
     * Renamed
     */
    const RENAME = "RENAME";
    /**
     * Set file auto-delete
     */
    const STORAGE_EXPIRATION = "STORAGE_EXPIRATION";
    /**
     * Undeleted
     */
    const UNDELETE = "UNDELETE";
    /**
     * Unlocked
     */
    const UNLOCK = "UNLOCK";
    /**
     * Uploaded
     */
    const UPLOAD = "UPLOAD";
    /**
     * Enabled shared links
     */
    const SHARE = "SHARE";
    /**
     * Share links settings updated
     */
    const ITEM_SHARED_UPDATE = "ITEM_SHARED_UPDATE";
    /**
     * Extend shared link expiration
     */
    const UPDATE_SHARE_EXPIRATION = "UPDATE_SHARE_EXPIRATION";
    /**
     * Set shared link expiration
     */
    const SHARE_EXPIRATION = "SHARE_EXPIRATION";
    /**
     * Unshared links
     */
    const UNSHARE = "UNSHARE";
    /**
     * Accepted invites
     */
    const COLLABORATION_ACCEPT = "COLLABORATION_ACCEPT";
    /**
     * Changed user roles
     */
    const COLLABORATION_ROLE_CHANGE = "COLLABORATION_ROLE_CHANGE";
    /**
     * Extend collaborator expiration
     */
    const UPDATE_COLLABORATION_EXPIRATION = "UPDATE_COLLABORATION_EXPIRATION";
    /**
     * Removed collaborators
     */
    const COLLABORATION_REMOVE = "COLLABORATION_REMOVE";
    /**
     * Invited
     */
    const COLLABORATION_INVITE = "COLLABORATION_INVITE";
    /**
     * Set collaborator expiration
     */
    const COLLABORATION_EXPIRATION = "COLLABORATION_EXPIRATION";
    /**
     * Synced folder
     */
    const ITEM_SYNC = "ITEM_SYNC";
    /**
     * Un-synced folder
     */
    const ITEM_UNSYNC = "ITEM_UNSYNC";

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