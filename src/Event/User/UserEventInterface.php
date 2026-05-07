<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/17/15
 * Time: 5:30 PM
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

namespace Box\Event\User;

use Box\Event\EventInterface;

interface UserEventInterface extends EventInterface
{
    public const URI = "https://api.box.com/2.0/events";

    /**
     * A folder or File was created
     */
    public const ITEM_CREATE = "ITEM_CREATE";
    /**
     * A folder or File was uploaded
     */
    public const ITEM_UPLOAD = "ITEM_UPLOAD";
    /**
     * A comment was created on a folder, file, or other comment
     */
    public const COMMENT_CREATE = "COMMENT_CREATE";
    /**
     * A file or folder was downloaded
     */
    public const ITEM_DOWNLOAD = "ITEM_DOWNLOAD";
    /**
     * A file was previewed
     */
    public const ITEM_PREVIEW = "ITEM_PREVIEW";
    /**
     * A file or folder was moved
     */
    public const ITEM_MOVE = "ITEM_MOVE";
    /**
     * A file or folder was copied
     */
    public const ITEM_COPY = "ITEM_COPY";
    /**
     * A task was assigned
     */
    public const TASK_ASSIGNMENT_CREATE = "TASK_ASSIGNMENT_CREATE";
    /**
     * A file was locked
     */
    public const LOCK_CREATE = "LOCK_CREATE";
    /**
     * A file was unlocked
     */
    public const LOCK_DESTROY = "LOCK_DESTROY";
    /**
     * A file or folder was marked as deleted
     */
    public const ITEM_TRASH = "ITEM_TRASH";
    /**
     * A file or folder was recovered out of the trash
     */
    public const ITEM_UNDELETE_VIA_TRASH = "ITEM_UNDELETE_VIA_TRASH";
    /**
     * A collaborator was added to a folder
     */
    public const COLLAB_ADD_COLLABORATOR = "COLLAB_ADD_COLLABORATOR";
    /**
     * A collaborator had their role changed
     */
    public const COLLAB_ROLE_CHANGE = "COLLAB_ROLE_CHANGE";
    /**
     * A collaborator was invited on a folder
     */
    public const COLLAB_INVITE_COLLABORATOR = "COLLAB_INVITE_COLLABORATOR";
    /**
     * A collaborator was removed from a folder
     */
    public const COLLAB_REMOVE_COLLABORATOR = "COLLAB_REMOVE_COLLABORATOR";
    /**
     * A folder was marked for sync
     */
    public const ITEM_SYNC = "ITEM_SYNC";
    /**
     * A folder was un-marked for sync
     */
    public const ITEM_UNSYNC = "ITEM_UNSYNC";
    /**
     * A file or folder was renamed
     */
    public const ITEM_RENAME = "ITEM_RENAME";
    /**
     * A file or folder was enabled for sharing
     */
    public const ITEM_SHARED_CREATE = "ITEM_SHARED_CREATE";
    /**
     * A file or folder was disabled for sharing
     */
    public const ITEM_SHARED_UNSHARE = "ITEM_SHARED_UNSHARE";
    /**
     * A folder was shared
     */
    public const ITEM_SHARED = "ITEM_SHARED";
    /**
     * A Tag was added to a file or folder
     */
    public const TAG_ITEM_CREATE = "TAG_ITEM_CREATE";
    /**
     * A user is logging in from a device we haven’t seen before
     */
    public const ADD_LOGIN_ACTIVITY_DEVICE = "ADD_LOGIN_ACTIVITY_DEVICE";
    /**
     * We invalidated a user session associated with an app
     */
    public const REMOVE_LOGIN_ACTIVITY_DEVICE = "REMOVE_LOGIN_ACTIVITY_DEVICE";
    /**
     * When an admin role changes for a user
     */
    public const CHANGE_ADMIN_ROLE = "CHANGE_ADMIN_ROLE";
}
