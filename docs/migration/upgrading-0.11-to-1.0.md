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
    - **Resource Migration**: All resource models (User, File, Folder, etc.) are now standalone classes and no longer extend legacy `Model`. They are now **passive state objects** (state-only) and no longer self-hydrate from the API, build their own URIs, or perform operations.
    - **Resource Construction**: Resource constructors no longer automatically hydrate from arrays by default in v1. Use `Box\Mapper\Hydrator` for mapping API data to resources.
    - **Compatibility Alias Removal**: True compatibility aliases and shims have been removed. For example, `Box\User\User` has been removed in favor of `Box\Resource\User`. The `Box\Model` namespace has been fully cleared of legacy shims.
    - **Service Response Parsing**: `Client::parseResponse()` now requires a valid `array` return. `BoxResponse::json()` has been hardened to return empty arrays/objects on invalid content to satisfy type hints.
    - **Factory Migration**: The legacy `AbstractFactory` and `FactoryException` have been removed. The legacy `Box\Connection\ConnectionFactory` has been removed. Use `Box\Factory\ConnectionFactory` (which implements `ConnectionFactoryInterface`) for creating connections. Infrastructure-related factory interfaces like `ConnectionFactoryInterface` remain as meaningful extension points.
    - **Token `toArray()`**: The `Token` class now implements `toArray()` for easier serialization of credentials.
    - **ModelMapper Cleanup**: `ModelMapper` no longer provides `mapBoxToClass`, `isInt`, or `removeEmpty`. Use `Hydrator` for mapping and PHP native functions or specialized helpers for other tasks.

### Factory Interface Rationalization
Redundant "one-class mirror" factory interfaces have been removed to simplify the SDK's internal structure while preserving the ability to inject concrete factories for testing.

- **Impact**: Moderate (Breaking change if you implement these interfaces).
- **Migration**:
    - **Removed Interfaces**:
        - `Box\Factory\FileFactoryInterface`
        - `Box\Factory\FolderFactoryInterface`
        - `Box\Factory\UserFactoryInterface`
        - `Box\Factory\GroupFactoryInterface`
        - `Box\Factory\CollaborationFactoryInterface`
    - **Update Type Hints**: Update type hints and constructor injections to use the concrete factory classes (e.g., `Box\Factory\FileFactory`) instead of the removed interfaces.
    - **Retained Interfaces**: Infrastructure-related factory interfaces have been retained as meaningful extension points:
        - `Box\Factory\ConnectionFactoryInterface`
        - `Box\Factory\TokenFactoryInterface`
        - `Box\Factory\AuthenticationResponseFactoryInterface`

### Resource Namespace and Interface Rationalization
Resource classes have been moved to the final `Box\Resource` namespace, and redundant mirror interfaces (e.g., `FileInterface`, `FolderInterface`) have been removed.

- **Impact**: Moderate (Breaking change).
- **Migration**:
    - **Namespace Updates**:
        - `Box\File\File` has been moved to `Box\Resource\File`.
        - `Box\Folder\Folder` has been moved to `Box\Resource\Folder`.
        - `Box\Group\Group` has been moved to `Box\Resource\Group`.
        - `Box\Collaboration\Collaboration` has been moved to `Box\Resource\Collaboration`.
        - `Box\Item\SharedLink\SharedLink` has been moved to `Box\Resource\SharedLink`.
        - `Box\Event\Event` has been moved to `Box\Resource\Event`.
        - `Box\Event\Admin\AdminEvent` has been moved to `Box\Resource\AdminEvent`.
        - `Box\Event\User\UserEvent` has been moved to `Box\Resource\UserEvent`.
    - **Interface Removal**: `Box\File\FileInterface`, `Box\Folder\FolderInterface`, `Box\Group\GroupInterface`, `Box\Collaboration\CollaborationInterface`, `SharedLinkInterface`, and `EventInterface` (including Admin/User variants) have been removed. Type hints should use the concrete resource classes.
    - **Doctrine Collections**: Entry sets in list responses (e.g., in `EventResponse`) now use Doctrine Collections as the standard container.
    - **Endpoint Constants**:
        - `FileInterface::URI` and `FileInterface::UPLOAD_URI` have been moved to `FileService` as `ENDPOINT` and `UPLOAD_ENDPOINT`.
        - `FolderInterface::URI` and `FolderInterface::SHARED_ITEM_URI` have been moved to `FolderService` as `ENDPOINT` and `SHARED_ITEM_ENDPOINT`.
        - `GroupInterface::URI` and `GroupInterface::MEMBERSHIP_URI` have been moved to `GroupService` as `ENDPOINT` and `MEMBERSHIP_ENDPOINT`.
        - `CollaborationInterface::URI` has been moved to `CollaborationService` as `ENDPOINT`.
        - `UserEventInterface::URI` has been moved to `UserEventService` as `ENDPOINT`.
    - **Service/Factory Returns**: `FileService`, `FolderService`, `GroupService`, `CollaborationService`, `FileFactory`, `FolderFactory`, `GroupFactory`, `CollaborationFactory`, and `Client` methods now return concrete resource classes (e.g., `Box\Resource\Folder`) instead of interfaces.
    - **FileService Signature**: `FileService::createSharedLink()` now requires a non-null `File` object. The previous nullable behavior was removed to ensure API safety.

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

### Event Retrieval

**Before (v0.11 style):**
```php
use Box\Service\Event\UserEventService;

$eventService = new UserEventService();
// ... setup ...
$events = $eventService->getEvents(); // returned array|stdClass|null
$nextPosition = $eventService->getLastResult()['next_stream_position'] ?? null;
```

**After (v1.0 Refined style):**
```php
use Box\Service\Event\UserEventService;

$eventService = new UserEventService();
// ... setup ...
$response = $eventService->getEvents(); // returns Box\Dto\Event\EventResponse

$events = $response->getEntries(); // Doctrine Collection of Event objects
$nextPosition = $response->getNextStreamPosition(); // string
```

### Model Hydration

**Before (v0.11 style):**
```php
use Box\Resource\User;

$user = new User();
$user->mapBoxToClass($data);
```

**After (v1.0 Refined style):**
```php
use Box\Mapper\Hydrator;
use Box\Resource\User;

$user = new User();
(new Hydrator())->hydrate($user, $data);

// OR via constructor (if the resource supports it)
$user = new User(['name' => 'John Doe']);
```

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

### Client Facade and Service Delegation
The `Client` class has been refactored into a thin facade over focused services. It no longer contains primary operation logic; instead, it delegates to specialized services via a `ClientServiceRegistry`.

- **Impact**: Moderate.
- **Migration**:
    - **Service Delegation**: Methods like `getFolder()`, `uploadFileToBox()`, and `getEvents()` now delegate to `FolderService`, `FileService`, and `UserEventService` respectively.
    - **Registry Usage**: Custom `Client` extensions should use `$this->serviceRegistry->get*Service()` to access underlying services.
    - **Narrowed Types**: Many `Client` methods have been narrowed to prefer concrete types (`Folder`, `File`) or specific scalar types over `mixed`. For example, `copyBoxFolder()` now requires `Folder` objects for both the original and parent arguments.
    - **Authenticated Service Boundary**: Services requiring an active session must implement `AuthenticatedServiceInterface`. The `Client` will throw a `RuntimeException` if an authenticated service is accessed without an available access token.

### Resource Purity
Resources in `Box\Resource` are now "pure" state objects. They do not own hydration logic, do not construct API URIs, and do not hold references to loggers or services.

- **Impact**: Moderate.
- **Migration**:
    - **URI Constants**: Endpoint URIs have moved from Resource classes/interfaces to their respective Services (e.g., `FileService::ENDPOINT`).
    - **Hydration**: Hydration is exclusively handled by `Hydrator` or Factories. In v1, services handle API response hydration internally, while factories manage the initial creation of resource objects from raw arrays where needed.
    - **Passive State**: Avoid adding logic or dependencies to resource classes; they should remain simple data containers with typed properties, getters, and setters.
    - **Doctrine Collections**: Resources themselves should not manage collections. Collections are returned by services or DTOs.

## Token Storage (Planned - Step 12)

Upcoming changes in Step 12 will introduce a formal Token Storage mechanism.

- **Impact**: Expected Moderate to High.
- **Planned Changes**:
    - Introduction of `TokenStorageInterface`.
    - Built-in support for Filesystem storage.
    - Decoupling of token persistence from the `Client` facade.
- **Migration**: Detailed notes will be provided upon completion of Step 12.
