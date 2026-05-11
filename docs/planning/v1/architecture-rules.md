# V1.0 Architecture Rules

These rules define the required structure for the Box PHP SDK V1.0 refactor.

## 1. Namespace and File Structure

- **Box\Resource**: Concrete API resource objects (e.g., `File`, `Folder`, `User`).
- **Box\Dto**: Request, response, and value DTOs (e.g., `FileUploadRequest`, `PaginationParams`).
- **Box\Enum**: PHP 8.1+ Enums for fixed API values (e.g., `BoxItemType`, `CollaborationRole`).
- **Box\Service**: Focused services for API operations (e.g., `FileService`, `UserService`).
- **Box\Http**: Raw HTTP concerns, clients, and response handling.
- **Box\Contract**: True extension contracts (Interfaces) for user-replaceable services.
- **Box\Trait**: Reusable logic traits.
- **Box\Base**: Base classes that provide common functionality but are not god-abstractions.

## 2. Interface Rules

- **Allowed**: For real extension boundaries (HTTP transport, hydration, token storage, factories, etc.).
- **Forbidden**: Mirroring a single concrete resource model's getters/setters/constants.
- **Factories**: Per-resource factory interfaces should be removed unless a specific public extension-point need is discovered.
- **Removal**: All interfaces under `src/Model` or resource namespaces that simply duplicate the model's public API must be removed.

## 3. Resource Model Rules

- **Location**: `Box\Resource`.
- **Logic**: Resources must NOT build URLs, perform HTTP calls, call services, or own hydration logic.
- **Construction**: Resource constructors should be passive and should NOT perform auto-hydration from arrays by default in v1.
- **Endpoints**: Endpoint URI constants must NOT live in resource models or model interfaces.
- **Types**:
    - IDs must be `string` typed.
    - Dates must use `DateTimeImmutable`.
    - Nested resources must be object-only (no raw arrays).
- **Setters**: Must NOT be fluent. Return `void`.
- **Collections**: Use Doctrine Collections selectively.
- **Payloads**: Raw Box API payloads should not be stored on resources by default.

## 4. Service Rules

- **Location**: `Box\Service`.
- **Responsibility**: Services are the primary way to interact with the Box API.
- **Registry**: Services should be accessed through the `ClientServiceRegistry` and configured with appropriate connection/auth state.
- **Authenticated Boundary**: Services requiring an active session must implement `AuthenticatedServiceInterface`.
- **Input**: Use scalar IDs, request DTOs, enums, or value objects.
- **Output**: Return concrete resource classes or DTOs.
- **Facade**: The `Client` class should be a lightweight facade over these focused services.

## 5. DTO Rules

- **Location**: `Box\Dto`.
- **Logic**: Minimal logic, primarily data containers.
- **SharedLink**: Model as a DTO/value object, not a top-level Resource initially.
- **Setters**: Return `void`. No fluent chains.

## 6. General Rules

- **PHP Version**: PHP 8.4+.
- **Strict Typing**: Mandatory for parameters, return values, and properties.
- **Metadata**: Use typed DTO envelopes for Box-defined metadata structure while allowing custom template values as `array<string, mixed>`.
- **Hydration**: Centralized in `Box\Mapper` or `Box\Http\Hydrator`, not in the models themselves. Resource construction should be handled by hydrator/mapper boundaries.

### Retry and Rate-Limit Behavior
- **Default**: Disabled.
- **Scope**: Applied at the Transport layer (middleware/decorator).
- **Applicability**: Applies to both Service and Direct Transport calls.
- **Safe Retries**: Only retry GET, HEAD, and idempotent operations (with `Idempotency-Key`) by default.
- **Opt-in**: Non-idempotent retries require explicit opt-in via request options.
- **Retry-After**: Transport MUST honor Box API's `Retry-After` header.
- **Exhaustion**: Throws `RetryExhaustedException` when max attempts are reached.
- **Metadata**: Exceptions and response wrappers MUST expose retry count and last response.

### Logging and Redaction Policy
- **Logger**: PSR-3 `Psr\Log\LoggerInterface` (Default: `NullLogger`).
- **Events**:
    - Request/Response execution (redacted).
    - Auth token refresh.
    - Retry attempts.
    - Hydration/Mapping failures (DEBUG).
- **Redaction Policy**:
    - **Tokens**: `access_token`, `refresh_token`, `Authorization` header, and any secret strings MUST be replaced with `[REDACTED]`.
    - **Bodies**: Request/Response bodies should be truncated or redacted if they exceed size limits or contain sensitive file content.
    - **Exceptions**: Exception messages and stack traces MUST NOT leak secrets.
- **Tests**: Logging must be verified with a test logger to ensure redaction rules are active.

### Error Taxonomy
- **Base**: `BoxException`.
- **Validation**: `ClientException` (Pre-request validation, invalid config).
- **Transport**: `TransportException` (Network errors, timeouts).
- **API (4xx/5xx)**: `ApiException`.
    - `AuthenticationException` (401 - Token expired or invalid).
    - `AuthorizationException` (403 - Insufficient permissions).
    - `NotFoundException` (404).
    - `ConflictException` (409).
    - `RateLimitException` (429 - Should be handled by retry if enabled).
- **Specialized**:
    - `JsonDecodeException` (Invalid response body).
    - `HydrationException` (Mapping failure).
    - `TokenStorageException` (Persistence failure).
    - `RetryExhaustedException` (Max attempts reached).
- **Context**: All API exceptions MUST provide access to the raw PSR-7 Request and Response (redacted).
- **Direct Transport**: Direct transport throws these exceptions by default, maintaining consistency with Services.

## 7. Collections

V1.0 should use Doctrine Collections selectively, not universally.

Doctrine Collections are appropriate for list response entry sets where in-memory traversal, filtering, mapping, and matching are useful. They do not require Doctrine ORM or database mapping.

Services should generally return specific response DTOs that contain a Doctrine Collection of entries plus typed pagination metadata.

Examples:

- `FolderItemsResponse`
- `GroupMembershipListResponse`
- `FileVersionListResponse`
- `CollaborationListResponse`
- `EventListResponse`

Avoid one generic public collection response DTO unless it remains narrow and internal.

Do not force small value-object arrays, enum lists, permission flags, or dynamic metadata values into Doctrine Collections without a clear benefit.

Collection filtering is in-memory only and should not be presented as a replacement for Box API search or server-side filtering.

## 8. HTTP Transport and Connection

- **Role**: Transport executes raw PSR-7 requests and returns SDK response wrappers.
- **Public API**: Direct transport usage is a supported advanced public API / escape hatch for uncovered endpoints.
- **Interface**: `TransportInterface` defines the core execution contract.
- **Methods**: Supports both `send(RequestInterface $request)` (PSR-oriented) and `request(string $method, string $pathOrUri, array $options = [])` (ergonomic).
- **Response Wrapper**: `BoxResponseInterface` / `BoxResponse` in the `Box\Http` namespace. It provides access to the raw PSR-7 response via `getPsrResponse()`, and includes helpers for `Retry-After`, success checks, status codes, and headers. The v1 implementation is a thin PSR-7-backed wrapper, not a Symfony-inherited object.
- **Service Return Types**: Services MUST return Resources or DTOs, not the response wrapper.
- **Default Client**: Guzzle 7 is the default PSR-18 implementation.

## 9. Raw API Payload Debugging

Resources should not store raw Box API payloads by default.

The SDK may support optional raw payload capture at the hydrator/mapper layer for debugging and migration diagnostics. This feature must be disabled by default.

Preferred implementation is a separate debug payload store, potentially backed by `WeakMap<object, array>`, so resources remain clean typed objects.

Raw payloads must not be logged, serialized, or exposed as the primary way to access resource fields. They must not be treated as the primary SDK API.