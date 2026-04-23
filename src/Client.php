<?php
/**
 * @package     Box
 * @subpackage  Box_Client
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

namespace Box;

use Box\Exception\BoxException;
use Box\Model\Collaboration\Collaboration;
use Box\Model\Collaboration\CollaborationInterface;
use Box\Model\Connection\Connection;
use Box\Model\Connection\ConnectionInterface;
use Box\Model\Connection\Token\Token;
use Box\Model\Connection\Token\TokenInterface;
use Box\Model\File\File;
use Box\Model\File\FileInterface;
use Box\Model\Folder\Folder;
use Box\Model\Folder\FolderInterface;
use Box\Model\Group\GroupInterface;
use Box\Model\Model;
use Box\Model\ModelInterface;
use Box\Model\User\UserInterface;
use JsonException;

/**
 * Class Client
 * @package Box\Model
 */
class Client extends Model
{
    CONST AUTH_URI = "https://account.box.com/api/oauth2/authorize";
    CONST TOKEN_URI = "https://www.box.com/api/oauth2/token";
    CONST REVOKE_URI = "https://www.box.com/api/oauth2/revoke";
    CONST SEARCH_URI = "https://api.box.com/2.0/search";

    protected mixed $state = null;

    /**
     * @var Connection|ConnectionInterface|null
     */
    protected ?ConnectionInterface $connection = null;
    /**
     * @var array of folder items indexed by the folder ID
     * @internal should just be an array of any folder known/retrieved by the client. does not need to be recursive
     *     since folders know their parents and items
     */
    protected mixed $folders = null;
    protected mixed $files = null;
    /**
     * @var array of collaborations
     */
    protected mixed $collaborations = null;

    /**
     * @var Folder|null
     */
    protected ?FolderInterface $root = null;

    /**
     * @var Token|TokenInterface|null
     */
    protected ?TokenInterface $token = null;

    protected mixed $authorizationCode = null;
    protected mixed $clientId = null;
    protected mixed $clientSecret = null;
    protected mixed $redirectUri = null;

    protected mixed $deviceId = null;
    protected mixed $deviceName = null;


    /**
     * allow for class injection by using an interface for these classes
     */
    protected string $folderClass = 'Box\Model\Folder\Folder';
    protected string $fileClass = 'Box\Model\File\File';
    protected string $connectionClass = 'Box\Model\Connection\Connection';
    protected string $tokenClass = 'Box\Model\Connection\Token\Token';
    protected string $collaborationClass = 'Box\Model\Collaboration\Collaboration';
    protected string $userClass = 'Box\Model\User\User';
    protected string $groupClass = 'Box\Model\Group\Group';


    /**
     * @param mixed $options
     *
     * @return Folder|FolderInterface
     */
    public function getNewFolder($options = null)
    {
        return $this->getNewClass('Folder', $options);
    }

    /**
     * @param mixed $options
     *
     * @return \Box\Model\User\User|\Box\Model\User\UserInterface
     */
    public function getNewUser($options = null)
    {
        return $this->getNewClass('User', $options);
    }

    /**
     * @param mixed $options
     *
     * @return \Box\Model\Group\Group|\Box\Model\Group\GroupInterface
     */
    public function getNewGroup($options = null)
    {
        return $this->getNewClass('Group', $options);
    }

    /**
     * @param mixed $options
     *
     * @return \Box\Model\Collaboration\Collaboration|\Box\Model\Collaboration\CollaborationInterface
     */
    public function getNewCollaboration($options = null)
    {
        return $this->getNewClass('Collaboration', $options);
    }

    /**
     * @param int $id use 0 for returning all folders
     * @param bool $retrieve if no folder is found, attempt to retrieve from box
     *
     * @return array|null|Folder returns null if no such folder exists and retrieve is false
     */
    public function getFolder($id = 0, $retrieve = true)
    {
        $folders = $this->getFolders($retrieve);

        if (0 == $id)
        {
            return $folders;
        }

        if (!array_key_exists($id, $folders))
        {
            if (!$retrieve)
            {
                return null;
            }
            $folder = $this->getFolderFromBox($id);
            $this->addFolder($folder);
        }


        $folder = $folders[ $id ];

        return $folder;

    }

    public function addFolder($folder)
    {
        $folders = $this->getFolders();
        $folders[] = $folder;
        $this->setFolders($folders);

    }

    public function getFolders($retrieve = true)
    {
        if (!$retrieve)
        {
            return $this->folders;
        }

        $root = $this->getRoot();
        if (null === $root)
        {
            $root = $this->getFolderFromBox();
            $this->setRoot($root);
        }

        // not sure if I should add recursive parsing of folder/items. stubbing out for now.
        return null;

    }

    /**
     * get membership list of a given group. if limit or offset is numeric, only retrieve specific list page;
     *
     * @param null $group
     * @param null $limit leave null to get all; if limit is null but offset is numeric, limit will default to 100
     * @param null $offset leave null to get all; if limit is null but offset is numeric, limit will default to 100
     *
     * @return array returns an array of User objects that are in the group membership
     * @return array returns an array of User objects that are in the group membership
     * @throws \Box\Exception\BoxException
     */
    public function getGroupMembershipList($group = null, $limit = null, $offset = null)
    {
        if (is_numeric($group) && is_int($group))
        {
            $groupId = $group;
            $group = $this->getNewGroup();
            $group->setId($groupId);
        }

        if (!$group instanceof GroupInterface)
        {
            throw new BoxException("Group object expected", BoxException::INVALID_INPUT);
        }

        $members = array();
        $entries = array();

        if (is_numeric($limit) || is_numeric($offset))
        {
            if (!is_numeric($limit))
            {
                $limit = 100;
            }

            $uri = $group->getMembershipListUri($limit, $offset);

            $data = $this->query($uri);

            $entries = $data['entries'];
        }
        else
        {
            $limit = 100;
            $offset = 0;

            $uri = $group->getMembershipListUri($limit, $offset);

            $data = $this->query($uri);

            $totalMembers = $data['total_count'];

            $entries = $data['entries'];

            $currentTotal = count($entries);

            while ($currentTotal < $totalMembers)
            {
                if (0 != $offset)
                {
                    $nextPage = $group->getMembershipListUri($limit, $offset);
                    $data = $this->query($nextPage);
                    $moreEntries = $data['entries'];
                    $entries = array_merge($entries, $moreEntries);

                    $currentTotal = count($entries);
                }

                $offset += $limit;
            }
        }

        foreach ($entries as $entry)
        {
            $userData = $entry['user'];
            $user = $this->getNewUser();
            $user->mapBoxToClass($userData);
            $members[] = $user;
        }

        return $members;
    }

    /**
     * @throws BoxException
     */
    public function getFolderBySharedUri($sharedUri = null)
    {
        if (!is_string($sharedUri))
        {
            throw new BoxException('shared uri must be a string value', BoxException::INVALID_INPUT);
        }

        $uri = Folder::SHARED_ITEM_URI;
        $sSharedLinkHeader = "BoxApi: shared_link=" . $sharedUri;
        $aSharedLinkHeader = array($sSharedLinkHeader);

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection, $aSharedLinkHeader);

        $response = $connection->query($uri);

        $data = $response->getContent();

        $jsonData = json_decode($data, true);
        /**
         * API docs says error is thrown if folder does not exist or no access.
         * no example of error to parse by. Have to assume success until can modify
         */

        /**
         * error decoding json data
         */
        if (null === $jsonData)
        {
            $data['error'] = "unable to decode json data";
            $data['error_description'] = 'try refreshing the token';
            $this->error($data, null, $response);
        }

        if (is_array($jsonData) && array_key_exists('type', $jsonData) && 'folder' === $jsonData['type'])
        {
            $folder = $this->getNewFolder();
            $folder->mapBoxToClass($jsonData);
        }
        else
        {
            if (is_array($jsonData) && array_key_exists('type', $jsonData) && 'error' === $jsonData['type'])
            {
                $errorData['error'] = $jsonData['message'];
                $errorData['error_description'] = $jsonData;
                $this->error($errorData, null, $response);
            }
            else
            {
                $folder = false;
            }
        }

        return $folder;
    }

    public function getFolderFromBox($id = 0): FolderInterface|Folder
    {
        $uri = Folder::URI . '/' . $id; // all class constant URIs do not end in a slash

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->query($uri);

        $data = $response->getContent();

        $jsonData = json_decode($data, true);
        /**
         * API docs says error is thrown if folder does not exist or no access.
         * no example of error to parse by. Have to assume success until can modify
         */

        /**
         * error decoding json data
         */
        if (null === $jsonData)
        {
            $data['error'] = "unable to decode json data";
            $data['error_description'] = 'try refreshing the token';
            $this->error($data, null, $response);
        }

        $folder = $this->getNewFolder();
        $folder->mapBoxToClass($jsonData);

        return $folder;
    }

    /**
     * @param \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface $folder
     * @param int $limit
     * @param int $offset
     *
     * @return \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface
     */
    public function getBoxFolderItems($folder, $limit = 100, $offset = 0)
    {
        $uri = $folder->getBoxFolderItemsUri($limit, $offset);
        $data = $this->query($uri);

        $folder->setItemCollection($data);

        return $folder;
    }

    public function getFolderItems($id = 0)
    {
        /**
         * @var Folder|FolderInterface $folder
         */
        $folder = $this->getFolder($id);

        return $folder->getItems();
    }

    /**
     * @param     $name
     * @param int $parentFolderId
     *
     * @return Folder|FolderInterface
     * @throws BoxException
     */
    public function createNewBoxFolder($name, $parentFolderId = 0)
    {
        $uri = Folder::URI;

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $params = array(
            'name' => $name,
            'parent' => array('id' => $parentFolderId)
        );

        $response = $connection->post($uri, $params, true);

        $data = $response->getContent();

        $jsonData = json_decode($data, true);

        /**
         * error decoding json data
         */
        if (null === $jsonData)
        {
            $data = array();
            $data['error'] = "unable to decode json data";
            $data['error_description'] = 'try refreshing the token';
            $this->error($data, null, $response);
        }
        else
        {
            if (is_array($jsonData) && array_key_exists('type', $jsonData) && 'error' === $jsonData['type'])
            {
                $data = array();
                $data['error'] = $jsonData['status'] . "  - " . $jsonData['code'];
                $data['error_description'] = var_export($jsonData['context_info'], true);
                $this->error($data, null, $response);
            }
        }

        $folder = $this->getNewFolder();
        $folder->mapBoxToClass($jsonData);

        return $folder;
    }

    /**
     * @param Folder|FolderInterface $folder
     * @param bool $ifMatchHeader
     *
     * @throws \Exception
     * @return mixed
     */
    public function updateBoxFolder($folder, $ifMatchHeader = false)
    {
        $uri = Folder::URI . '/' . $folder->getId();

        // can't just do classArray(), only certain request attributes can be sent so have to send specialized param array.
        // @todo implement this to work. restubbing for now since classArray isn't working
        $params = $folder->classArray();
        throw new \Exception("currently not implemented/working.");

        // @todo implement If-Match header logic

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);
        $response = $connection->put($uri, $params, true);

        $json = $response->getContent();

        $data = json_decode($json, true);

        /**
         * error decoding json data
         */
        if (null === $data)
        {
            $errorData = array();
            $errorData['error'] = "unable to decode json data";
            $errorData['error_description'] = $data;
            $this->error($errorData, null, $response);
        }
        else
        {
            if (is_array($data) && array_key_exists('type', $data) && 'error' === $data['type'])
            {
                $errorData = array();
                $errorData['error'] = $data['status'] . "  - " . $data['code'];
                $errorData['error_description'] = var_export($data['context_info'], true);
                $this->error($errorData, null, $response);
            }
        }

        return $data; // inconsistent? figure out what return is needed, if any
    }

    /**
     * @param null|\Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface $folder
     *
     * @return mixed raw json data as an array
     * @throws BoxException
     */
    public function getFolderCollaborations($folder = null)
    {
        if (!$folder instanceof FolderInterface)
        {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting FolderInterface class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }
        $folderId = $folder->getId();
        $uri = Folder::URI . '/' . $folderId . '/collaborations';

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->query($uri);

        $json = $response->getContent();

        $data = json_decode($json, true);

        // this can be refactored too...from copyBoxFolder
        if (null === $data)
        {
            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data, null, $response);
        }
        else
        {
            if (array_key_exists('error', $data))
            {
                $this->error($data, null, $response);
            }
            else
            {
                if (array_key_exists('type', $data) && 'error' === $data['type'])
                {
                    $data['error'] = "sdk_unknown";
                    $ditto = $data;
                    $data['error_description'] = $ditto;
                    $this->error($data, null, $response);
                }
            }
        }

        return $data;
    }

    /**
     * @param null|\Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface $folder
     * @param null|\Box\Model\User\User|\Box\Model\User\UserInterface|\Box\Model\Group\GroupInterface $collaborator
     * @param string $role see {@link http://developers.box.com/docs/#collaborations box documentation for all possible
     *     roles} default is viewer
     *
     * @return \Box\Model\Collaboration\Collaboration|\Box\Model\Collaboration\CollaborationInterface
     * @throws BoxException
     */
    public function addCollaboration($folder = null, $collaborator = null, $role = 'viewer')
    {
        if (!$folder instanceof FolderInterface)
        {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting FolderInterface class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }

        if (!$collaborator instanceof UserInterface && !$collaborator instanceof GroupInterface)
        {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting UserInterface class. given (" . var_export($collaborator, true) . ")";
            $this->error($err);
        }

        $uri = Collaboration::URI;

        $folderId = $folder->getId();
        $collaboratorId = $collaborator->getId();

        $params = array(
            'item' => array(
                "id" => $folderId,
                "type" => "folder"
            ),
            'accessible_by' => array(
                "id" => $collaboratorId
            ),

            'role' => $role
        );

        // can be refactored a bit more but the json encode works in the connection class
        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->post($uri, $params, true);

        $json = $response->getContent();

        $data = json_decode($json, true);

        if (null === $data)
        {
            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data, null, $response);
        }
        else
        {
            if (array_key_exists('error', $data))
            {
                $this->error($data, null, $response);
            }
            else
            {
                if (array_key_exists('type', $data) && 'error' === $data['type'])
                {
                    $data['error'] = "sdk_unknown";
                    $ditto = $data;
                    $data['error_description'] = $ditto;
                    $this->error($data, null, $response);
                }
            }
        }

        $collaboration = $this->getNewCollaboration();
        $collaboration->mapBoxToClass($data);

        return $collaboration;
    }

    /**
     * @param null|\Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface $folder
     * @param array|null shared link options with
     * default shared link set to collaborator access, no unshared time or permissions set to
     *
     * @return \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface
     * @throws BoxException
     */
    public function createSharedLinkForFolder($folder = null, $params = null)
    {
        if (!$folder instanceof FolderInterface)
        {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting FolderInterface class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }

        $uri = Folder::URI;

        $folderId = $folder->getId();

        $uri .= "/" . $folderId;

        if (!is_array($params))
        {
            $params = array(
                'shared_link' => array(
                    'access' => 'collaborators'
                )
            );
        }

        // can be refactored a bit more but the json encode works in the connection class
        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->put($uri, $params, true);

        $json = $response->getContent();

        $data = json_decode($json, true);

        if (null === $data)
        {
            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data, null, $response);
        }
        else
        {
            if (array_key_exists('error', $data))
            {
                $this->error($data, null, $response);
            }
            else
            {
                if (array_key_exists('type', $data) && 'error' === $data['type'])
                {
                    $data['error'] = "sdk_unknown";
                    $ditto = $data;
                    $data['error_description'] = $ditto;
                    $this->error($data);
                }
            }
        }

        $updatedFolder = $this->getNewFolder();
        $updatedFolder->mapBoxToClass($data);

        return $updatedFolder;
    }

    /**
     * @param Folder $originalFolder
     * @param Folder|array $parent
     * @param string $name
     * @param bool $addToFolders
     *
     * @return \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface
     * @throws Exception*@throws BoxException
     * @throws BoxException
     * @internal param $destinationId
     */
    public function copyBoxFolder($originalFolder, $parent, $name = null, $addToFolders = true)
    {
        if (!$originalFolder instanceof FolderInterface)
        {
            $this->error(array(
                'error' => 'Folder or FolderInterface expected',
                'error_description' => $originalFolder
            ));
        }

        $uri = Folder::URI . '/' . $originalFolder->getId() . '/copy';
        $this->debug("copy uri: " . $uri, [__METHOD__, __LINE__]);
        $this->debug("initial parent: " . var_export($parent, true), [__METHOD__, __LINE__]);

        if (is_array($parent))
        {
            $folder = $this->getNewFolder();
            $folder->mapBoxToClass($parent);
            $parent = $folder;
        }

        if (!$parent instanceof FolderInterface)
        {
            $this->error(array(
                'error' => 'Folder or FolderInterface expected',
                'error_description' => $parent
            ));
        }

        $params['parent'] = array('id' => $parent->getId());
        if (null !== $name)
        {
            $params['name'] = $name;
        }

        $this->debug("params: " . var_export($params, true), [__METHOD__, __LINE__]);

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->post($uri, $params, true);
        $this->debug("response header: " . var_export($response->getResponseHeader(), true), [__METHOD__, __LINE__]);

        $json = $response->getContent();
        $this->debug("response content (json expected): " . var_export($json, true), [__METHOD__, __LINE__]);

        $data = json_decode($json, true);
        $originalDecodeData = $data;
        $this->debug("original decoded data: " . var_export($originalDecodeData, true), [__METHOD__, __LINE__]);

        if (null === $data)
        {

            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data, null, $response);
        }
        else
        {
            if (array_key_exists('error', $data))
            {
                $this->error($data, null, $response);
            }
            else
            {
                if (array_key_exists('type', $data) && 'error' === $data['type'])
                {
                    $data['error'] = "sdk_unknown";
                    $ditto = $data;
                    $data['error_description'] = $ditto;
                    $this->error($data, null, $response);
                }
            }
        }

        $copy = $this->getNewFolder();
        $copy->mapBoxToClass($data);

        if (true === $addToFolders && $copy instanceof Folder)
        {
            $this->addFolder($copy);
        }

        return $copy;
    }

    // @todo make multiple file upload

    /**
     * @throws Exception
     * @throws BoxException
     * @throws JsonException
     */
    public function uploadFileToBox($file)
    {
        $accessToken = $this->getToken()->getAccessToken();
        if (empty($accessToken) || trim($accessToken) === '') {
            throw new BoxException('BOX_ACCESS_TOKEN is required for upload.', BoxException::INVALID_INPUT);
        }

        $uri = File::UPLOAD_URI;

        // loop through the files and add the @ to the filename if not present

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->postFile($uri, $file);

        $uploaded = $response->getContent();

        $data = json_decode($uploaded, true, 512, JSON_THROW_ON_ERROR);

        if (is_array($data) && array_key_exists('type', $data) && 'error' === $data['type'])
        {
            $data['error'] = "sdk_unknown";
            $ditto = $data;
            $data['error_description'] = $ditto;
            $this->error($data);
        }

        return $data;
    }

    public function getAccessToken()
    {
        $connection = $this->getConnection();
        $params['grant_type'] = 'authorization_code';
        $params['code'] = $this->getAuthorizationCode();
        $params['client_id'] = $this->getClientId();
        $params['client_secret'] = $this->getClientSecret();

        $redirectUri = $this->getRedirectUri();

        $response = $connection->post(self::TOKEN_URI, $params);
        $json = $response->getContent();

        $data = json_decode($json, true);

        if (null === $data)
        {
            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data);
        }
        else
        {
            if (array_key_exists('error', $data))
            {
                $this->error($data);
            }
        }

        $token = $this->getToken();
        $this->setTokenData($token, $data);

        return $token;

    }

    /**
     * @return \Box\Model\Connection\Token\Token|\Box\Model\Connection\Token\TokenInterface
     */
    public function refreshToken()
    {
        // outside script will set token via getAccessToken
        $token = $this->getToken();

        $params['refresh_token'] = $token->getRefreshToken();
        $params['client_id'] = $this->getClientId();
        $params['client_secret'] = $this->getClientSecret();
        $params['grant_type'] = 'refresh_token';

        $deviceId = $this->getDeviceId();
        if (null !== $deviceId)
        {
            $params['device_id'] = $deviceId;
        }

        $deviceName = $this->getDeviceName();
        if (null !== $deviceName)
        {
            $params['device_name'] = $deviceName;
        }

        $connection = $this->getConnection();

        $response = $connection->post(self::TOKEN_URI, $params);
        $json = $response->getContent();

        $data = json_decode($json, true);

        if (null === $data)
        {
            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data, null, $response);
        }
        else
        {
            if (array_key_exists('error', $data))
            {
                $this->error($data, null, $response);
            }
        }

        $this->setTokenData($token, $data);

        $this->setToken($token);

        return $token;
    }

    public function getAuthorizationHeader()
    {
        $token = $this->getToken();

        return "Authorization: Bearer " . $token->getAccessToken();
    }

    /**
     * @param $token \Box\Model\Connection\Token\TokenInterface
     * @param $data
     */
    public function setTokenData($token, $data): void
    {
        $token->setAccessToken($data['access_token']);
        $token->setExpiresIn($data['expires_in']);
        $token->setTokenType($data['token_type']);
        $token->setRefreshToken($data['refresh_token']);
    }

    /**
     * @param $token \Box\Model\Connection\Token\TokenInterface|\Box\Model\Connection\Token\Token
     *
     * @return mixed
     */
    public function destroyToken($token)
    {
        $params['client_id'] = $this->getClientId();
        $params['client_secret'] = $this->getClientSecret();
        // The access_token or refresh_token to be destroyed. Only one is required, though both will be destroyed.
        $params['token'] = $token->getAccessToken();

        $connection = $this->getConnection();

        $response = $connection->post(self::REVOKE_URI, $params);
        $json = $response->getContent();
        // @todo add error handling for null data
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    public function auth()
    {
        // build get query to auth uri
        $query = $this->buildAuthQuery();

        // send get query to auth uri (auth uri will redirect to app redirect uri)
        $connection = $this->getConnection();

        // can't get return data b/c of redirect
        $connection->query($query);
    }

    public function buildAuthQuery()
    {
        $uri = self::AUTH_URI . '?';
        $params = array();

        $params['response_type'] = "code";

        $clientId = $this->getClientId();
        $params['client_id'] = $clientId;

        $state = $this->getState();
        if (null !== $state)
        {
            $params['state'] = $state;
        }

        $query = $this->buildQuery($params); // buildQuery does urlencode
        $uri .= $query;

        $redirectUri = $this->getRedirectUri();

        if (null !== $redirectUri)
        {
            $redirectUri = urlencode($redirectUri);
            $uri .= "&redirect_uri=" . $redirectUri;
        }

        return $uri;
    }

    /**
     * @param      $connection Connection
     * @param null|array $additionalHeaders
     *
     * @throws BoxException
     */
    public function setConnectionAuthHeader($connection, $additionalHeaders = null): void
    {
        $authorizationHeader = $this->getAuthorizationHeader();

        if (str_ends_with($authorizationHeader, ' ')) {
             throw new BoxException('BOX_ACCESS_TOKEN is required for upload.', BoxException::INVALID_INPUT);
        }

        $headers = array($authorizationHeader);

        if (null !== $additionalHeaders && !is_array($additionalHeaders))
        {
            throw new BoxException('additional headers must be in array format', BoxException::INVALID_INPUT);
        }

        if (is_array($additionalHeaders))
        {
            $headers = array_merge($headers, $additionalHeaders);
        }

        // header opt will require a merge with other headers to not overwrite.
        // @todo refactor to allow additional headers with auth header
        $connection->setCurlOpts(array('CURLOPT_HTTPHEADER' => $headers));
    }

    /**
     * @param mixed $clientId
     * @return void
     */
    public function setClientId($clientId = null): void
    {
        $this->clientId = $clientId;

    }

    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientSecret
     * @return void
     */
    public function setClientSecret($clientSecret = null): void
    {
        $this->clientSecret = $clientSecret;

    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed $redirectUri
     * @return void
     */
    public function setRedirectUri($redirectUri = null): void
    {
        $this->redirectUri = $redirectUri;

    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }


    /**
     * @param mixed $authorizationCode
     * @return void
     */
    public function setAuthorizationCode($authorizationCode = null): void
    {
        $this->authorizationCode = $authorizationCode;

    }

    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * @param mixed $token
     * @return void
     */
    public function setToken($token = null): void
    {
        $this->token = $token;

    }

    public function getToken()
    {
        if (null === $this->token)
        {
            $tokenClass = $this->getTokenClass();
            $token = new $tokenClass();
            $this->token = $token;
        }

        return $this->token;
    }

    /**
     * @param mixed $tokenClass
     * @return void
     */
    public function setTokenClass($tokenClass = null): void
    {
        $this->validateClass($tokenClass, TokenInterface::class);
        $this->tokenClass = $tokenClass;

    }

    public function getTokenClass()
    {
        return $this->tokenClass;
    }

    /**
     * @param mixed $connectionClass
     * @return void
     */
    public function setConnectionClass($connectionClass = null): void
    {
        $this->validateClass($connectionClass, ConnectionInterface::class);
        $this->connectionClass = $connectionClass;

    }

    public function getConnectionClass()
    {
        return $this->connectionClass;
    }

    /**
     * @param mixed $connection
     * @return void
     * @throws BoxException
     */
    public function setConnection($connection = null): void
    {
        if (!$connection instanceof ConnectionInterface)
        {
            throw new BoxException("Invalid Class", BoxException::INVALID_CLASS);
        }
        $this->connection = $connection;

    }

    public function getConnection()
    {
        if (null === $this->connection)
        {
            $connectionClass = $this->getConnectionClass();
            $connection = new $connectionClass();
            if ($this->logger) {
                $connection->setLogger($this->logger);
            }
            $this->connection = $connection;
        }

        return $this->connection;
    }

    /**
     * @param mixed $fileClass
     * @return void
     */
    public function setFileClass($fileClass = null): void
    {
        $this->validateClass($fileClass, FileInterface::class);
        $this->fileClass = $fileClass;

    }

    public function getFileClass()
    {
        return $this->fileClass;
    }

    /**
     * @todo determine best validation for this
     *
     * @param null $files
     *
     * @return $this
     */
    /**
     * @param mixed $files
     * @return void
     */
    public function setFiles($files = null): void
    {
        $this->files = $files;

    }

    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $folderClass
     * @return void
     */
    public function setFolderClass($folderClass = null): void
    {
        $this->validateClass($folderClass, FolderInterface::class);
        $this->folderClass = $folderClass;

    }

    public function getFolderClass()
    {
        return $this->folderClass;
    }

    /**
     * @param mixed $folders
     * @return void
     */
    public function setFolders($folders = null): void
    {
        $this->folders = $folders;

    }

    /**
     * @param mixed $collaborationClass
     * @return void
     */
    public function setCollaborationClass($collaborationClass = null): void
    {
        $this->validateClass($collaborationClass, CollaborationInterface::class);
        $this->collaborationClass = $collaborationClass;

    }

    public function getCollaborationClass()
    {
        return $this->collaborationClass;
    }

    /**
     * @param mixed $userClass
     * @return void
     */
    public function setUserClass($userClass = null): void
    {
        $this->validateClass($userClass, UserInterface::class);
        $this->userClass = $userClass;

    }

    public function getUserClass()
    {
        return $this->userClass;
    }

    /**
     * @param mixed $groupClass
     * @return void
     */
    public function setGroupClass($groupClass = null): void
    {
        $this->validateClass($groupClass, GroupInterface::class);
        $this->groupClass = $groupClass;

    }

    public function getGroupClass()
    {
        return $this->groupClass;
    }

    /**
     * @param array $collaborations
     *
     * @return \Box\Model\Client\Client $this
     */
    /**
     * @param mixed $collaborations
     * @return void
     */
    public function setCollaborations($collaborations = null): void
    {
        $this->collaborations = $collaborations;

    }

    /**
     * @return array
     */
    public function getCollaborations()
    {
        return $this->collaborations;
    }

    /**
     * @param mixed $deviceId
     * @return void
     */
    public function setDeviceId($deviceId = null): void
    {
        $this->deviceId = $deviceId;

    }

    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param mixed $deviceName
     * @return void
     */
    public function setDeviceName($deviceName = null): void
    {
        $this->deviceName = $deviceName;

    }

    public function getDeviceName()
    {
        return $this->deviceName;
    }

    /**
     * @param mixed $state
     * @return void
     */
    public function setState($state = null): void
    {
        $this->state = $state;

    }

    public function getState()
    {
        return $this->state;
    }

    /**
     * @param \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface $root
     *
     * @return \Box\Model\Client\Client
     */
    /**
     * @param mixed $root
     * @return void
     */
    public function setRoot($root = null): void
    {
        $this->root = $root;

    }

    /**
     * @return \Box\Model\Folder\Folder|\Box\Model\Folder\FolderInterface
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param $uri
     *
     * @return mixed
     * @throws BoxException
     */
    public function query($uri = null)
    {
        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->query($uri);

        $json = $response->getContent();

        $data = json_decode($json, true);

        if (null === $data)
        {
            $data['error'] = "sdk_json_decode";
            $data['error_description'] = "unable to decode or recursion level too deep";
            $this->error($data);
        }

        if (array_key_exists('error', $data))
        {
            $this->error($data);
        }

        if (array_key_exists('type', $data) && 'error' === $data['type'])
        {
            $data['error'] = "sdk_unknown";
            $ditto = $data;
            $data['error_description'] = $ditto;
            $this->error($data);
        }

        return $data;
    }

    /**
     * @param string|null $query
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $type
     * @return mixed
     * @throws BoxException
     */
    public function search($query = null, $limit = null, $offset = null, $type = null)
    {
        if (empty($query))
        {
            throw new BoxException('please enter a search term', BoxException::INVALID_INPUT);
        }

        $uriQuery = rawurlencode($query);

        $uri = self::SEARCH_URI . "/?query=" . $uriQuery;

        if (is_string($type) && in_array($type, ['folder', 'file'])) {
            $uri .= "&type=" . $type;
        }

        if (is_numeric($limit) && is_int($limit))
        {
            $uri .= "&limit=" . $limit;
        }

        if (is_numeric($offset) && is_int($offset))
        {
            $uri .= "&offset=" . $offset;
        }

        $this->debug("full search uri: " . $uri, [__METHOD__, __LINE__]);

        return $this->query($uri);
    }
}
