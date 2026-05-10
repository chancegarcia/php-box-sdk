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

use Box\Collaboration\CollaborationInterface;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Exception\BoxException;
use Box\Factory\CollaborationFactory;
use Box\Factory\CollaborationFactoryInterface;
use Box\Factory\ConnectionFactory;
use Box\Factory\ConnectionFactoryInterface;
use Box\Factory\FileFactory;
use Box\Factory\FileFactoryInterface;
use Box\Factory\FolderFactory;
use Box\Factory\FolderFactoryInterface;
use Box\Factory\GroupFactory;
use Box\Factory\GroupFactoryInterface;
use Box\Factory\TokenFactory;
use Box\Factory\TokenFactoryInterface;
use Box\Factory\UserFactory;
use Box\Factory\UserFactoryInterface;
use Box\Resource\File;
use Box\Resource\Folder;
use Box\Resource\User;
use Box\Group\GroupInterface;
use Box\Service\Folder\FolderService;
use Box\Http\FileStream;
use Box\Http\Response\BoxResponseInterface;
use Box\Mapper\Hydrator;
use Box\Collaboration\Collaboration;
use Box\Connection\Token\Token;
use Box\Logger\LoggerAwareInterface;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxLoggerTrait;
use Box\Service\File\FileService;

/**
 * Class Client
 * @package Box
 */
class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use BoxLoggerTrait;

    public const AUTH_URI = "https://account.box.com/api/oauth2/authorize";
    public const TOKEN_URI = "https://www.box.com/api/oauth2/token";
    public const REVOKE_URI = "https://www.box.com/api/oauth2/revoke";
    public const SEARCH_URI = "https://api.box.com/2.0/search";

    protected ?string $state = null;

    /**
     * @var ConnectionInterface|null
     */
    protected ?ConnectionInterface $connection = null;
    /**
     * @var array of folder items indexed by the folder ID
     * @internal should just be an array of any folder known/retrieved by the client. does not need to be recursive
     *     since folders know their parents and items
     */
    protected ?array $folders = null;
    protected ?array $files = null;
    /**
     * @var array of collaborations
     */
    protected ?array $collaborations = null;

    /**
     * @var Folder|null
     */
    protected ?Folder $root = null;

    protected ?TokenInterface $token = null;

    protected ?string $authorizationCode = null;
    protected ?string $clientId = null;
    protected ?string $clientSecret = null;
    protected ?string $redirectUri = null;

    protected ?string $deviceId = null;
    protected ?string $deviceName = null;

    protected FolderFactoryInterface $folderFactory;
    protected FileFactoryInterface $fileFactory;
    protected UserFactoryInterface $userFactory;
    protected GroupFactoryInterface $groupFactory;
    protected CollaborationFactoryInterface $collaborationFactory;
    protected TokenFactoryInterface $tokenFactory;
    protected ConnectionFactoryInterface $connectionFactory;

    public function __construct(
        ?array $options = null,
        ?FolderFactoryInterface $folderFactory = null,
        ?FileFactoryInterface $fileFactory = null,
        ?UserFactoryInterface $userFactory = null,
        ?GroupFactoryInterface $groupFactory = null,
        ?CollaborationFactoryInterface $collaborationFactory = null,
        ?TokenFactoryInterface $tokenFactory = null,
        ?ConnectionFactoryInterface $connectionFactory = null
    ) {
        if (is_array($options)) {
            (new Hydrator())->hydrate($this, $options);
        }
        $this->folderFactory = $folderFactory ?? new FolderFactory();
        $this->fileFactory = $fileFactory ?? new FileFactory();
        $this->userFactory = $userFactory ?? new UserFactory();
        $this->groupFactory = $groupFactory ?? new GroupFactory();
        $this->collaborationFactory = $collaborationFactory ?? new CollaborationFactory();
        $this->tokenFactory = $tokenFactory ?? new TokenFactory();
        $this->connectionFactory = $connectionFactory ?? new ConnectionFactory();
    }

    /**
     * @param mixed $options
     *
     * @return Folder
     */
    public function getNewFolder(mixed $options = null): Folder
    {
        $instance = $this->folderFactory->createFolder($options);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }

    /**
     * @param mixed $options
     *
     * @return User
     */
    public function getNewUser(mixed $options = null): User
    {
        $instance = new User();
        // Options handling might be needed if legacy code passed them here
        return $instance;
    }

    /**
     * @param mixed $options
     *
     * @return GroupInterface
     */
    public function getNewGroup(mixed $options = null): GroupInterface
    {
        $instance = $this->groupFactory->createGroup($options);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }

    public function getNewCollaboration(mixed $options = null): CollaborationInterface
    {
        $instance = $this->collaborationFactory->createCollaboration($options);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }

    /**
     * @param mixed $options
     *
     * @return File
     */
    public function getNewFile(mixed $options = null): File
    {
        $instance = $this->fileFactory->createFile($options);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }

    /**
     * @param string|int $id use 0 for returning all folders
     * @param bool $retrieve if no folder is found, attempt to retrieve from box
     *
     * @return Folder|null returns null if no such folder exists and retrieve is false
     */
    public function getFolder(string|int $id = 0, bool $retrieve = true): ?Folder
    {
        $folders = $this->getFolders($retrieve);

        if (0 === $id) {
            return $folders[0] ?? null;
        }

        if (!array_key_exists($id, $folders)) {
            if (!$retrieve) {
                return null;
            }
            $folder = $this->getFolderFromBox($id);
            $this->addFolder($folder);
        }

        return $folders[$id] ?? null;
    }

    public function addFolder(mixed $folder): void
    {
        $folders = $this->getFolders(false) ?? [];
        $id = $folder->getId();
        if ($id) {
            $folders[$id] = $folder;
        } else {
            $folders[] = $folder;
        }
        $this->setFolders($folders);
    }

    public function getFolders(bool $retrieve = true): mixed
    {
        if (!$retrieve) {
            return $this->folders;
        }

        $root = $this->getRoot();
        if (null === $root) {
            $root = $this->getFolderFromBox();
            $this->setRoot($root);
        }

        // not sure if I should add recursive parsing of folder/items. stubbing out for now.
        return $this->folders ?? [];
    }

    /**
     * get membership list of a given group. if limit or offset is numeric, only retrieve specific list page;
     *
     * @param null $group
     * @param null $limit leave null to get all; if limit is null but offset is numeric, limit will default to 100
     * @param null $offset leave null to get all; if limit is null but offset is numeric, limit will default to 100
     *
     * @return array returns an array of User objects that are in the group membership
     * @throws \Box\Exception\BoxException
     */
    public function getGroupMembershipList($group = null, $limit = null, $offset = null)
    {
        if (is_numeric($group)) {
            $groupId = $group;
            $group = $this->getNewGroup();
            $group->setId($groupId);
        }

        if (!$group instanceof GroupInterface) {
            throw new BoxException("Group object expected", BoxException::INVALID_INPUT);
        }

        $members = [];
        $entries = [];

        if (is_numeric($limit) || is_numeric($offset)) {
            if (!is_numeric($limit)) {
                $limit = 100;
            }

            $uri = $group->getMembershipListUri($limit, $offset);

            $data = $this->query($uri);

            $entries = $data['entries'];
        } else {
            $limit = 100;
            $offset = 0;

            $uri = $group->getMembershipListUri($limit, $offset);

            $data = $this->query($uri);

            $totalMembers = $data['total_count'];

            $entries = $data['entries'];

            $currentTotal = count($entries);

            while ($currentTotal < $totalMembers) {
                if (0 != $offset) {
                    $nextPage = $group->getMembershipListUri($limit, $offset);
                    $data = $this->query($nextPage);
                    $moreEntries = $data['entries'];
                    $entries = array_merge($entries, $moreEntries);

                    $currentTotal = count($entries);
                }

                $offset += $limit;
            }
        }

        foreach ($entries as $entry) {
            $userData = $entry['user'];
            $user = $this->getNewUser();
            (new Hydrator())->hydrate($user, $userData);
            $members[] = $user;
        }

        return $members;
    }

    /**
     * @throws BoxException
     */
    public function getFolderBySharedUri($sharedUri = null)
    {
        if (!is_string($sharedUri)) {
            throw new BoxException('shared uri must be a string value', BoxException::INVALID_INPUT);
        }

        $uri = FolderService::SHARED_ITEM_ENDPOINT;
        $sSharedLinkHeader = "BoxApi: shared_link=" . $sharedUri;
        $aSharedLinkHeader = [$sSharedLinkHeader];

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection, $aSharedLinkHeader);

        $response = $connection->query($uri);

        $jsonData = $this->parseResponse($response);

        if (is_array($jsonData) && array_key_exists('type', $jsonData) && 'folder' === $jsonData['type']) {
            $folder = $this->getNewFolder();
            (new Hydrator())->hydrate($folder, $jsonData);
        } else {
            if (is_array($jsonData) && array_key_exists('type', $jsonData) && 'error' === $jsonData['type']) {
                $errorData['error'] = $jsonData['message'];
                $errorData['error_description'] = $jsonData;
                $this->error($errorData, null, $response);
            } else {
                $folder = false;
            }
        }

        return $folder;
    }

    /**
     * @param string|int $id
     * @return Folder
     * @throws BoxException
     */
    public function getFolderFromBox($id = 0): Folder
    {
        $uri = FolderService::ENDPOINT . '/' . $id; // all class constant URIs do not end in a slash

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->query($uri);

        $jsonData = $this->parseResponse($response);

        $folder = $this->getNewFolder();
        (new Hydrator())->hydrate($folder, $jsonData);

        return $folder;
    }

    /**
     * @param Folder $folder
     * @param int $limit
     * @param int $offset
     *
     * @return Folder
     */
    public function getBoxFolderItems($folder, $limit = 100, $offset = 0)
    {
        $uri = $folder->getBoxFolderItemsUri($limit, $offset);
        $data = $this->query($uri);

        $folder->setItemCollection($data);

        return $folder;
    }

    /**
     * @param string|int $id
     * @return array
     * @throws BoxException
     */
    public function getFolderItems($id = 0)
    {
        /**
         * @var Folder $folder
         */
        $folder = $this->getFolder($id);

        return $folder->getItems();
    }

    /**
     * @param string $name
     * @param string|int $parentFolderId
     * @param array $options
     *
     * @return Folder
     * @throws BoxException
     */
    public function createNewBoxFolder($name, $parentFolderId = 0, ?array $options = [])
    {
        $uri = FolderService::ENDPOINT;

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $params = [
            'name' => $name,
            'parent' => ['id' => (string)$parentFolderId]
        ];

        $params = array_merge_recursive($params, $options);

        $response = $connection->post($uri, $params, true);

        $jsonData = $this->parseResponse($response);

        $folder = $this->getNewFolder();
        (new Hydrator())->hydrate($folder, $jsonData);

        return $folder;
    }

    /**
     * @param Folder $folder
     * @param string|bool $ifMatchHeader etag string or true to use folder's current etag
     *
     * @throws BoxException
     * @return array updated folder data
     */
    public function updateBoxFolder($folder, $ifMatchHeader = false)
    {
        if (!$folder instanceof Folder) {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting Folder class. given (" . get_debug_type($folder) . ")";
            $this->error($err);
        }

        $uri = FolderService::ENDPOINT . '/' . $folder->getId();

        $params = [
            'name' => $folder->getName(),
            'description' => $folder->getDescription(),
        ];

        $parent = $folder->getParent();
        if (null !== $parent) {
            $parentId = null;
            if (is_array($parent) && isset($parent['id'])) {
                $parentId = $parent['id'];
            } elseif (is_object($parent) && method_exists($parent, 'getId')) {
                $parentId = $parent->getId();
            }

            if (null !== $parentId) {
                $params['parent'] = ['id' => $parentId];
            }
        }

        $sharedLink = $folder->getSharedLink();
        if (null !== $sharedLink) {
            if (method_exists($sharedLink, 'toArray')) {
                $params['shared_link'] = $sharedLink->toArray();
            } else {
                // Basic mapping if toArray is not available
                $params['shared_link'] = (array) $sharedLink;
            }
        }

        $params = array_filter($params, fn($v) => null !== $v);

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        if (true === $ifMatchHeader) {
            $ifMatchHeader = $folder->getEtag();
        }

        if (is_string($ifMatchHeader) && !empty($ifMatchHeader)) {
            $connection->addHeader('If-Match', $ifMatchHeader);
        }

        $response = $connection->put($uri, $params, true);

        return $this->parseResponse($response);
    }

    /**
     * @param null|Folder $folder
     *
     * @return mixed raw json data as an array
     * @throws BoxException
     */
    public function getFolderCollaborations($folder = null)
    {
        if (!$folder instanceof Folder) {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting Folder class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }
        $folderId = $folder->getId();
        $uri = FolderService::ENDPOINT . '/' . $folderId . '/collaborations';

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->query($uri);

        return $this->parseResponse($response);
    }

    /**
     * @param null|Folder $folder
     * @param null|User|GroupInterface $collaborator
     * @param string $role see {@link http://developers.box.com/docs/#collaborations box documentation for all possible
     *     roles} default is viewer
     *
     * @return Collaboration|CollaborationInterface
     * @throws BoxException
     */
    public function addCollaboration($folder = null, $collaborator = null, $role = 'viewer')
    {
        if (!$folder instanceof Folder) {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting Folder class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }

        if (!$collaborator instanceof User && !$collaborator instanceof GroupInterface) {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting User class. given (" . var_export($collaborator, true) . ")";
            $this->error($err);
        }

        $uri = CollaborationInterface::URI;

        $folderId = $folder->getId();
        $collaboratorId = $collaborator->getId();

        $params = [
            'item' => [
                "id" => $folderId,
                "type" => "folder"
            ],
            'accessible_by' => [
                "id" => $collaboratorId
            ],

            'role' => $role
        ];

        // can be refactored a bit more but the json encode works in the connection class
        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->post($uri, $params, true);

        $data = $this->parseResponse($response);

        $collaboration = $this->getNewCollaboration();
        (new Hydrator())->hydrate($collaboration, $data);

        return $collaboration;
    }

    /**
     * @param null|Folder $folder
     * @param array|null shared link options with
     * default shared link set to collaborator access, no unshared time or permissions set to
     *
     * @return Folder
     * @throws BoxException
     */
    public function createSharedLinkForFolder($folder = null, $params = null)
    {
        if (!$folder instanceof Folder) {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting Folder class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }

        $uri = FolderService::ENDPOINT;

        $folderId = $folder->getId();

        $uri .= "/" . $folderId;

        if (!is_array($params)) {
            $params = [
                'shared_link' => [
                    'access' => 'collaborators'
                ]
            ];
        }

        // can be refactored a bit more but the json encode works in the connection class
        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->put($uri, $params, true);

        $data = $this->parseResponse($response);

        $updatedFolder = $this->getNewFolder();
        (new Hydrator())->hydrate($updatedFolder, $data);

        return $updatedFolder;
    }

    /**
     * @param Folder $originalFolder
     * @param Folder|array $parent
     * @param string $name
     * @param bool $addToFolders
     *
     * @return Folder
     * @throws \Exception
     * @throws BoxException
     * @internal param $destinationId
     */
    public function copyBoxFolder($originalFolder, $parent, $name = null, $addToFolders = true)
    {
        if (!$originalFolder instanceof Folder) {
            $this->error([
                'error' => 'Folder expected',
                'error_description' => $originalFolder
            ]);
        }

        $uri = FolderService::ENDPOINT . '/' . $originalFolder->getId() . '/copy';
        $this->debug("copy uri: " . $uri, [__METHOD__, __LINE__]);
        $this->debug("initial parent: " . var_export($parent, true), [__METHOD__, __LINE__]);

        if (is_array($parent)) {
            $folder = $this->getNewFolder();
            (new Hydrator())->hydrate($folder, $parent);
            $parent = $folder;
        }

        if (!$parent instanceof Folder) {
            $this->error([
                'error' => 'Folder expected',
                'error_description' => $parent
            ]);
        }

        $params['parent'] = ['id' => (string)$parent->getId()];
        if (null !== $name) {
            $params['name'] = $name;
        }

        $this->debug("params: " . var_export($params, true), [__METHOD__, __LINE__]);

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->post($uri, $params, true);
        $this->debug("response header: " . var_export($response->getResponseHeader(), true), [__METHOD__, __LINE__]);

        $data = $this->parseResponse($response);

        $copy = $this->getNewFolder();
        (new Hydrator())->hydrate($copy, $data);

        if (true === $addToFolders && $copy instanceof Folder) {
            $this->addFolder($copy);
        }

        return $copy;
    }

    // @todo make multiple file upload

    /**
     * @param BoxResponseInterface $response
     * @return array
     * @throws BoxException
     */
    public function parseResponse(BoxResponseInterface $response): array
    {
        $data = $response->json(true);
        return is_array($data) ? $data : [];
    }

    /**
     * @param string|FileStream $file
     * @param string|int $parentId
     * @return array
     * @throws BoxException
     */
    public function uploadFileToBox(string|FileStream $file, string|int $parentId = 0): array
    {
        $accessToken = $this->getToken()->getAccessToken();
        if (empty($accessToken) || trim($accessToken) === '') {
            throw new BoxException('BOX_ACCESS_TOKEN is required for upload.', BoxException::INVALID_INPUT);
        }

        $uri = FileService::UPLOAD_ENDPOINT;

        $connection = $this->getConnection();
        $this->setConnectionAuthHeader($connection);

        $response = $connection->postFile($uri, $file, $parentId);

        return $this->parseResponse($response);
    }

    public function exchangeAuthorizationCodeForToken()
    {
        return $this->getAccessToken();
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
        $data = $this->parseResponse($response);

        $token = $this->getToken();
        $this->setTokenData($token, $data);

        return $token;
    }

    /**
     * @return Token|TokenInterface
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
        if (null !== $deviceId) {
            $params['device_id'] = $deviceId;
        }

        $deviceName = $this->getDeviceName();
        if (null !== $deviceName) {
            $params['device_name'] = $deviceName;
        }

        $connection = $this->getConnection();

        $response = $connection->post(self::TOKEN_URI, $params);

        $data = $this->parseResponse($response);

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
     * @param $token \Box\Connection\Token\TokenInterface
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
     * @param $token \Box\Connection\Token\TokenInterface|\Box\Connection\Token\Token
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

        return $this->parseResponse($response);
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
        $params = [];

        $params['response_type'] = "code";

        $clientId = $this->getClientId();
        $params['client_id'] = $clientId;

        $state = $this->getState();
        if (null !== $state) {
            $params['state'] = $state;
        }

        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $uri .= $query;

        $redirectUri = $this->getRedirectUri();

        if (null !== $redirectUri) {
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
    public function setConnectionAuthHeader(ConnectionInterface $connection, ?array $additionalHeaders = null): void
    {
        $token = $this->getToken();
        $accessToken = $token->getAccessToken();
        if (null === $accessToken || "" === $accessToken) {
             throw new BoxException('BOX_ACCESS_TOKEN is required for upload.', BoxException::INVALID_INPUT);
        }

        $authorizationHeader = 'Authorization: Bearer ' . $accessToken;

        // SYNC: ensure connection has the access token
        if ($connection instanceof ConnectionInterface) {
            $connection->setAccessToken($this->getToken()->getAccessToken());
        }

        if (null !== $additionalHeaders && !is_array($additionalHeaders)) {
            throw new BoxException('additional headers must be in array format', BoxException::INVALID_INPUT);
        }

        if (is_array($additionalHeaders)) {
            foreach ($additionalHeaders as $name => $value) {
                if (is_int($name)) {
                    // if it's "Name: Value" string
                    $parts = explode(':', $value, 2);
                    if (count($parts) === 2) {
                        $connection->addHeader(trim($parts[0]), trim($parts[1]));
                    }
                } else {
                    $connection->addHeader($name, $value);
                }
            }
        }

        // header opt will require a merge with other headers to not overwrite.
        // @todo refactor to allow additional headers with auth header
        // For compatibility, we still call setCurlOpts if it's a CurlTransport or if someone depends on it
        $headers = [$authorizationHeader];
        if (is_array($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }
        $connection->setCurlOpts(['CURLOPT_HTTPHEADER' => $headers]);
    }

    /**
     * @param string|null $clientId
     * @return void
     */
    public function setClientId(?string $clientId = null): void
    {
        $this->clientId = $clientId;
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->setClientId($clientId);
        }
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientSecret
     *
     * @return void
     */
    public function setClientSecret(?string $clientSecret = null): void
    {
        $this->clientSecret = $clientSecret;
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->setClientSecret($clientSecret);
        }
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    /**
     * @param string|null $redirectUri
     *
     * @return void
     */
    public function setRedirectUri(?string $redirectUri = null): void
    {
        $this->redirectUri = $redirectUri;
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->setRedirectUri($redirectUri);
        }
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }


    /**
     * @param string|null $authorizationCode
     * @return void
     */
    public function setAuthorizationCode(?string $authorizationCode = null): void
    {
        $this->authorizationCode = $authorizationCode;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function setToken(?TokenInterface $token = null): void
    {
        $this->token = $token;
    }

    public function getToken(): TokenInterface
    {
        if (null === $this->token) {
            $this->token = $this->tokenFactory->createToken();
            if ($this->logger && method_exists($this->token, 'setLogger')) {
                $this->token->setLogger($this->logger);
            }
        }

        return $this->token;
    }

    /**
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        if (null === $this->token) {
            return false;
        }

        return $this->token->isExpired();
    }

    /**
     * @return int|null
     */
    public function getRemainingTokenLifetime(): ?int
    {
        if (null === $this->token) {
            return null;
        }

        $expiresIn = $this->token->getExpiresIn();
        $receivedAt = $this->token->getReceivedAt();

        if (null === $expiresIn || null === $receivedAt) {
            return null;
        }

        $now = time();
        $expirationTime = $receivedAt + (int) $expiresIn;
        $remaining = $expirationTime - $now;

        return max(0, $remaining);
    }


    public function setConnection(?ConnectionInterface $connection = null): void
    {
        $this->connection = $connection;
    }

    public function getConnection(): ConnectionInterface
    {
        if (null === $this->connection) {
            $this->connection = $this->connectionFactory->createConnection();
            if ($this->logger) {
                $this->connection->setLogger($this->logger);
            }
            $this->connection->setClientId($this->getClientId());
            $this->connection->setClientSecret($this->getClientSecret());
            $this->connection->setRedirectUri($this->getRedirectUri());
            if ($this->token) {
                $this->connection->setAccessToken($this->token->getAccessToken());
            }
        }

        return $this->connection;
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
    public function setFiles(mixed $files = null): void
    {
        $this->files = $files;
    }

    public function getFiles()
    {
        return $this->files;
    }


    /**
     * @param mixed $folders
     * @return void
     */
    public function setFolders(mixed $folders = null): void
    {
        $this->folders = $folders;
    }


    /**
     * @param array $collaborations
     *
     */
    /**
     * @param mixed $collaborations
     * @return void
     */
    public function setCollaborations(mixed $collaborations = null): void
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
    public function setDeviceId(mixed $deviceId = null): void
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
    public function setDeviceName(mixed $deviceName = null): void
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
    public function setState(mixed $state = null): void
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    /**
     * @param Folder $root
     */
    /**
     * @param mixed $root
     * @return void
     */
    public function setRoot(mixed $root = null): void
    {
        $this->root = $root;
    }

    /**
     * @return Folder
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

        return $this->parseResponse($response);
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
        if (empty($query)) {
            throw new BoxException('please enter a search term', BoxException::INVALID_INPUT);
        }

        $uriQuery = rawurlencode($query);

        $uri = self::SEARCH_URI . "/?query=" . $uriQuery;

        if (is_string($type) && in_array($type, ['folder', 'file'])) {
            $uri .= "&type=" . $type;
        }

        if (is_numeric($limit) && is_int($limit)) {
            $uri .= "&limit=" . $limit;
        }

        if (is_numeric($offset) && is_int($offset)) {
            $uri .= "&offset=" . $offset;
        }

        $this->debug("full search uri: " . $uri, [__METHOD__, __LINE__]);

        return $this->query($uri);
    }
}
