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

```php
// Token Refresh Example
if ($token->isExpired()) {
    $newToken = $client->refreshToken();
    $this->tokenRepo->save($newToken);
}
```

For more details on these changes, see the [v1 Upgrade Guide](../migration/upgrading-0.11-to-1.0.md).

## 5. Response and Error Model

The SDK uses `Box\Http\BoxResponseInterface` for all API responses and throws `Box\Exception\BoxException` (or more specific subclasses) when things go wrong.

### Exception Taxonomy
The SDK uses a refined exception hierarchy for better error handling:
- `BoxException`: Base exception for all SDK errors.
- `TransportException`: Thrown when a network or connection error occurs (e.g., timeout, DNS failure).
- `BoxResponseException`: Base for exceptions that include a `BoxResponseInterface`.
- `ApiException`: Thrown when the Box API returns an error (4xx or 5xx status codes). Subclasses like `UnauthorizedException` (401) or `NotFoundException` (404) are available for common errors.

### Robust Error Handling
An `ApiException` contains a `BoxResponseInterface` (via `$e->getResponse()`). This allows you to inspect the JSON payload for specific Box error codes and response headers.

**Key Security Feature:** The SDK automatically sanitizes sensitive data (access tokens, refresh tokens, client secrets) from exception messages and context information using its internal `Redactor`.

### Auth Aliases and Exchange
In v0.11, `exchangeAuthorizationCodeForToken()` is preferred for the OAuth2 code exchange step for better clarity.

```php
$client->setAuthorizationCode($_GET['code']);
$token = $client->exchangeAuthorizationCodeForToken();
// Equivalent to: $token = $client->getAccessToken();
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

## 7. Model Validation and Box IDs

The SDK models use a shared trait-based validation system. In v0.11.x, Box IDs are consistently handled as `string|int` to ensure compatibility with both numeric and string-based IDs used by Box.

### Data Types
- **IDs:** Use `string` or `int`. The SDK will normalize these when communicating with the API.
- **Models:** Models are populated via arrays but can be validated for required fields.

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
Models use `Box\Model\BaseModelTrait` and `Box\Model\ModelTrait` for shared logic. Validation in v0.11.x avoids side effects during class verification, ensuring that simply instantiating or checking a model doesn't trigger unexpected state changes.

---

**See also:**
- [README.md](../README.md)
- [CLI Test Harness Guide](cli-test-harness.md)
- [Project Roadmap](../planning/roadmap.md)
- [v1.0 Planning](../planning/v1/overview.md)
