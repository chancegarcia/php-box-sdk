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

```php
// Token Refresh Example
if ($token->isExpired()) {
    $newToken = $client->refreshToken();
    // Your application persists $newToken
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

## 8. Logging and Observability

The SDK implements `Psr\Log\LoggerAwareInterface`. It is highly recommended to inject your application's PSR-3 compliant logger (e.g., Monolog).

- **Library Style:** The SDK logs internal transitions, API requests (at debug level), and errors using PSR-3 conventions and structured context.
- **Propagation:** Once a logger is set on the `Client`, it is automatically propagated to all objects created by the client, such as `Connection`, `Folder`, and `File` models.
- **Host Integration:** In a Symfony or Laravel app, the container should automatically inject the main application logger into the `Client` service.

## 9. Extension and Composition Patterns

To maintain a clean boundary:
- **Factories:** Use a factory to create the `Client` with the correct `Logger` and base configuration.
- **Adapters:** If your application supports multiple storage backends, implement a `StorageInterface` and create a `BoxStorageAdapter` that wraps the SDK.

## 10. Best Practices

- **Security:** Never commit `BOX_CLIENT_SECRET` or any tokens to source control.
- **Infrastructure:** Treat the SDK as infrastructure. Your business logic shouldn't know how Box handles OAuth2; it should only care about "storing a file."
- **Testing:** Use the SDK's interfaces to mock the `Client` in your unit tests.

## 12. Design Patterns and Robustness

The SDK follows several internal design patterns to ensure robustness and ease of use.

### ID Handling
Box IDs are handled as `string|int` throughout the v0.11.x branch. This ensures compatibility with large numeric IDs that might exceed 32-bit integer limits or contain leading zeros while still allowing convenient integer usage for legacy code.

### File Streaming
The `Box\Http\FileStream` abstraction allows the SDK to handle file content from various sources (strings, resources, local paths) uniformly. This is particularly useful for serverless environments or memory-constrained integrations where writing to disk is undesirable.

### Model Validation
Resources in v1.0 are focused on data representation. Validation and hydration logic have been moved to the service and mapper layers to ensure a cleaner separation of concerns.

---

**See also:**
- [README.md](../README.md)
- [CLI Test Harness Guide](cli-test-harness.md)
- [Upgrading from 0.11 to 1.0](../migration/upgrading-0.11-to-1.0.md)
