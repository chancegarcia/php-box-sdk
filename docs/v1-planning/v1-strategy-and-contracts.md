# V1.0 Strategy and Contracts Hardening
This document consolidates and hardens the v1.0 architecture, contracts, workflows, and strategies.

## 1. Current Planning Gap Summary

| Area | Status | Existing Source | Gap | Recommended Action |
|---|---|---|---|---|
| Glossary | Missing | N/A | No centralized terminology | Create v1 Glossary |
| Object Boundaries | Partial | `v1-architecture-rules.md` | Needs more detail on transport vs service | Explicitly define responsibilities |
| Service/Resource/Workflow Contracts | Partial | `v1-architecture-rules.md` | Specific return types and DTO roles | Define standard contract patterns |
| Value Objects/Enums | Partial | `v1-api-coverage-audit.md` | Missing explicit policy on primitives vs objects | Establish VO/Enum Policy |
| Auth Workflows | Missing | N/A | OAuth2 flow details and JWT/S2S planning | Document Auth Strategy |
| JWT/Server-to-Server Auth | Missing | N/A | Needs sequencing and feasibility notes | Add to Auth Strategy |
| Error Handling | Missing | N/A | No taxonomy or exception hierarchy | Define Error Strategy |
| Logging | Missing | N/A | PSR-3 usage and redaction rules | Define Logging Strategy |
| Observability | Missing | N/A | SDK vs downstream boundaries | Define Observability Strategy |
| Retry/Rate-Limit | Missing | N/A | Configuration and default behavior | Define Retry Strategy |
| Request/Response Abstraction | Partial | `v1-architecture-rules.md` | PSR compliance details | Define Request/Response Strategy |
| HTTP Client Strategy | Partial | `v1-architecture-rules.md` | Guzzle default justification | Document Guzzle/PSR-18 path |
| Public Direct Transport | Missing | N/A | Extension point for advanced users | Define Direct Transport Strategy |
| Lifecycle Management | Partial | `v1-api-coverage-audit.md` | Standard CRUD+ pattern | Define Resource Lifecycle Strategy |
| Testing | Partial | `v1-test-coverage-plan.md` | Needs transport/auth test specifics | Update Test Strategy section |
| Pagination | Partial | `v1-api-coverage-audit.md` | Offset/Marker/Auto-pagination details | Define Pagination Strategy |
| Hydration/Serialization | Partial | `v1-architecture-rules.md` | snake_case/camelCase and strictness | Define Serialization Strategy |
| Configuration | Missing | N/A | Immutable vs Mutable config | Define Configuration Strategy |
| Security | Missing | N/A | Secret handling and redaction rules | Define Security Strategy |
| Migration | Partial | `v1-interface-and-model-audit.md` | User-facing migration examples | Expand Migration Strategy |
| Endpoint Coverage | Partial | `v1-api-coverage-audit.md` | Priorities and ownership | Formalize Coverage Matrix |
| Public API Stability | Missing | N/A | Policy for v1.x post-release | Define Stability Policy |
| Documentation Drift | Missing | N/A | Strategy to prevent stale docs | Define Drift Check Policy |

## 2. v1 Glossary

- **Resource**: A passive object representing a Box entity (e.g., `File`, `User`). Contains only typed data and no logic for network calls or hydration.
- **Service**: A focused class (e.g., `FileService`) that performs operations against the Box API. The primary entry point for SDK users.
- **Workflow**: A sequence of operations handled by a service or higher-level helper (e.g., multi-step upload).
- **DTO (Data Transfer Object)**: A container for data moving between the user and the SDK (e.g., `CreateFolderRequest`, `FolderItemsResponse`).
- **Value Object**: An immutable object representing a domain concept with its own invariants (e.g., `BoxId`, `Email`).
- **Enum**: A PHP 8.1+ Enum for fixed API value sets (e.g., `CollaborationRole`).
- **Model**: Legacy term. In v1, replaced by **Resource** or **DTO**.
- **Hydrator**: An internal component that converts raw API data into Resources or DTOs.
- **Mapper**: A component that maps between different data shapes (e.g., snake_case to camelCase).
- **Transport**: The layer responsible for executing HTTP requests.
- **Public Transport API**: The stable interface/classes allowing users to execute raw requests through the SDK's configured transport.
- **Transport Escape Hatch**: Synonym for Direct Transport Usage.
- **Service Bypass**: Using the transport directly instead of a Service.
- **Advanced User API**: Features intended for users needing low-level control (Direct Transport).
- **Connection**: A legacy term often used for the combination of credentials and transport. In v1, preferred term is **Client** or **Transport**.
- **HTTP Client**: The underlying library (e.g., Guzzle) that implements PSR-18.
- **Request / Response**: The messages exchanged via HTTP. v1 prefers PSR-7 for these.
- **Token**: An authentication artifact (Access, Refresh).
- **Token Storage**: A contract for persisting and retrieving tokens.
- **Authentication Provider**: A component that manages the auth lifecycle (obtaining/refreshing tokens).
- **Retry Policy**: Rules governing automatic re-execution of failed requests.
- **Rate Limit**: Restrictions imposed by the Box API on request frequency.
- **Public SDK Contract**: The set of classes, interfaces, and methods guaranteed stable in v1.0.
- **Backward Compatibility**: Preservation of behavior and signatures across v1.x releases.
- **Documentation Drift**: When code and documentation become inconsistent.

## 3. Object Boundary Definition

### Client (Facade)
- **Responsibility**: High-level entry point; provides access to services; holds shared configuration.
- **Non-Responsibility**: Implementing business logic or individual API operations.

### Service
- **Responsibility**: Orchestrating API calls for a specific resource family; input validation (via DTOs); calling transport; handling hydration.
- **Non-Responsibility**: Managing raw HTTP sockets; persisting tokens.
- **Network Calls**: Yes, via Transport.
- **Expose Raw Transport**: No.

### Resource
- **Responsibility**: Holding typed data representing a Box entity.
- **Non-Responsibility**: Network calls; building URLs; hydration logic.
- **Network Calls**: No.

### DTO
- **Responsibility**: Carrying request parameters or response data.
- **Non-Responsibility**: Any logic beyond basic data consistency.

### Transport / Connection
- **Responsibility**: Executing PSR-7 requests; injecting auth headers (if configured); logging; retrying (if configured); mapping transport errors to SDK exceptions.
- **Non-Responsibility**: Resource-specific logic; hydration of high-level Resource objects.
- **Public API**: The interface and a default implementation are public. Internals (adapters) remain private.
- **Auth**: Handles auth headers automatically when created via Client.
- **Retry/Logging**: Applies automatically if configured.
- **Errors**: Normalizes to SDK Exceptions.
- **Returns**: SDK Response wrappers (providing access to raw PSR-7 response and basic metadata).

### Hydrator / Mapper
- **Responsibility**: Converting raw arrays/JSON to DTOs/Resources and vice versa.
- **Location**: Used primarily by Services.

### Token Storage / Auth Provider
- **Responsibility**: Managing credentials and token persistence.
- **Boundary**: SDK provides standard implementations (In-memory, PDO); users can provide their own.

## 4. Core Contract Definitions

### Client and Services
- Client provides a factory or getter for services (e.g., `$client->files()`).

### Services and Resources/DTOs
- Services return concrete Resources or DTOs.
- Creation/Update methods accept DTOs (e.g., `UpdateFileRequest`).

### Services and Transport
- Services use Transport to send requests.
- Services handle response status codes and call Hydrator to produce Resources.

### Direct Transport API (Advanced)
- **Purpose**: Provides an escape hatch for users needing to call endpoints not yet covered by high-level services, or requiring low-level HTTP control.
- **Audience**: Power users and contributors working on new API features.
- **Relation to Services**: Services use the same internal transport mechanism but encapsulate it. Direct transport bypasses service-level hydration and validation.
- **Usage Patterns**:
    - `send(RequestInterface $request)`: Accepts a pre-constructed PSR-7 `RequestInterface`.
    - `request(string $method, string $pathOrUri, array $options = [])`: Ergonomic wrapper for quick calls.
- **Expected Options for `request()`**:
    - `headers`: `array<string, string|string[]>`
    - `query`: `array<string, mixed>` (Appended to URI)
    - `json`: `mixed` (Encoded as JSON body)
    - `body`: `string|resource|StreamInterface` (Raw body)
    - `multipart`: `array` (For file uploads, if supported)
    - `auth`: `bool` (Whether to inject auth headers; default `true`)
    - `retry`: `bool|array` (Override default retry behavior for this request)
- **Warnings**: Using direct transport requires manual response handling and bypasses SDK-managed resource integrity. Users are responsible for parsing results and handling non-200 responses if they disable exceptions.
- **When to Use**:
    - New/beta Box API endpoints not yet in the SDK.
    - Custom request headers or non-standard query parameters.
    - Debugging raw API interactions.
- **When NOT to Use**:
    - When a high-level Service exists for the resource.
    - Standard CRUD operations.
- **Test Expectations**: Direct transport must be tested for header injection, option mapping, and error propagation.
- **Migration**: Replacing v0.10's raw `Request` objects and manual `Guzzle` calls with the standardized `$client->transport()` interface.

### Type Standards
- **IDs**: Always `string`.
- **Dates**: Always `DateTimeImmutable`.
- **Enums**: Used for roles, statuses, and fixed types.
- **Collections**: `Doctrine\Common\Collections\Collection` for list results.
- **Pagination**: Specific DTOs (e.g., `FolderItemsResponse`) containing entries and metadata.
- **Direct Transport Response**: Direct transport returns `BoxResponseInterface` (SDK wrapper). Access to raw PSR-7 response must be via explicit methods on the wrapper.
- **Retry Scope**: Retry behavior applies to both service calls and direct transport calls when enabled at the transport layer.

## 5. Value Object and Enum Policy

| Concept | Policy | Reasoning | Priority |
|---|---|---|---|
| Box IDs | **Primitive (string)** | Simple, used everywhere, avoids wrapper overhead. | Required v1.0 |
| Dates/Timestamps | **DateTimeImmutable** | Native PHP standard, immutable. | Required v1.0 |
| Access/Refresh Tokens| **DTO** | Multi-field (token, expiry, type). | Required v1.0 |
| Roles (Collab/User) | **Enum** | Fixed set of values defined by Box. | Required v1.0 |
| Resource Types | **Enum** | `file`, `folder`, `user`, etc. | Required v1.0 |
| SharedLink | **DTO** | Complex nested structure. | Preferred v1.0 |
| Permissions | **DTO** | Set of boolean flags. | Preferred v1.0 |
| Email | **Primitive (string)** | String validation usually sufficient. | Optional v1.0 |
| ETags | **Primitive (string)** | Opaque strings. | Optional v1.0 |

## 6. Authentication Workflow Strategy

### OAuth2 Workflow
1. **Auth URL Generation**: Helper in `AuthService` or `Client`.
2. **Code Exchange**: Service method taking `code`, returning `Token` DTO.
3. **Token Refresh**: Automatic refresh handled by `AuthProvider` during request execution.
4. **Token Storage**: `AuthProvider` calls `TokenStorage->save()` after refresh.

### JWT / Server-to-Server (S2S)
- **Status**: Targeted v1.0.0 requirement.
- **Sequencing**: A feasibility checkpoint will be performed after the core transport/auth-provider refactor.
- **Components**: `JwtAuthProvider` requiring private key, client ID, client secret, and enterprise ID.

### Direct Transport Auth
- **Default**: Auth headers injected automatically if Transport was created via Client.
- **Manual**: Users can provide a `Token` directly to a Transport instance if they bypass the Client.

## 7. Error Handling Strategy

### Exception Hierarchy
- `BoxException` (Base)
    - `ClientException` (Validation, Configuration)
    - `TransportException` (Network, Timeout)
    - `ApiException` (Box API errors: 4xx, 5xx)
        - `AuthenticationException` (401)
        - `AuthorizationException` (403)
        - `NotFoundException` (404)
        - `ConflictException` (409)
        - `RateLimitException` (429)
    - `RetryExhaustedException`

### Behavior
- Direct transport **throws** exceptions for unsuccessful HTTP responses unless configured otherwise.
- Exceptions contain the raw PSR-7 request and response (where available) for debugging.
- Redaction: Access tokens and secrets MUST NOT be included in exception messages or publicly accessible metadata.

## 8. Logging and Observability Strategy

### PSR-3 Logging
- SDK accepts `Psr\Log\LoggerInterface`.
- **Default**: `NullLogger`.
- **Levels**:
    - `DEBUG`: Raw request/response (redacted), hydration details.
    - `INFO`: Token refresh, retries.
    - `ERROR`: API errors, transport failures.

### Redaction Rules
- **Access/Refresh Tokens**: REMOVE from logs.
- **Client Secrets / Private Keys**: REMOVE from logs.
- **Request/Response Bodies**: Redact or truncate if they contain sensitive identifiers (e.g., file content).

### Observability Boundary
- SDK provides logs and exception context.
- SDK **does not** provide metrics (Prometheus, StatsD) or tracing (OpenTelemetry) out of the box to avoid dependency bloat, but allows user-injected HTTP middleware.

## 9. Retry and Rate-Limit Strategy

### Policy
- **Optional**: Disabled by default or configurable.
- **Scope**: Applied at the Transport layer.
- **Config**:
    - `max_attempts` (Default: 3)
    - `backoff_strategy` (Exponential)
    - `base_delay` (1000ms)
    - `retry_after`: Honors Box API's `Retry-After` header.

### Safe Retries
- Only retry GET, HEAD, and idempotent operations unless user explicitly allows (e.g., via `Idempotency-Key`).

## 10. Request/Response, HTTP Client, and Public Transport Strategy

### HTTP Strategy
- **Default**: Guzzle 7 (PSR-18 compliant).
- **Interoperability**: Any PSR-18 client can be injected.
- **Messages**: PSR-7 Request/Response objects.
- **Factories**: PSR-17.

### Response Strategy (v1)
- **Primary Internal Response**: PSR-7 `ResponseInterface`.
- **Public Response Wrapper**: A thin SDK wrapper around PSR-7.
- **Wrapper Responsibility**:
    - Provide access to raw PSR-7 response via `getPsrResponse()`.
    - Provide SDK-specific helpers for Box-specific metadata (e.g., `getRetryAfter()`).
    - Standardize common checks like `isSuccessful()`.
- **Naming**: `BoxResponseInterface` / `BoxResponse` in the `Box\Http` namespace.
- **Implementation**: The current implementation (which inherits from Symfony) will be **replaced** by a new, thin wrapper that strictly delegates to an internal PSR-7 response. Legacy Symfony inheritance and manual header parsing will be removed.
- **Avoid**:
    - Full PSR-7 mutation API (Responses are immutable in the SDK).
    - Symfony inheritance or dependencies.
    - Legacy response-header parsing unless internally justified for v1 compatibility.
- **Required Methods**:
    - `getPsrResponse()`: Returns the underlying PSR-7 response.
    - `getStatusCode()`: Delegates to PSR-7.
    - `isSuccessful()`: Boolean check (200-299).
    - `getHeaders()`: Delegates to PSR-7.
    - `getHeader(string $name)`: Delegates to PSR-7.
    - `getHeaderLine(string $name)`: Delegates to PSR-7.
    - `hasHeader(string $name)`: Delegates to PSR-7.
    - `getBody()`: Returns PSR-7 `StreamInterface`.
    - `getContent()`: Returns string body (convenience for `(string) $getBody()`).
    - `getRetryAfter()`: Returns `int|null` (parsed from `Retry-After` header).
    - `json(bool $assoc = true): mixed`: Helper to return decoded JSON body.
- **`json()` Helper Decision**:
    - **Purpose**: Ergonomics for direct transport users working with raw/custom endpoints.
    - **Hydration**: Does NOT replace service-level DTO/resource hydration.
    - **Signature**: `json(bool $assoc = true): mixed`.
    - **Error Behavior**: Invalid JSON MUST throw an `ApiException` (or specific `JsonDecodeException`) with the raw response context. Empty bodies return `null`.
- **Service Return Types**: Services MUST return Resources or DTOs, never raw response objects or wrappers.
- **Exception Context**: Exceptions store the PSR-7 response and may use the wrapper for parsing, but must redact sensitive data in string output.
- **Test Expectations**: Tests must cover successful JSON decoding, invalid JSON exceptions, empty body handling, and preservation of raw body access.

### Usage Layers
1. **Client/Facade**: `$client->files()->get($id)`.
2. **Service Usage**: `$service = new FileService($transport); $service->get($id)`.
3. **Direct Transport**: `$client->transport()->send($psr7Request)`. Returns the thin SDK response wrapper.

## 11. Resource Lifecycle Strategy

### Standard CRUD+ Pattern
- `get(string $id)`: Returns Resource.
- `create(CreateRequest $request)`: Returns Resource.
- `update(string $id, UpdateRequest $request)`: Returns Resource.
- `delete(string $id)`: Returns `void`.
- `list(PaginationParams $params)`: Returns PaginatedResponse DTO.

## 12. Endpoint and Resource Coverage Matrix

| Resource | v1.0 Priority | Owning Service |
|---|---|---|
| Files | High | `FileService` |
| Folders | High | `FolderService` |
| Users | High | `UserService` |
| Groups | High | `GroupService` |
| Collaborations | High | `CollaborationService` |
| Sign Requests | v1.1.0 | `SignRequestService` (New) |
| Webhooks | v1.1.0 | `WebhookService` (New) |
| Metadata | High | `MetadataService` (New) |

## 13. Pagination Strategy

- **Manual**: Users receive a DTO with `entries` and `next_marker` / `offset`.
- **Auto-pagination**: Optional feature in Services returning a `Generator` or `Iterator`.
- **Direct Transport**: User responsible for parsing pagination fields.

## 14. Serialization and Hydration Strategy

- **Mapping**: snake_case (API) <-> camelCase (SDK).
- **Strictness**: Ignore unknown fields by default; allow strict mode for tests.
- **Types**: Hydrator handles `DateTimeImmutable` and nested DTOs.

## 15. Configuration Strategy

- **Immutable**: Credentials, Base URI, Default HTTP Client (mostly).
- **Mutable**: Logger, Retry Policy (can be changed on a per-request basis via options).

## 16. Security and Secret Handling Strategy

### Redaction and Secrets
- **No Secrets in Logs**: Enforcement via automated tests.
- **Token Storage**: Encourages secure storage (encrypted DB, secure environment vars). Token storage implementations MUST NOT log the tokens themselves.
- **TLS**: Hard requirement for all API communication.

### Auth Provider and Token Storage Boundary
- **AuthProvider Responsibility**:
    - Managing the authentication lifecycle (OAuth2, JWT).
    - Handling token exchange (code -> token) and refresh.
    - Injecting auth headers into requests (delegated to Transport).
- **TokenStorage Responsibility**:
    - **Passive** persistence and retrieval of `Token` DTOs.
    - Must NOT contain logic for token refresh or exchange.
    - Must NOT make network calls.
- **Workflow**:
    1. `AuthProvider` checks if cached token is expired.
    2. If expired, `AuthProvider` performs refresh and calls `TokenStorage->save()`.
    3. If no token exists, `AuthProvider` triggers the initial flow or throws an exception.
- **Tests**: Providers must be tested for refresh triggers. Storage must be tested for persistence reliability.

## 17. Documentation Strategy

- **README**: High-level quickstart.
- **Migration Guide**: Transition from v0.x to v1.0.
- **Service Docs**: Detailed API coverage.
- **Advanced Docs**: Direct Transport and extension points.

## 18. Test Strategy

- **Unit**: Mocked Transport, testing hydration and service logic.
- **Integration**: Real HTTP calls against Box Sandbox (optional/manual).
- **Contract**: Ensuring PSR-18 compliance.

## 19. Migration and Backward Compatibility Strategy

- **v1.0**: Breaking change release.
- **v1.x**: SemVer compliance; no breaking changes.
- **Legacy Aliases**: Removed in v1.0.

### Migration Guide Requirements
The final migration guide MUST cover:
- **Client**: Transitioning from god-object `BoxClient` to the lightweight facade/service pattern.
- **Models**: Shift from `Model` terminology to `Resource` and `DTO`.
- **Transport**: Usage of the direct transport escape hatch.
- **Responses**: Changes from Symfony-based `BoxResponse` to the thin PSR-7 wrapper.
- **Exceptions**: The new hierarchy and how to access response metadata.
- **Auth**: Moving to `AuthProvider` and `TokenStorage` interfaces.
- **Retry/Logging**: New default behaviors and configuration options.
- **Namespaces**: Removal of all `Box\Model` and legacy transition aliases.
- **Data Types**: Strict enforcement of IDs (string), dates (`DateTimeImmutable`), and enums.
- **Arrays**: Removal of the 0.11 transition layer's broad associative array support in favor of typed objects.

## 20. Decision Records

1. **Facade over God-Object**: Client is a lightweight entry point.
2. **Strict Typing**: All signatures must be typed.
3. **Guzzle as Default**: Industry standard, PSR-18 compliant.
4. **Direct Transport Support**: Essential for API agility and power users.

## 21. Open Questions and Risks

- **JWT Timing**: Targeted v1.0.0; feasibility checkpoint after foundation.
- **Auto-pagination**: Complexity vs value for v1.0.
- **Upload Progress**: PSR-18 doesn't natively handle progress hooks well; deferred to v1.1.0 or earlier only if concrete Guzzle-specific requirements arise.
