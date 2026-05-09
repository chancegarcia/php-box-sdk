# Upgrading from v0.11 to v1.0

This guide covers the migration from the v0.11 compatibility layer to the refined v1.0 foundation.

## Foundation Refinement

The "Foundation Refinement" initiative has modernized the core SDK architecture to improve type safety, security, and PSR compliance.

### Response Wrapper
The `BoxResponse` class has been refactored to wrap a PSR-7 `ResponseInterface`. It no longer inherits from Symfony's `Response`.

- **Impact**: Low to Moderate (if you relied on Symfony-specific response methods).
- **Migration**:
    - Use `BoxResponseInterface` as the type hint for responses.
    - Use the new `json()` helper for decoding JSON bodies.
    - Use `getRetryAfter()` for rate-limit handling.
    - Access the underlying PSR-7 response via `getPsrResponse()` if needed.

### Exception Taxonomy
A refined exception hierarchy has been introduced for more granular error handling.

- **Impact**: Low.
- **Migration**:
    - Catch `Box\Exception\ApiException` instead of generic `BoxException` to access API-specific context.
    - Use `$e->getResponse()` on an `ApiException` to inspect the response.
    - Catch `Box\Exception\TransportException` for network-level failures.

### Security and Redaction
The SDK now automatically redacts sensitive data (access tokens, refresh tokens, client secrets) from exception messages, log context, and CLI output.

- **Impact**: None (Improved security).

### Transport Normalization
Response behavior is now consistent across Guzzle and Curl transports. Both return a `BoxResponseInterface`.

- **Impact**: None.

### Service Response Handling
Service methods (e.g., `$client->getFile($id)`) maintain their existing return types but now use the refined response helpers internally.

- **Impact**: None.

## Usage Comparison

### Exception Handling

**Before (v0.11 style):**
```php
try {
    $client->getFile($id);
} catch (\Box\Exception\BoxException $e) {
    // generic handling
}
```

**After (v1.0 Refined style):**
```php
try {
    $client->getFile($id);
} catch (\Box\Exception\ApiException $e) {
    $response = $e->getResponse();
    $errorData = $response->json();
    $boxCode = $errorData['code'] ?? 'unknown';
} catch (\Box\Exception\TransportException $e) {
    // handle network error
}
```

### Response Decoding

**Before (v0.11 style):**
```php
$response = $client->getFile($id);
$data = json_decode($response->getContent(), true);
```

**After (v1.0 Refined style):**
```php
$response = $client->getFile($id);
$data = $response->json();
```
