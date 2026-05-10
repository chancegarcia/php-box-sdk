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

### Service Layer Hardening
The service layer has been hardened to provide more consistent hydration, better error handling, and clearer boundaries between raw API data and typed resources.

- **Impact**: Low to Moderate (if you relied on stateful service properties).
- **Migration**:
    - **Prefer Hardened Helpers**: Use `$service->getResourceFromBox()` or `$service->sendUpdateAndHydrate()` in custom service extensions.
    - **Avoid Stateful Debugging**: The `getLastResult()`, `getDefaultReturnType()`, and `setDefaultReturnType()` methods have been removed. Instead, catch `ApiException` and use `$e->getResponse()` to inspect the failed response.
    - **Service State Removal**: Stateful properties like `lastResultOriginal`, `lastResultDecoded`, and `lastResultFlat` have been removed. Services now rely on explicit return values.

### Model Trait and Mapping Infrastructure Removal
The legacy `BaseModelTrait` and `ModelTrait` have been removed. Mapping infrastructure is now centralized in `Box\Mapper\Hydrator` and `Box\Mapper\ModelMapper`.

- **Impact**: Moderate to High (Breaking change).
- **Migration**:
    - **Removed Traits**: `BaseModelTrait` and `ModelTrait` are gone.
    - **Removed Interface Methods**: `toClassVar`, `toBoxVar`, `mapBoxToClass`, `isInt`, `removeEmpty` have been removed from `BaseModelInterface`. `toBoxArray` and `buildQuery` have been removed from `ModelInterface`.
    - **Removed Class Methods**: `BaseModel` and `Model` no longer provide `toClassVar`, `toBoxVar`, `isInt`, `removeEmpty`, `classArray`, or `buildQuery`.
    - **Prefer `toArray()`**: Models now support `toArray()` for obtaining an associative array representation.
    - **Prefer `Hydrator::hydrate()`**: Use `(new Hydrator())->hydrate($model, $data)` instead of `$model->mapBoxToClass($data)`. The `mapBoxToClass` and `toArray` methods on legacy `Model` have been removed along with the `Box\Model` base architecture.
    - **Removed `Box\Model` Architecture**: `BaseModel`, `Model`, `BoxModel`, and their respective interfaces have been removed. Infrastructure and resource models no longer inherit from these legacy bases.
    - **Infrastructure Migration**: `Service` and `Connection` hierarchies have been decoupled from legacy model bases. Both now implement `LoggerAwareInterface` directly to preserve logging capabilities.
    - **Resource Migration**: All resource models (User, File, Folder, etc.) are now standalone classes and no longer extend legacy `Model`. They now include constructors that accept options for automatic hydration via `Hydrator`.
    - **Compatibility Alias Removal**: True compatibility aliases and shims have been removed. For example, `Box\User\User` has been removed in favor of `Box\Resource\User`. The `Box\Model` namespace has been fully cleared of legacy shims.
    - **Service Response Parsing**: `Client::parseResponse()` now requires a valid `array` return. `BoxResponse::json()` has been hardened to return empty arrays/objects on invalid content to satisfy type hints.
    - **Factory Migration**: `AbstractFactory::get()` now returns `object` instead of `ModelInterface` and no longer enforces inheritance from legacy model interfaces.
    - **Token `toArray()`**: The `Token` class now implements `toArray()` for easier serialization of credentials.
    - **ModelMapper Cleanup**: `ModelMapper` no longer provides `mapBoxToClass`, `isInt`, or `removeEmpty`. Use `Hydrator` for mapping and PHP native functions or specialized helpers for other tasks.

### Event Service Overhaul
The `UserEventService` has been modernized to return a typed `EventResponse` DTO instead of raw associative arrays or relying on `getLastResult()`. Response mapping is now handled by `EventResponseMapper`. `EventResponse` returns a defensive copy of its entries to maintain immutability. `next_stream_position` is now represented as a `string` to safely handle large cursor values.

- **Impact**: Moderate (Breaking change).
- **Migration**:
    - `getEvents()` now returns `Box\Dto\Event\EventResponse`.
    - Access entries via `$response->getEntries()` which returns a Doctrine `Collection` of `Event` objects.
    - Access pagination data via `$response->getNextStreamPosition()` and `$response->getChunkSize()`.
    - Default `stream_position` is now `now` (API default) instead of `0`.
    - Parameter `type` and optional `EventCollectionInterface` in `getEvents()` have been removed.
    - Chained/fluent setters on `UserEventService` have been removed; setters now return `void`.

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
