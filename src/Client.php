<?php

namespace Box;

use Box\Auth\AuthProviderInterface;
use Box\Auth\Jwt\JwtProvider;
use Box\Auth\OAuth2Provider;
use Box\Auth\OAuth2ProviderInterface;
use Box\Connection\Connection;
use Box\Connection\ConnectionInterface;
use Box\Event\Auth\TokenExchanged;
use Box\Event\Auth\TokenRefreshed;
use Box\Event\Auth\TokenRevoked;
use Box\Event\Auth\TokenLoadedFromStorage;
use Box\Event\Auth\TokenSavedToStorage;
use Box\Connection\Token\TokenInterface;
use Box\Dto\PagedResult;
use Box\Dto\TokenStorageContext;
use Box\Exception\BoxException;
use Box\Exception\BoxResponseException;
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
use Box\Trait\BoxApiErrorTrait;
use Box\Factory\TokenFactory;
use Box\Factory\TokenFactoryInterface;
use JsonException;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionException;
use RuntimeException;

class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use BoxApiErrorTrait;

    public const string SEARCH_URI = "https://api.box.com/2.0/search";

    /**
     * @var ConnectionInterface|null
     */
    protected ?ConnectionInterface $connection = null;
    /**
     * @var array|null $folders array of folder items indexed by the folder ID
     *
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

    protected ?TokenStorageInterface $tokenStorage = null;
    protected ?TokenStorageContext $tokenStorageContext = null;

    protected ClientServiceRegistryInterface $serviceRegistry;

    protected ?AuthProviderInterface $authProvider = null;

    protected ?TokenFactoryInterface $tokenFactory = null;

    protected ?ClientConfig $config = null;

    private ?EventDispatcherInterface $eventDispatcher = null;

    public function __construct(
        ?ClientConfig $config = null,
        ?ClientServiceRegistryInterface $serviceRegistry = null
    ) {
        $this->config = $config;
        if (null !== $config) {
            $this->applyConfig($config);
        }
        $this->serviceRegistry = $serviceRegistry ?? new ClientServiceRegistry();
    }

    protected function applyConfig(ClientConfig $config): void
    {
        $this->clientId = $config->getOAuth2ClientId();
        $this->clientSecret = $config->getOAuth2ClientSecret();
        $this->authorizationCode = $config->getOAuth2AuthCode();
    }

    /**
     * @param array|null $options
     */
    public function getNewFolder(?array $options = null): Folder
    {
        return $this->serviceRegistry->getFolderFactory()->createFolder($options);
    }

    /**
     * @param array|null $options
     */
    public function getNewUser(?array $options = null): User
    {
        return $this->serviceRegistry->getUserFactory()->createUser($options);
    }

    /**
     * @param string|int $id use 0 for returning all folders
     * @param bool $retrieve if no folder is found, attempt to retrieve from box
     *
     * @throws BoxException
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

    /**
     * @param array|null $options
     */
    public function getNewGroup(?array $options = null): Group
    {
        return $this->serviceRegistry->getGroupFactory()->createGroup($options);
    }

    /**
     * @param array|null $options
     */
    public function getNewCollaboration(?array $options = null): Collaboration
    {
        return $this->serviceRegistry->getCollaborationFactory()->createCollaboration($options);
    }

    /**
     * @param array|null $options
     */
    public function getNewFile(?array $options = null): File
    {
        return $this->serviceRegistry->getFileFactory()->createFile($options);
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
     * @throws BoxException
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
     * @param int|null $limit leave null to get all; if limit is null but offset is numeric, limit will default to 100
     * @param int|null $offset leave null to get all; if limit is null but offset is numeric, limit will default to 100
     *
     * @throws ReflectionException
     * @throws BoxException
     * @return array returns an array of User objects that are in the group membership
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
            new Hydrator()->hydrate($user, $userData);
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
     * @throws BoxException
     */
    public function getFolderFromBox(string|int $id = 0): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->getFolder($id);
    }

    public function getBoxFolderItems(Folder $folder, int $limit = 100, int $offset = 0): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->getFolderItems($folder->getId(), $limit, $offset);
    }

    /**
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
     * @param array|null $options
     *
     * @throws BoxException
     * @throws JsonException
     */
    public function createNewBoxFolder(string $name, string|int $parentFolderId = 0, ?array $options = []): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->createFolder($name, $parentFolderId, $options);
    }

    /**
     * @param string|bool $ifMatchHeader etag string or true to use folder's current etag
     *
     * @throws JsonException
     */
    public function updateBoxFolder(Folder $folder, string|bool $ifMatchHeader = false): Folder
    {
        $folderService = $this->configureService($this->serviceRegistry->getFolderService());

        return $folderService->updateFolder($folder, $ifMatchHeader);
    }

    /**
     * @throws BoxException
     * @return PagedResult<Collaboration>
     */
    public function getFolderCollaborations(Folder $folder): PagedResult
    {
        $collaborationService = $this->configureService($this->serviceRegistry->getCollaborationService());

        return $collaborationService->getFolderCollaborations($folder);
    }

    /**
     * @param string $role see {@link http://developers.box.com/docs/#collaborations box documentation for all possible
     *     roles} default is viewer
     *
     * @throws BoxException
     * @throws JsonException
     * @throws ReflectionException
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

        $connection = $this->getConnection();

        $response = $connection->post($uri, json_encode($params, JSON_THROW_ON_ERROR));

        $data = $this->parseResponse($response);

        $collaboration = $this->getNewCollaboration();
        new Hydrator()->hydrate($collaboration, $data);

        return $collaboration;
    }

    /**
     * @param array|null $params shared link options; default shared link set to collaborator access, no unshared time or permissions set
     *
     * @throws BoxException
     * @throws JsonException
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
     * @throws \Exception
     * @throws BoxException
     * @throws JsonException
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

    // Post-v1: add multi-file upload convenience method

    /**
     * @throws JsonException
     */
    public function parseResponse(BoxResponseInterface $response): array
    {
        $data = $response->json(true);
        return is_array($data) ? $data : [];
    }

    /**
     * @throws BoxResponseException
     * @throws RuntimeException
     */
    public function uploadFileToBox(string|FileStream $file, string|int $parentId = 0): File
    {
        $fileService = $this->serviceRegistry->getFileService();
        $this->configureService($fileService);

        if (null !== $this->eventDispatcher) {
            $fileService->setEventDispatcher($this->eventDispatcher);
        }

        return $fileService->uploadFile($file, $parentId);
    }

    public function chunkedUpload(string|FileStream $file, string|int $parentId): File
    {
        $fileService = $this->serviceRegistry->getFileService();
        $this->configureService($fileService);

        if (null !== $this->eventDispatcher) {
            $fileService->setEventDispatcher($this->eventDispatcher);
        }

        return $fileService->chunkedUpload($file, $parentId);
    }

    /**
     * @throws BoxException if no authorization code is set
     * @throws JsonException
     */
    public function exchangeAuthorizationCodeForToken(): TokenInterface
    {
        $code = $this->getAuthorizationCode();
        if (null === $code) {
            throw new BoxException('Authorization code is required for exchange.', BoxException::INVALID_INPUT);
        }

        $token = $this->getAuthProvider()->exchangeAuthorizationCode($code);

        $this->setToken($token);
        $this->saveTokenToStorage($token);

        $this->eventDispatcher?->dispatch(new TokenExchanged($token));

        return $token;
    }

    /**
     * @throws BoxException
     * @throws JsonException
     */
    public function refreshToken(): TokenInterface
    {
        $token = $this->getToken();

        if (
            $this->getAuthProvider() instanceof OAuth2ProviderInterface
            && null === $token->getRefreshToken()
        ) {
            throw new BoxException(
                'Cannot refresh token: no refresh token available. OAuth2 requires a refresh token to renew access.'
            );
        }

        $newToken = $this->getAuthProvider()->refreshToken($token, []);

        $this->setToken($newToken);
        $this->saveTokenToStorage($newToken);

        $this->eventDispatcher?->dispatch(new TokenRefreshed($newToken));

        return $newToken;
    }

    /**
     * @throws BoxException
     */
    public function destroyToken(TokenInterface $token): array
    {
        $this->getAuthProvider()->revokeToken($token);
        $this->eventDispatcher?->dispatch(new TokenRevoked($token));

        return ['success' => true];
    }

    public function buildAuthorizationUrl(array $options = []): string
    {
        $state = $this->config?->getOAuth2State();
        if (null !== $state && !isset($options['state'])) {
            $options['state'] = $state;
        }

        return $this->getAuthProvider()->buildAuthorizationUrl($options);
    }

    public function setClientId(string $clientId = ''): void
    {
        $this->clientId = $clientId;
        if ($this->authProvider instanceof OAuth2ProviderInterface) {
            $this->authProvider->setClientId($clientId);
        }
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientSecret(string $clientSecret = ''): void
    {
        $this->clientSecret = $clientSecret;
        if ($this->authProvider instanceof OAuth2ProviderInterface) {
            $this->authProvider->setClientSecret($clientSecret);
        }
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

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
            $this->token = $this->getTokenFactory()->createToken();
            if ($this->logger && method_exists($this->token, 'setLogger')) {
                $this->token->setLogger($this->logger);
            }
        }

        return $this->token;
    }

    public function setTokenFactory(TokenFactoryInterface $tokenFactory): void
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function getTokenFactory(): TokenFactoryInterface
    {
        if (null === $this->tokenFactory) {
            $this->tokenFactory = new TokenFactory();
        }

        return $this->tokenFactory;
    }

    public function setAuthProvider(AuthProviderInterface $authProvider): void
    {
        $this->authProvider = $authProvider;

        if ($authProvider instanceof JwtProvider && null !== $this->eventDispatcher) {
            $authProvider->setEventDispatcher($this->eventDispatcher);
        }
    }

    public function getAuthProvider(): AuthProviderInterface
    {
        if (null === $this->authProvider) {
            $this->authProvider = new OAuth2Provider(
                $this->getConnection(),
                $this->getTokenFactory(),
                $this->getClientId(),
                $this->getClientSecret(),
                $this->config?->getOAuth2RedirectUri()
            );
        }

        return $this->authProvider;
    }

    public function isTokenExpired(): bool
    {
        if (null === $this->token) {
            return false;
        }

        return $this->token->isExpired();
    }

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

        if ($connection instanceof Connection && null !== $this->eventDispatcher) {
            $connection->setEventDispatcher($this->eventDispatcher);
        }
    }

    public function getConnection(): ConnectionInterface
    {
        if (null === $this->connection) {
            $this->connection = $this->serviceRegistry->getConnectionFactory()->createConnection();
            if ($this->logger) {
                $this->connection->setLogger($this->logger);
            }
            if ($this->token) {
                $this->connection->setAccessToken($this->token->getAccessToken());
            }
        }

        return $this->connection;
    }


    /**
     * @param array<string|int, File>|null $files
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
     */
    public function setFolders(?array $folders = null): void
    {
        $this->folders = $folders;
    }


    /**
     * @param array<int, Collaboration>|null $collaborations
     */
    public function setCollaborations(?array $collaborations = null): void
    {
        $this->collaborations = $collaborations;
    }

    public function getCollaborations(): ?array
    {
        return $this->collaborations;
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

            $this->eventDispatcher?->dispatch(new TokenLoadedFromStorage($token));
        }

        return $token;
    }

    /**
     * Save token to configured storage using provided or configured context.
     *
     * @param TokenInterface|null $token If null, the current Client token is used.
     * @param TokenStorageContext|null $context If null, the configured Client context is used.
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
        $this->eventDispatcher?->dispatch(new TokenSavedToStorage($token));
    }

    /**
     * Remove token from configured storage using provided or configured context.
     *
     * @param TokenStorageContext|null $context If null, the configured Client context is used.
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

    public function setRoot(?Folder $root = null): void
    {
        $this->root = $root;
    }

    public function getRoot(): ?Folder
    {
        return $this->root;
    }

    /**
     * @throws BoxException
     */
    public function query(string $uri): array
    {
        $connection = $this->getConnection();

        $response = $connection->query($uri);

        return $this->parseResponse($response);
    }

    /**
     * @throws BoxException
     */
    public function search(?string $query = null, ?int $limit = null, ?int $offset = null, ?string $type = null): array
    {
        $searchService = $this->configureService($this->serviceRegistry->getSearchService());

        return $searchService->search($query, $limit, $offset, $type);
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->eventDispatcher = $dispatcher;

        if ($this->connection instanceof Connection) {
            $this->connection->setEventDispatcher($dispatcher);
        }

        if ($this->authProvider instanceof JwtProvider) {
            $this->authProvider->setEventDispatcher($dispatcher);
        }
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @throws RuntimeException if an authenticated service has no access token set
     */
    protected function configureService(ServiceInterface $service): ServiceInterface
    {
        $service->setConnection($this->getConnection());

        if ($service instanceof AuthenticatedServiceInterface) {
            $token = $this->getToken();
            if (null === $token->getAccessToken()) {
                throw new RuntimeException("Access token is not set for authenticated service: " . $service::class);
            }
            $service->setToken($token);
        } else {
            try {
                $service->setToken($this->getToken());
            } catch (RuntimeException $e) {
                // Token not set on client, skip setting it on service
            }
        }

        if ($this->logger) {
            $service->setLogger($this->logger);
        }

        return $service;
    }
}
