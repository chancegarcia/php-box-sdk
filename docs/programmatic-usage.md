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
The SDK provides a `Box\Model\Connection\Token\Token` model. Your application is responsible for:
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

## 5. Response and Error Model

The SDK uses `Box\Http\Response\BoxResponseInterface` for all API responses and throws `Box\Exception\BoxException` when things go wrong.

### Inspection
Always check the HTTP status code. Successful operations usually return `200`, `201`, or `204`.

### Error Handling
A `BoxException` may contain a `BoxResponseInterface` if the error originated from the Box API. This allows you to inspect the JSON payload for specific Box error codes.

```php
try {
    $response = $client->uploadFileToBox($path);
} catch (Box\Exception\BoxException $e) {
    $apiResponse = $e->getBoxResponse();
    if ($apiResponse && $apiResponse->getStatusCode() === 409) {
        // Handle name collision
    }
    throw $e;
}
```

## 6. File Upload Integration

Uploads require a local file path and optionally a target folder ID (defaults to `0` for the root folder).

- **Validation:** Validate that the local file exists and is readable *before* calling the SDK.
- **Targeting:** Treat folder IDs as configuration or dynamic metadata within your app. Do not hardcode them.

## 7. Logging and Observability

The SDK implements `Psr\Log\LoggerAwareInterface`. It is highly recommended to inject your application's PSR-3 compliant logger (e.g., Monolog).

- **Library Style:** The SDK logs internal transitions, API requests (at debug level), and errors using PSR-3 conventions and structured context.
- **Propagation:** Once a logger is set on the `Client`, it is automatically propagated to all objects created by the client, such as `Connection`, `Folder`, and `File` models.
- **Host Integration:** In a Symfony or Laravel app, the container should automatically inject the main application logger into the `Client` service.

## 8. Extension and Composition Patterns

To maintain a clean boundary:
- **Factories:** Use a factory to create the `Client` with the correct `Logger` and base configuration.
- **Adapters:** If your application supports multiple storage backends, implement a `StorageInterface` and create a `BoxStorageAdapter` that wraps the SDK.

## 9. Best Practices

- **Security:** Never commit `BOX_CLIENT_SECRET` or any tokens to source control.
- **Infrastructure:** Treat the SDK as infrastructure. Your business logic shouldn't know how Box handles OAuth2; it should only care about "storing a file."
- **Testing:** Use the SDK's interfaces to mock the `Client` in your unit tests.

## 10. Minimal Examples

### Service Wrapper
```php
use Box\Client;
use Box\Model\Connection\Token\Token;

class BoxIntegration
{
    private Client $client;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->client = new Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
    }

    public function setToken(array $tokenData): void
    {
        $this->client->setToken(new Token($tokenData));
    }

    public function upload(string $path, string $folderId = '0')
    {
        return $this->client->uploadFileToBox($path, $folderId);
    }
}
```

### Response Inspection
```php
/** @var \Box\Http\Response\BoxResponseInterface $response */
$status = $response->getStatusCode();
$data = json_decode($response->getContent(), true);

if ($status === 201) {
    echo "File uploaded: " . $data['entries'][0]['name'];
}
```

---

**See also:**
- [README.md](../README.md)
- [CLI Test Harness Guide](cli-test-harness.md)
- [Project Roadmap](roadmap.md)
