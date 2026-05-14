# V1.0 Decision Index

This document tracks implementation-ready decisions, proposed strategies, and open questions for the V1.0 refactor.

## 1. Decision Status Summary

| Decision | Status | Source | Open Questions |
|---|---|---|---|
| Client facade over focused services | **Decided** | `v1-strategy-and-contracts.md` | None |
| Service-first architecture | **Decided** | `v1-strategy-and-contracts.md` | None |
| Resource vs DTO distinction | **Decided** | `v1-strategy-and-contracts.md` | None |
| Passive typed Resources | **Decided** | `v1-architecture-rules.md` | None |
| Resource getters must not mutate state | **Decided** | `v1-release-roadmap.md` | None |
| Factories hydrate user arrays | **Decided** | `v1-release-roadmap.md` | None |
| Services hydrate API responses | **Decided** | `v1-release-roadmap.md` | None |
| Services own URI construction | **Decided** | `v1-release-roadmap.md` | None |
| Authenticated service boundary | **Decided** | `11-v1-service-coverage-auth-boundary-audit.md` | None |
| Public direct transport API | **Decided** | `v1-strategy-and-contracts.md` | None |
| Guzzle 7 as default PSR-18 client | **Decided** | `v1-strategy-and-contracts.md` | None |
| IDs as `string` | **Decided** | `v1-architecture-rules.md` | None |
| Dates as `DateTimeImmutable` | **Decided** | `v1-architecture-rules.md` | None |
| PSR-3 Logging with Redaction | **Decided** | `v1-strategy-and-contracts.md` | Implementation details |
| Retry disabled by default | **Decided** | `v1-architecture-rules.md` | None |
| Retry scope (Transport layer) | **Decided** | `v1-strategy-and-contracts.md` | None |
| Exception taxonomy | **Decided** | `v1-architecture-rules.md` | None |
| `json()` Helper Decision | **Decided** | `v1-strategy-and-contracts.md` | None |
| Auth Provider / Token Storage Boundary | **Decided** | `v1-strategy-and-contracts.md` | None |
| Token Storage Core Scope | **Decided** | `v1-strategy-and-contracts.md` | None |
| Token Storage Context Strategy | **Decided** | `v1-strategy-and-contracts.md` | None |
| PDO Schema Management | **Decided** | `v1-strategy-and-contracts.md` | None |
| Encryption at Rest | **Decided** | `v1-strategy-and-contracts.md` | None |
| Multi-Token Support | **Decided** | `v1-strategy-and-contracts.md` | None |
| Doctrine ORM Storage Deferral | **Decided** | `future-symfony-bundle.md` | None |
| Doctrine Collections for Lists | **Decided** | `v1-architecture-rules.md` | None |
| Migration Documentation Requirements | **Decided** | `v1-strategy-and-contracts.md` | None |
| Documentation Gap Inventory | **Active** | `v1-documentation-gap-inventory.md` | Tracks ongoing P2/P3 items |
| SDK Response Wrapper | **Decided** | `v1-strategy-and-contracts.md` | Implementation: Replace with thin PSR-7 wrapper |
| Response Strategy Alignment | **Decided** | `v1-strategy-and-contracts.md` | None |
| JWT/S2S Auth Required | **Decided** | `v1-release-roadmap.md` | Required for v1 release |
| Token Storage Integration Review | **Decided** | `v1-release-roadmap.md` | Client and CLI integration required |
| API Coverage Depth vs Parity | **Decided** | `v1-release-roadmap.md` | Prioritize core resource value for v1 |
| Webhook Verification Required | **Decided** | `v1-release-roadmap.md` | Security requirement for v1 |
| Webhook Management (CRUD) | **Decided** | `v1-release-roadmap.md` | Deferred to post-v1; direct transport is the escape hatch |
| Comments / Tasks / Metadata | **Evaluation** | `v1-release-roadmap.md` | Evaluate for v1 or defer |
| CLI Persistence | **Decided** | `strategy-and-contracts.md` | Optional/Configurable; Resolution order defined |
| Sign Requests | **Deferred** | `v1-strategy-and-contracts.md` | v1.1.0 priority |
| Auto-pagination | **Deferred** | `v1-strategy-and-contracts.md` | v1.x candidate |
| Upload Progress | **Deferred** | `v1-strategy-and-contracts.md` | v1.1.0 candidate |

## 2. Refined Decisions

### Retry Defaults and Scope
- **Decision**: Automatic retry is **disabled by default**.
- **Scope**: Retry is implemented at the Transport layer and applies to both Service calls and Direct Transport calls when enabled.
- **Rules**: Only safe/idempotent methods (GET, HEAD) are retried by default unless explicitly configured otherwise.

### Direct Transport Public Contract
- **Status**: Supported advanced public API / escape hatch.
- **Decision**: Support both PSR-oriented and ergonomic methods.
    - `$client->transport()->send(RequestInterface $request)` (Pure PSR-7)
    - `$client->transport()->request(string $method, string $pathOrUri, array $options = [])` (SDK convenience)
- **Response**: Returns a thin SDK wrapper around PSR-7.
- **Decision**: The wrapper MUST provide `getPsrResponse()` for raw access and SDK helpers for metadata (`Retry-After`, success checks).
- **Naming**: `BoxResponseInterface` / `BoxResponse` in the `Box\Http` namespace.
- **Methods**:
    - `getPsrResponse()`
    - `getStatusCode()`
    - `isSuccessful()`
    - `getHeaders()`
    - `getHeader(string $name)`
    - `getHeaderLine(string $name)`
    - `hasHeader(string $name)`
    - `getBody()`
    - `getContent()`
    - `getRetryAfter()`
- **Service Boundary**: Services MUST return Resources/DTOs, not the response wrapper.

### Response Strategy and PSR-7 Alignment
- **Decision**: Internal transport boundaries use PSR-7 `ResponseInterface`.
- **Decision**: `BoxResponseInterface` remains as the public wrapper contract but is simplified.
- **Decision**: Services must not return response objects; they return Resources or DTOs.
- **Naming**: Keep `BoxResponseInterface` / `BoxResponse` naming for the public SDK response wrapper.
- **Method Design**: Keep the SDK response wrapper minimal and PSR-oriented.
- **Legacy Compatibility**: `BoxResponse` will be **replaced** by a new implementation that wraps PSR-7 and removes Symfony inheritance. Legacy methods like `isOk`, `isForbidden`, etc., will be removed in favor of `getStatusCode()` or simplified helpers like `isSuccessful()`.
- **Decision**: `json(bool $assoc = true)` is a first-class helper method on the wrapper for ergonomic direct transport usage. It MUST throw a `JsonDecodeException` on invalid JSON.

### Upload Progress Abstraction
- **Decision**: **Deferred**.
- **Target**: v1.1.0 candidate or earlier only if a concrete Guzzle-specific requirement arises.
- **Rationale**: PSR-18 does not standardize progress hooks. Adding a generic abstraction before a concrete requirement risks over-designing the transport layer.
- **v1.0.0 Impact**: Not a blocker.

### Exception Metadata and Redaction
- **Decision**: Exceptions may contain raw PSR-7 messages for debugging.
- **Redaction**: Access tokens, refresh tokens, client secrets, and authorization codes MUST be redacted from exception messages and log output. Raw objects remain available for advanced debugging but users should be warned about sensitivity.

### Auth Provider and Token Storage Boundary
- **Decision**: `AuthProvider` manages lifecycle (refresh, exchange, injection); `TokenStorage` is a **passive** data store only.
- **Requirement**: `TokenStorage` MUST NOT make network calls or contain refresh logic.
- **Orchestration**: The `Client` (or a dedicated orchestrator service) coordinates between `AuthProvider` and `TokenStorage` to load, refresh, and persist tokens.
- **Independence**: Services MUST NOT depend on `TokenStorage`.

### Error Taxonomy
- **Decision**: Comprehensive hierarchy based on `BoxException` defined in `v1-architecture-rules.md`.
- **API Errors**: Specialized exceptions for 401, 403, 404, 409, 429.
- **Internal Errors**: Specialized exceptions for JSON decode, Hydration, Token Storage, and Retry Exhaustion.
- **Redaction**: All exceptions MUST redact secrets from string/log output.

### Migration Documentation Requirements
- **Decision**: The migration guide must explicitly cover the shift to facade/services, passive resources, direct transport, the new response wrapper, and the exception hierarchy.

### JWT/S2S Auth Strategy
- **Decision**: JWT/S2S is **REQUIRED** for v1.0.0.
- **Scope**: Includes JWT configuration, assertion generation, token exchange, and integration with `Client` and `Connection`.
- **CLI**: CLI/harness support must be evaluated or implemented during Step 15.

### Endpoint Priority (Sign Requests and Webhooks)
- **Decision**: Sign Requests are v1.1.0 items.
- **Decision**: Webhook signature **verification** is implemented in v1.0.0 via `WebhookVerifier` (`src/Webhook/`).
- **Decision**: Webhook **management** (create, list, delete webhooks via the Box API) is deferred to post-v1. Direct transport is the escape hatch for consumers who need it before then.
- **Rationale**: Verification is a security requirement with well-defined scope. CRUD management adds a full service layer without clear v1 demand.

### Token Storage Context

- **Decision**: v1 token storage uses a small `TokenStorageContext` DTO/value object or equivalent explicit context object.
- **Rationale**: Context must be consistent across storage implementations and framework-neutral.
- **v1.0 Scope**: Context identifies logical token owner/scope, such as default, CLI profile, user, enterprise, tenant, or integration.

### PDO Schema Management

- **Decision**: v1 core SDK documents required PDO token storage schema and may provide schema helper SQL, but does not own framework migrations.
- **Rationale**: PDO storage remains framework-neutral and predictable.

### Encryption at Rest

- **Decision**: v1 core SDK does not implement encryption at rest directly for PDO token storage.
- **Decision**: v1 docs must clearly state that access tokens, refresh tokens, client secrets, JWT private keys, and related auth material are secrets and should be protected by the consuming application.
- **Rationale**: Encryption and key management are application/infrastructure-specific.

### Multi-Token Support

- **Decision**: v1 core SDK token storage supports multiple storage contexts, with one active token record per context.
- **Decision**: Broad multi-token-per-context support is deferred.
- **Rationale**: Multi-context storage supports real-world SDK usage without forcing application-specific token selection policy into the core SDK. Multiple active tokens per context create token selection, refresh, revocation, schema, and security complexity better suited to future Symfony/Doctrine integration work.
- **v1.0 Scope**: In-memory and PDO storage support context-aware persistence with one active token per context.
- **Deferred Scope**: Token history, multiple active grants per context, token labels/profiles, active/default selection, and Doctrine-backed multi-token models.

### CLI Token Storage and Auth Behavior

- **Decision**: CLI token storage is **optional and configurable**. The CLI MUST NOT globally require token storage to be configured or present.
- **Decision**: Resource-related CLI commands (e.g., `file:get`) follow a specific auth resolution priority:
    1. **Configured Storage**: Use configured token storage and context if available.
    2. **Explicit Input**: Use explicit auth input (e.g., `--token` option), environment variables, or config file paths if supported.
    3. **Graceful Failure**: Fail with clear, actionable error message if no auth source is available.
- **Decision**: Auth exchange commands may optionally persist tokens to storage if configured or explicitly requested.
- **Decision**: Auth refresh commands must persist refreshed tokens to storage if storage is configured.
- **Pending (Step 12)**: Specific CLI storage backend/configuration mechanism (options vs config provider).
- **Pending (Step 12)**: CLI storage context selection policy.
- **Decision (Step 12/15)**: **Deferred JWT/S2S CLI configuration note**. When JWT/S2S auth is implemented, evaluate whether the CLI/auth harness should support separate environment-variable groups or named auth profiles for OAuth2 versus JWT credentials. This would allow CLI testing of JWT and OAuth2 without manually swapping shared `BOX_CLIENT_ID` / `BOX_CLIENT_SECRET` values and reduce the risk of mismatched credential pairs or accidentally combining OAuth2 and JWT configuration. Do not implement this during Step 12 token storage unless a later approved plan explicitly includes CLI auth profile work.

## 3. Open Questions for Human Review

None at this stage. Questions resolved in v0.11 planning pass.

## 4. Implementation Sequencing

1. **Foundation Refinement**: Transport refactor, Auth provider boundaries, Exception hierarchy, Logging/Redaction foundation.
2. **Core Services**: Files, Folders, Users, Groups, Collaborations.
3. **Pagination and Collections**: Standardize response DTOs and Doctrine collection usage.
4. **Direct Transport**: Finalize public API and documentation.
5. **Auth Expansion**: JWT/S2S implementation (if decided for 1.0.0).
6. **Feature Expansion**: Webhooks, Sign Requests, Metadata (if in scope for 1.0.0).
7. **Migration Docs**: Complete guide and examples.
8. **V1.0.0 Release Readiness**: Package rename, final audit, tag.
