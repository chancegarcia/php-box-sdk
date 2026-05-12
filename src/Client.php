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

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Exception\BoxException;
use Box\Resource\Collaboration;
use Box\Resource\File;
use Box\Resource\Folder;
use Box\Resource\User;
use Box\Resource\Group;
use Box\Service\Collaboration\CollaborationService;
use Box\Service\AuthenticatedServiceInterface;
use Box\Service\ClientServiceRegistry;
use Box\Service\ClientServiceRegistryInterface;
use Box\Service\ServiceInterface;
use Box\Storage\Token\TokenStorageInterface;
use Box\Http\FileStream;
use Box\Http\Response\BoxResponseInterface;
use Box\Mapper\Hydrator;
use Box\Logger\LoggerAwareInterface;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxLoggerTrait;

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

    protected ?TokenStorageInterface $tokenStorage = null;
    protected ?TokenStorageContext $tokenStorageContext = null;

    protected ClientServiceRegistryInterface $serviceRegistry;

    public function __construct(
        ?ClientConfig $config = null,
        ?ClientServiceRegistryInterface $serviceRegistry = null
    ) {
        if ($config instanceof ClientConfig) {
            $this->applyConfig($config);
        }
        $this->serviceRegistry = $serviceRegistry ?? new ClientServiceRegistry();
    }

    protected function applyConfig(ClientConfig $config): void
    {
        $this->clientId = $config->getClientId();
        $this->clientSecret = $config->getClientSecret();
        $this->redirectUri = $config->getRedirectUri();
        $this->authorizationCode = $config->getAuthorizationCode();
        $this->deviceId = $config->getDeviceId();
        $this->deviceName = $config->getDeviceName();
        $this->state = $config->getState();
    }

    /**
     * @param array|null $options
     *
     * @return Folder
     */
    public function getNewFolder(?array $options = null): Folder
    {
        return $this->serviceRegistry->getFolderFactory()->createFolder($options);
    }

    /**
     * @param array|null $options
     *
     * @return User
     */
    public function getNewUser(?array $options = null): User
    {
        return $this->serviceRegistry->getUserFactory()->createUser($options);
    }

    /**
     * @param array|null $options
     *
     * @return Group
     */
    public function getNewGroup(?array $options = null): Group
    {
        return $this->serviceRegistry->getGroupFactory()->createGroup($options);
    }

    /**
     * @param array|null $options
     *
     * @return Collaboration
     */
    public function getNewCollaboration(?array $options = null): Collaboration
    {
        return $this->serviceRegistry->getCollaborationFactory()->createCollaboration($options);
    }

    /**
     * @param array|null $options
     *
     * @return File
     */
    public function getNewFile(?array $options = null): File
    {
        return $this->serviceRegistry->getFileFactory()->createFile($options);
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

    public function addFolder(Folder $folder): void
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

    /**
     * @param bool $retrieve
     * @return array<string|int, Folder>|null
     */
    public function getFolders(bool $retrieve = true): ?array
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
    public function getGroupMembershipList(Group|string|int|null $group = null, ?int $limit = null, ?int $offset = null): array
    {
        if (is_numeric($group)) {
            $groupId = (string) $group;
            $group = $this->getNewGroup();
            $group->setId($groupId);
        }

        if (!$group instanceof Group) {
            throw new BoxException("Group object expected", BoxException::INVALID_INPUT);
        }

        $groupService = $this->configureService($this->serviceRegistry->getGroupService());

        if (is_numeric($limit) || is_numeric($offset)) {
            if (!is_numeric($limit)) {
                $limit = 100;
            }

            $data = $groupService->getGroupMembershipList($group->getId(), $limit, (int) ($offset ?? 0));
            $entries = $data['entries'];
        } else {
            $limit = 100;
            $offset = 0;

            $data = $groupService->getGroupMembershipList($group->getId(), $limit, $offset);

            $totalMembers = $data['total_count'] ?? count($data['entries']);

            $entries = $data['entries'];

            $currentTotal = count($entries);

            while ($currentTotal < $totalMembers) {
                $offset += $limit;
                $data = $groupService->getGroupMembershipList($group->getId(), $limit, $offset);
                $moreEntries = $data['entries'];
                foreach ($moreEntries as $moreEntry) {
                    $entries[] = $moreEntry;
                }

                $currentTotal = count($entries);
                if (empty($moreEntries)) {
                    break;
                }
            }
        }

        $members = [];
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
    public function getFolderBySharedUri(?string $sharedUri = null): ?Folder
    {
        if (null === $sharedUri) {
            throw new BoxException('shared uri must be a string value', BoxException::INVALID_INPUT);
        }

        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->getFolderBySharedUri($sharedUri);
    }

    /**
     * @param string|int $id
     * @return Folder
     * @throws BoxException
     */
    public function getFolderFromBox(string|int $id = 0): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->getFolder($id);
    }

    /**
     * @param Folder $folder
     * @param int $limit
     * @param int $offset
     *
     * @return Folder
     */
    public function getBoxFolderItems(Folder $folder, int $limit = 100, int $offset = 0): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->getFolderItems($folder->getId(), $limit, $offset);
    }

    /**
     * @param string|int $id
     * @return array
     * @throws BoxException
     */
    public function getFolderItems(string|int $id = 0): array
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
     * @param array|null $options
     *
     * @return Folder
     * @throws BoxException
     */
    public function createNewBoxFolder(string $name, string|int $parentFolderId = 0, ?array $options = []): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->createFolder($name, $parentFolderId, $options);
    }

    /**
     * @param Folder $folder
     * @param string|bool $ifMatchHeader etag string or true to use folder's current etag
     *
     * @throws BoxException
     * @return array updated folder data
     */
    public function updateBoxFolder(Folder $folder, string|bool $ifMatchHeader = false): array
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->updateFolder($folder, $ifMatchHeader);
    }

    /**
     * @param Folder $folder
     *
     * @return array raw json data as an array
     * @throws BoxException
     */
    public function getFolderCollaborations(Folder $folder): array
    {
        $collaborationService = $this->configureService($this->serviceRegistry->getCollaborationService());

        return $collaborationService->getFolderCollaborations($folder);
    }

    /**
     * @param Folder $folder
     * @param User|Group $collaborator
     * @param string $role see {@link http://developers.box.com/docs/#collaborations box documentation for all possible
     *     roles} default is viewer
     *
     * @return Collaboration
     * @throws BoxException
     */
    public function addCollaboration(Folder $folder, User|Group $collaborator, string $role = 'viewer'): Collaboration
    {
        $uri = CollaborationService::ENDPOINT;

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
    public function createSharedLinkForFolder(?Folder $folder = null, ?array $params = null): Folder
    {
        if (!$folder instanceof Folder) {
            $err['error'] = 'sdk_unexpected_type';
            $err['error_description'] = "expecting Folder class. given (" . var_export($folder, true) . ")";
            $this->error($err);
        }

        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->createSharedLink($folder, $params);
    }

    /**
     * @param Folder $originalFolder
     * @param Folder $parent
     * @param string|null $name
     * @param bool $addToFolders
     *
     * @return Folder
     * @throws \Exception
     * @throws BoxException
     * @internal param $destinationId
     */
    public function copyBoxFolder(Folder $originalFolder, Folder $parent, ?string $name = null, bool $addToFolders = true): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());
        $copy = $folderService->copyFolder($originalFolder, $parent, $name);

        if (true === $addToFolders) {
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
        $fileService = $this->configureService($this->serviceRegistry->getFileService());

        return $fileService->uploadFile($file, $parentId);
    }

    public function exchangeAuthorizationCodeForToken(): TokenInterface
    {
        return $this->getAccessToken();
    }

    public function getAccessToken(): TokenInterface
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

        $this->saveTokenToStorage($token);

        return $token;
    }

    /**
     * @return TokenInterface
     */
    public function refreshToken(): TokenInterface
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

        $this->saveTokenToStorage($token);

        return $token;
    }

    public function getAuthorizationHeader(): string
    {
        $token = $this->getToken();

        return "Authorization: Bearer " . $token->getAccessToken();
    }

    /**
     * @param TokenInterface $token
     * @param array $data
     */
    public function setTokenData(TokenInterface $token, array $data): void
    {
        $token->setAccessToken($data['access_token']);
        $token->setExpiresIn($data['expires_in']);
        $token->setTokenType($data['token_type']);
        $token->setRefreshToken($data['refresh_token']);
    }

    /**
     * @param TokenInterface $token
     *
     * @return array
     * @throws BoxException
     */
    public function destroyToken(TokenInterface $token): array
    {
        $params['client_id'] = $this->getClientId();
        $params['client_secret'] = $this->getClientSecret();
        // The access_token or refresh_token to be destroyed. Only one is required, though both will be destroyed.
        $params['token'] = $token->getAccessToken();

        $connection = $this->getConnection();

        $response = $connection->post(self::REVOKE_URI, $params);

        return $this->parseResponse($response);
    }

    public function auth(): void
    {
        // build get query to auth uri
        $query = $this->buildAuthQuery();

        // send get query to auth uri (auth uri will redirect to app redirect uri)
        $connection = $this->getConnection();

        // can't get return data b/c of redirect
        $connection->query($query);
    }

    public function buildAuthQuery(): string
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

        // SYNC: ensure connection has the access token
        $connection->setAccessToken($accessToken);

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
            $this->token = $this->serviceRegistry->getTokenFactory()->createToken();
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
            $this->connection = $this->serviceRegistry->getConnectionFactory()->createConnection();
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
     * @param array<string|int, File>|null $files
     * @return void
     */
    public function setFiles(?array $files = null): void
    {
        $this->files = $files;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }


    /**
     * @param array<string|int, Folder>|null $folders
     * @return void
     */
    public function setFolders(?array $folders = null): void
    {
        $this->folders = $folders;
    }


    /**
     * @param array<int, Collaboration>|null $collaborations
     * @return void
     */
    public function setCollaborations(?array $collaborations = null): void
    {
        $this->collaborations = $collaborations;
    }

    /**
     * @return array
     */
    public function getCollaborations(): ?array
    {
        return $this->collaborations;
    }

    public function setDeviceId(?string $deviceId = null): void
    {
        $this->deviceId = $deviceId;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function setDeviceName(?string $deviceName = null): void
    {
        $this->deviceName = $deviceName;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function setState(?string $state = null): void
    {
        $this->state = $state;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setTokenStorage(?TokenStorageInterface $tokenStorage = null): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getTokenStorage(): ?TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function setTokenStorageContext(?TokenStorageContext $tokenStorageContext = null): void
    {
        $this->tokenStorageContext = $tokenStorageContext;
    }

    public function getTokenStorageContext(): ?TokenStorageContext
    {
        return $this->tokenStorageContext;
    }

    /**
     * Load token from configured storage using provided or configured context.
     * If successful, the loaded token is set on the Client.
     *
     * @param TokenStorageContext|null $context
     * @return TokenInterface|null
     */
    public function loadTokenFromStorage(?TokenStorageContext $context = null): ?TokenInterface
    {
        $storage = $this->getTokenStorage();
        $context = $context ?? $this->getTokenStorageContext();

        if (null === $storage || null === $context) {
            return null;
        }

        $token = $storage->retrieveToken($context);

        if (null !== $token) {
            $this->setToken($token);
        }

        return $token;
    }

    /**
     * Save token to configured storage using provided or configured context.
     *
     * @param TokenInterface|null $token If null, the current Client token is used.
     * @param TokenStorageContext|null $context If null, the configured Client context is used.
     * @return void
     */
    public function saveTokenToStorage(?TokenInterface $token = null, ?TokenStorageContext $context = null): void
    {
        $storage = $this->getTokenStorage();
        $context = $context ?? $this->getTokenStorageContext();
        $token = $token ?? $this->token;

        if (null === $storage || null === $context || null === $token) {
            return;
        }

        $storage->storeToken($token, $context);
    }

    /**
     * Remove token from configured storage using provided or configured context.
     *
     * @param TokenStorageContext|null $context If null, the configured Client context is used.
     * @return void
     */
    public function removeTokenFromStorage(?TokenStorageContext $context = null): void
    {
        $storage = $this->getTokenStorage();
        $context = $context ?? $this->getTokenStorageContext();

        if (null === $storage || null === $context) {
            return;
        }

        $storage->removeToken($context);
    }

    /**
     * @param Folder|null $root
     * @return void
     */
    public function setRoot(?Folder $root = null): void
    {
        $this->root = $root;
    }

    /**
     * @return Folder|null
     */
    public function getRoot(): ?Folder
    {
        return $this->root;
    }

    /**
     * @param string $uri
     *
     * @return array
     * @throws BoxException
     */
    public function query(string $uri): array
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
     * @return array
     * @throws BoxException
     */
    public function search(?string $query = null, ?int $limit = null, ?int $offset = null, ?string $type = null): array
    {
        $searchService = $this->configureService($this->serviceRegistry->getSearchService());

        return $searchService->search($query, $limit, $offset, $type);
    }

    protected function configureService(ServiceInterface $service): ServiceInterface
    {
        $service->setConnection($this->getConnection());
        $service->setAuthorizedConnection($this->getConnection());
        $service->setClientId($this->getClientId());
        $service->setClientSecret($this->getClientSecret());
        $service->setDeviceId($this->getDeviceId());
        $service->setDeviceName($this->getDeviceName());

        if ($service instanceof AuthenticatedServiceInterface) {
            $token = $this->getToken();
            if (null === $token->getAccessToken()) {
                throw new \RuntimeException("Access token is not set for authenticated service: " . $service::class);
            }
            $service->setToken($token);
        } else {
            try {
                $service->setToken($this->getToken());
            } catch (\RuntimeException $e) {
                // Token not set on client, skip setting it on service
            }
        }

        if ($this->logger) {
            $service->setLogger($this->logger);
        }

        return $service;
    }
}
