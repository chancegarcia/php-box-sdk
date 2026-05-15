# Programmatic Usage Guide

This guide is intended for developers integrating the Box PHP SDK as a core dependency within their applications or building higher-level framework integrations (e.g., Symfony bundles).

## 1. Purpose and Scope

While the README provides a quickstart for immediate API interaction, this guide focuses on the **architectural integration** of the SDK. It defines how the SDK should be positioned within a host application's lifecycle, how to manage state (tokens), and how to handle errors and logging in a production environment.

## 2. Architectural Role of the SDK

The SDK functions as a **boundary layer** around the Box API. It encapsulates transport concerns, authentication protocols, and API-specific data structures.

### Recommendations:
- **Isolation:** Keep Box-specific logic (e.g., folder IDs, specific API calls) isolated from your core business logic.
- **Service Orientation:** Wrap the `Box\Client` in your own application-specific service or adapter. This allows you to define an interface that matches your domain's needs.
- **Logger Propagation:** When using a custom factory to create `Client` instances, ensure the logger is injected into the factory or directly onto the `Client`. The SDK automatically propagates the logger to internal models and connections.
- **Forward Compatibility:** This SDK is designed to be "framework-friendly." By following these patterns, you simplify future transitions to specialized integrations like a Symfony bundle.

## 3. Integration Model

Prefer **composition** over ad-hoc usage. Instead of creating a `new Client()` whenever you need to upload a file, define a service factory or use dependency injection.

### The Thin Integration Layer
Your application should have a small "Box Service" that:
1. Orchestrates `Client` instantiation.
2. Handles token retrieval from your persistent storage.
3. Maps your domain exceptions to Box-specific failures where necessary.

```php
// Example: A simplified application-level service
class BoxStorageService
{
    public function __construct(
        private Box\Client $client,
        private TokenRepository $tokenRepo
    ) {}

    public function storeDocument(string $localPath, string $targetFolderId): string
    {
        $token = $this->tokenRepo->getActiveToken();
        $this->client->setToken($token);

        $response = $this->client->uploadFileToBox($localPath, $targetFolderId);
        // ... handle response
    }
}
```

## 4. Client Lifecycle and Token Management

The `Box\Client` is stateful regarding its configuration (Client ID, Secret) and its current `Token`.

### Configuration
Always fail fast if required configuration is missing. Use environment variables or secure secret management to inject `BOX_CLIENT_ID` and `BOX_CLIENT_SECRET`.

### Token Persistence
The SDK provides a `Box\Connection\Token\Token` model. Your application is responsible for:
1. **Exchanging** the authorization code for an initial token.
2. **Persisting** the serialized token data (access token, refresh token, expiration).
3. **Reloading** the token into the `Client` for subsequent requests.
4. **Refreshing** the token before or upon expiration using `$client->refreshToken()`.

> **Note**: v1.0 ships a formal `TokenStorageInterface` with Filesystem and PDO backends. See [Token Storage](../migration/upgrading-0.11-to-1.0.md#token-storage) in the migration guide.

### Wiring Token Storage

The SDK ships two ready-made backends. Wire one to the `Client` so exchange, refresh, and revoke calls persist automatically:

```php
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\Filesystem\FilesystemTokenStorage;

$storage = new FilesystemTokenStorage('/var/storage/box-tokens');
$context = new TokenStorageContext(userId: 'user_123');

$client->setTokenStorage($storage);
$client->setTokenStorageContext($context);

// Attempt to restore a previously saved token
$token = $client->loadTokenFromStorage();

if (null === $token) {
    // First time: run OAuth2 exchange — token is saved automatically
    $client->setAuthorizationCode($_GET['code']);
    $token = $client->exchangeAuthorizationCodeForToken();
}
```

Use `Box\Storage\Token\Pdo\TokenStorage` instead when your application already has a database connection:

```php
use Box\Storage\Token\Pdo\TokenStorage as PdoTokenStorage;

$storage = new PdoTokenStorage($pdo, tableName: 'box_tokens');
$client->setTokenStorage($storage);
```

### Custom Token Storage

Both built-in backends implement `Box\Storage\Token\TokenStorageInterface`. Implement this interface to plug in any storage you need — Redis, DynamoDB, an encrypted vault, etc.:

```php
use Box\Storage\Token\TokenStorageInterface;
use Box\Dto\TokenStorageContext;
use Box\Connection\Token\TokenInterface;

class RedisTokenStorage implements TokenStorageInterface
{
    public function storeToken(TokenInterface $token, TokenStorageContext $context): void { /* ... */ }
    public function updateToken(TokenInterface $token, TokenStorageContext $context): void { /* ... */ }
    public function retrieveToken(TokenStorageContext $context): ?TokenInterface { /* ... */ }
    public function removeToken(TokenStorageContext $context): void { /* ... */ }
    public function clear(): void { /* ... */ }
}

$client->setTokenStorage(new RedisTokenStorage($redis));
```

### Token Refresh

```php
// Token Refresh Example
if ($token->isExpired()) {
    $newToken = $client->refreshToken();
    // Token is also saved to configured storage automatically
    $this->tokenRepo->save($newToken);
}
```

For more details on these changes, see the [v1 Upgrade Guide](../migration/upgrading-0.11-to-1.0.md).

## 4a. JWT / Server-to-Server (S2S)

JWT/S2S authentication is for integrations that act as the application itself (enterprise token) or as a specific managed user (app user token). No browser OAuth2 redirect is needed.

### Required Configuration

Set `BOX_AUTH_MODE=jwt` and the following environment variables in `.env` (or your secrets manager):

```
BOX_AUTH_MODE=jwt
BOX_JWT_CLIENT_ID=your_client_id
BOX_JWT_CLIENT_SECRET=your_client_secret
BOX_JWT_ENTERPRISE_ID=your_enterprise_id
BOX_JWT_PUBLIC_KEY_ID=your_key_id
BOX_JWT_PRIVATE_KEY_PATH=/path/to/private_key.pem
BOX_JWT_PRIVATE_KEY_PASSPHRASE=optional_passphrase
```

`BOX_JWT_PRIVATE_KEY_PATH` must point to the PEM private key file. The SDK reads the file contents — never the path — into `JwtAuthConfig::$privateKey`.

### Enterprise Token

Used for administrative operations (managing users, groups, events, etc.):

```php
use Box\Auth\Jwt\JwtAuthConfig;
use Box\Service\BoxClientFactory;
use Box\Service\EnvConfigProvider;

$configProvider = new EnvConfigProvider();
$config = new JwtAuthConfig(
    clientId:             $configProvider->getJwtClientId(),
    clientSecret:         $configProvider->getJwtClientSecret(),
    enterpriseId:         $configProvider->getJwtEnterpriseId(),
    publicKeyId:          $configProvider->getJwtPublicKeyId(),
    privateKey:           $configProvider->getJwtPrivateKey(),
    privateKeyPassphrase: $configProvider->getJwtPrivateKeyPassphrase(),
);

$factory = new BoxClientFactory($configProvider);
$client  = $factory->createJwtClient($config);

// Exchange for an enterprise access token
$provider = $client->getAuthProvider();
$token    = $provider->exchangeForEnterpriseToken();
$client->setToken($token);

$currentUser = $client->getCurrentUser();
```

### App User Token

Used to act as a specific managed (App) user:

```php
$token = $provider->exchangeForAppUserToken('123456789'); // Box App User ID
$client->setToken($token);

// The client now acts as that app user
$folder = $client->getFolder('0');
```

Tokens obtained via JWT are short-lived (typically 1 hour). Re-exchange rather than refresh — JWT tokens do not use refresh tokens.

### Auto-mode via EnvConfigProvider

If `BOX_AUTH_MODE=jwt` is set, `BoxClientFactory::createClientForCurrentMode()` automatically builds a JWT client:

```php
$factory = new BoxClientFactory($configProvider);
$client  = $factory->createClientForCurrentMode(); // JWT client when BOX_AUTH_MODE=jwt
```

## 5. Response and Error Model

The SDK uses `Box\Http\BoxResponseInterface` for all API responses and throws `Box\Exception\ApiException` (or more specific subclasses) when things go wrong.

### Exception Taxonomy
The SDK uses a refined exception hierarchy for better error handling:
- `ApiException`: Thrown when the Box API returns an error (4xx or 5xx status codes).
- `TransportException`: Thrown when a network or connection error occurs (e.g., timeout, DNS failure).
- `BoxResponseException`: Base for exceptions that include a `BoxResponseInterface`.
- `BoxException`: Base exception for all SDK errors.

### Service Layer Patterns
Services in v1.0 follow a "send-and-hydrate" pattern, ensuring that you receive typed resources instead of raw arrays.

- **Read Operations**: Use `getResourceFromBox()` to fetch and hydrate a resource.
- **Write Operations**: Use `sendUpdateAndHydrate()` to send a payload and hydrate the resulting resource.

Example with `UserService`:
```php
use Box\Service\UserService;

$userService = new UserService();
$userService->setToken($token);

// Returns a Box\Resource\User object
$user = $userService->getCurrentUser();
echo $user->getName();
```

### Robust Error Handling
An `ApiException` contains a `BoxResponseInterface` (via `$e->getResponse()`). This allows you to inspect the JSON payload for specific Box error codes and response headers.

**Key Security Feature:** The SDK automatically sanitizes sensitive data (access tokens, refresh tokens, client secrets) from exception messages and context information using its internal `Redactor`.

### Auth Aliases and Exchange
Use `exchangeAuthorizationCodeForToken()` for the OAuth2 code exchange step for better clarity.

```php
$client->setAuthorizationCode($_GET['code']);
$token = $client->exchangeAuthorizationCodeForToken();
// Equivalent to: $token = $client->getAccessToken();
```

### Authenticated Service Boundary
Services requiring an active session must implement `AuthenticatedServiceInterface`. The `Client` facade ensures that an access token is available before delegating calls to these services. If no token is set, a `RuntimeException` will be thrown.

```php
// Ensure client has a token before calling authenticated services
$client->setToken($token);
$folder = $client->getFolder('12345'); 
```

### Advanced Context and Retry-After
When Box returns a `429 Too Many Requests` status, the SDK parses the `Retry-After` header. You can access the parsed value directly from the response.

```php
try {
    $response = $client->uploadFileToBox($path, $folderId);
} catch (Box\Exception\ApiException $e) {
    // Check if it's a rate limit error
    if ($e->getCode() === 429) {
        $response = $e->getResponse();
        $delaySeconds = $response->getRetryAfter(); 
        
        if ($delaySeconds) {
            sleep($delaySeconds);
            // ... retry request
        }
    }

    // Inspect Box-specific error details via json() helper
    $errorData = $e->getResponse()->json();
    $boxCode = $errorData['code'] ?? 'unknown';
}
```

The SDK follows a "fail-fast" principle for client-side validation (e.g., missing tokens or invalid parameters) while providing detailed information for server-side errors.

### Error Recovery: 401 → Refresh → Retry

When an access token expires mid-session, the Box API returns HTTP 401. A robust integration should catch this, refresh the token, and retry once:

```php
use Box\Exception\ApiException;

function callWithRefresh(callable $fn, \Box\Client $client, TokenRepository $repo): mixed
{
    try {
        return $fn();
    } catch (ApiException $e) {
        if ($e->getCode() !== 401) {
            throw $e;
        }

        // Token expired — refresh and retry once
        $newToken = $client->refreshToken();
        $repo->save($newToken);

        return $fn();
    }
}

// Usage
$file = callWithRefresh(
    fn() => $client->getFile('98765'),
    $client,
    $tokenRepo
);
```

> **JWT note:** JWT tokens do not support refresh. Re-exchange via `$provider->exchangeForEnterpriseToken()` instead of calling `refreshToken()`.

## 6. File Uploads and Streaming

The SDK supports uploading files either from a local path or directly from a stream.

### Local File Upload
```php
$client->uploadFileToBox('/path/to/local/file.txt', 'target_folder_id');
```

### File Streaming with `FileStream`
For in-memory content or resources from other streams, use `Box\Http\FileStream`. This avoids writing temporary files to disk.

**Validation:** The SDK validates upload arguments (paths, parent IDs, and resources) before initiating network requests.

```php
use Box\Http\FileStream;

// From a string
$stream = FileStream::fromString('Dynamic content', 'report.csv', 'text/csv');
$client->uploadFileToBox($stream, '0');

// From an existing resource
$fh = fopen('https://example.com/remote-image.jpg', 'r');
$stream = new FileStream($fh, 'remote-image.jpg');
$client->uploadFileToBox($stream, 'folder_id');

// From a path with custom filename/mimetype
$stream = FileStream::fromPath('/tmp/raw_data', 'final_name.dat', 'application/octet-stream');
$client->uploadFileToBox($stream, 'folder_id');
```

### Model Validation and Hydration
The SDK models are hydrated via a centralized `Hydrator`. In v1.0, services handle this boundary internally.

### Data Types
- **IDs:** Box IDs are handled as `string|int`. The SDK will normalize these when communicating with the API.
- **Resources:** Typed objects (e.g., `Box\Resource\File`, `Box\Resource\User`) representing Box entities. Services handle hydration; resources do not perform network calls or self-map.

## 7. Chunked Upload

For files larger than ~50 MB, Box requires a multi-part upload session. The SDK provides both a high-level convenience method and low-level primitives for custom orchestration.

### Convenience Method

`Client::chunkedUpload()` handles session creation, part looping, SHA1 accumulation, and commit in one call:

```php
use Box\Http\FileStream;

// From a local path
$file = $client->chunkedUpload('/var/data/large-video.mp4', 'target_folder_id');

// From a FileStream (in-memory or remote source)
$stream = FileStream::fromPath('/tmp/export.zip', 'export.zip');
$file = $client->chunkedUpload($stream, 'target_folder_id');

echo $file->getId(); // Box file ID of the committed upload
```

### PSR-14 Events During Upload

Inject an `EventDispatcherInterface` to observe progress without polling:

```php
use Box\Event\File\UploadSessionCreated;
use Box\Event\File\UploadPartUploaded;
use Box\Event\File\UploadSessionCommitted;
use Box\Event\File\UploadSessionAborted;

$client->setEventDispatcher($dispatcher);

// Your listener (example with a simple closure dispatcher)
// UploadSessionCreated  — fired after the session is opened
// UploadPartUploaded    — fired after each part; carries $partNumber and $totalParts
// UploadSessionCommitted — fired on success; carries the committed File resource
// UploadSessionAborted   — fired on failure; carries $sessionId and $error
```

### Low-Level API (Custom Orchestration)

Use `FileService` directly when you need fine-grained control — resumable uploads, progress persistence, or custom retry logic per part:

```php
use Box\Service\File\FileService;
use Box\Dto\File\UploadPart;

$service = new FileService();
$service->setConnection($connection);
$service->setToken($token);

// 1. Open a session
$session = $service->createUploadSession('folder_id', 'video.mp4', $fileSizeBytes);

$parts  = [];
$hash   = hash_init('sha1');
$offset = 0;

$fh = fopen('/var/data/large-video.mp4', 'rb');

try {
    while (!feof($fh)) {
        $chunk = fread($fh, $session->partSize);
        if ($chunk === false || $chunk === '') {
            break;
        }

        hash_update($hash, $chunk);

        // 2. Upload each part
        $parts[]  = $service->uploadPart($session->sessionId, $chunk, $offset, $fileSizeBytes);
        $offset  += strlen($chunk);
    }

    $fileSha1 = base64_encode(hash_final($hash, true));

    // 3. Commit
    $file = $service->commitUploadSession($session->sessionId, $parts, $fileSha1);
} catch (\Throwable $e) {
    $service->abortUploadSession($session->sessionId);
    throw $e;
} finally {
    fclose($fh);
}
```

> **Resume tip:** Persist `$session->sessionId` and completed `$parts` between process restarts. Call `$service->listUploadSessionParts($sessionId)` to retrieve already-uploaded parts and calculate the correct `$offset` to continue from.

## 8. Logging and Observability

The SDK implements `Psr\Log\LoggerAwareInterface`. It is highly recommended to inject your application's PSR-3 compliant logger (e.g., Monolog).

- **Library Style:** The SDK logs internal transitions, API requests (at debug level), and errors using PSR-3 conventions and structured context.
- **Propagation:** Once a logger is set on the `Client`, it is automatically propagated to all objects created by the client, such as `Connection`, `Folder`, and `File` models.
- **Host Integration:** In a Symfony or Laravel app, the container should automatically inject the main application logger into the `Client` service.

## 9. Extension Points

Every major seam in the SDK is backed by an interface. You can swap in your own implementation by passing it to the `Client` or to the relevant service.

| Interface | Namespace | What to implement it for |
|---|---|---|
| `TokenStorageInterface` | `Box\Storage\Token` | Custom token persistence (Redis, vault, etc.) — see §4 |
| `ConnectionInterface` | `Box\Connection` | Custom HTTP transport or middleware wrapping |
| `TransportInterface` | `Box\Http\Transport` | Low-level HTTP adapter (replaces Guzzle) |
| `AuthProviderInterface` | `Box\Auth` | Custom auth flows beyond OAuth2 and JWT |
| `ConfigProviderInterface` | `Box\Contract` | Custom config source (Vault, SSM, typed config objects) |
| `BoxClientFactoryInterface` | `Box\Contract` | Custom `Client` construction and wiring |
| `WebhookVerifierInterface` | `Box\Webhook` | Alternative signature verification strategy |
| `ServiceInterface` | `Box\Service` | Base contract for any custom Box service |
| `AuthenticatedServiceInterface` | `Box\Service` | Custom service that requires an active token |
| `Psr\Log\LoggerInterface` | PSR-3 | Any PSR-3 logger (`Client::setLogger()`) |
| `Psr\EventDispatcher\EventDispatcherInterface` | PSR-14 | Any PSR-14 dispatcher (`Client::setEventDispatcher()`) |

Individual service interfaces (`FileServiceInterface`, `FolderServiceInterface`, `UserServiceInterface`, `SearchServiceInterface`, `CollaborationServiceInterface`, `GroupServiceInterface`, `UserEventServiceInterface`) are useful for mocking in unit tests or wrapping services with decoration.

For a full list of supported Box API endpoints per service, see [API Coverage](api-coverage.md).

### Composition Pattern

```php
// Swap the transport
$client->getConnection()->setTransport(new MyCustomTransport());

// Inject a PSR-3 logger
$client->setLogger($monolog);

// Inject a PSR-14 dispatcher
$client->setEventDispatcher($symfony->get(EventDispatcherInterface::class));

// Use a custom token storage
$client->setTokenStorage(new RedisTokenStorage($redis));
```

## 10. Best Practices

- **Security:** Never commit `BOX_CLIENT_SECRET` or any tokens to source control.
- **Infrastructure:** Treat the SDK as infrastructure. Your business logic shouldn't know how Box handles OAuth2; it should only care about "storing a file."
- **Testing:** Use the SDK's interfaces to mock the `Client` in your unit tests.

## 11. Folder Operations and Search

### Create a Folder

```php
$folder = $client->createFolder('My Project', parentFolderId: '0');
echo $folder->getId();   // Box folder ID
echo $folder->getName(); // 'My Project'
```

### List Folder Items

```php
$items = $client->getBoxFolderItems('0'); // '0' is the root folder

foreach ($items as $item) {
    // Each item is a Box\Resource\File or Box\Resource\Folder
    echo $item->getName() . PHP_EOL;
}
```

### Search

```php
$results = $client->search('quarterly report');

foreach ($results as $item) {
    echo $item->getName() . ' (' . $item->getId() . ')' . PHP_EOL;
}
```

> **Tip:** Box search is eventually consistent. Newly uploaded files may not appear in results immediately.

## 12. Design Patterns and Robustness

The SDK follows several internal design patterns to ensure robustness and ease of use.

### ID Handling
Box IDs are handled as `string|int` throughout the v0.11.x branch. This ensures compatibility with large numeric IDs that might exceed 32-bit integer limits or contain leading zeros while still allowing convenient integer usage for legacy code.

### File Streaming
The `Box\Http\FileStream` abstraction allows the SDK to handle file content from various sources (strings, resources, local paths) uniformly. This is particularly useful for serverless environments or memory-constrained integrations where writing to disk is undesirable.

### Model Validation
Resources in v1.0 are focused on data representation. Validation and hydration logic have been moved to the service and mapper layers to ensure a cleaner separation of concerns.

## 13. Webhook Verification

When Box delivers a webhook event to your endpoint, it includes two headers — `BOX-SIGNATURE-PRIMARY` and `BOX-SIGNATURE-SECONDARY` — computed with your webhook's signing keys. Always verify these before processing the payload.

```php
use Box\Webhook\WebhookVerifier;

// Keys are found in the Box Developer Console under your webhook configuration
$verifier = new WebhookVerifier(
    primaryKey:   $_ENV['BOX_WEBHOOK_PRIMARY_KEY'],
    secondaryKey: $_ENV['BOX_WEBHOOK_SECONDARY_KEY'],
);

$rawBody   = file_get_contents('php://input');
$timestamp = $_SERVER['HTTP_BOX_DELIVERY_TIMESTAMP'] ?? '';
$sigPrimary   = $_SERVER['HTTP_BOX_SIGNATURE_PRIMARY']   ?? '';
$sigSecondary = $_SERVER['HTTP_BOX_SIGNATURE_SECONDARY'] ?? '';

if (!$verifier->verify($rawBody, $timestamp, $sigPrimary, $sigSecondary)) {
    http_response_code(403);
    exit;
}

$payload = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
// Process $payload...
```

**Signing formula:** `base64(HMAC-SHA256(body + timestamp, key))`. The verifier checks both keys and accepts the payload if either signature matches — supporting key rotation without downtime.

> **Tip:** Validate the `BOX-DELIVERY-TIMESTAMP` header against your server time and reject payloads older than a few minutes to protect against replay attacks.

---

**See also:**
- [README.md](../README.md)
- [CLI Test Harness Guide](cli-test-harness.md)
- [Upgrading from 0.11 to 1.0](../migration/upgrading-0.11-to-1.0.md)
