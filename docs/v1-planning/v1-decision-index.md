# V1.0 Decision Index

This document tracks implementation-ready decisions, proposed strategies, and open questions for the V1.0 refactor.

## 1. Decision Status Summary

| Decision | Status | Source | Open Questions |
|---|---|---|---|
| Client facade over focused services | **Decided** | `v1-strategy-and-contracts.md` | None |
| Service-first architecture | **Decided** | `v1-strategy-and-contracts.md` | None |
| Resource vs DTO distinction | **Decided** | `v1-strategy-and-contracts.md` | None |
| Passive typed Resources | **Decided** | `v1-architecture-rules.md` | None |
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
| Migration Documentation Requirements | **Decided** | `v1-strategy-and-contracts.md` | None |
| Documentation Gap Inventory | **Active** | `v1-documentation-gap-inventory.md` | Tracks ongoing P2/P3 items |
| SDK Response Wrapper | **Decided** | `v1-strategy-and-contracts.md` | Implementation: Replace with thin PSR-7 wrapper |
| Response Strategy Alignment | **Decided** | `v1-strategy-and-contracts.md` | None |
| JWT/S2S Auth Timing | **Decided** | `v1-strategy-and-contracts.md` | Feasibility checkpoint after foundation |
| Sign Requests / Webhooks | **Decided** | `v1-strategy-and-contracts.md` | v1.1.0 priority |
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

### Error Taxonomy
- **Decision**: Comprehensive hierarchy based on `BoxException` defined in `v1-architecture-rules.md`.
- **API Errors**: Specialized exceptions for 401, 403, 404, 409, 429.
- **Internal Errors**: Specialized exceptions for JSON decode, Hydration, Token Storage, and Retry Exhaustion.
- **Redaction**: All exceptions MUST redact secrets from string/log output.

### Migration Documentation Requirements
- **Decision**: The migration guide must explicitly cover the shift to facade/services, passive resources, direct transport, the new response wrapper, and the exception hierarchy.

### JWT/S2S Auth Sequencing
- **Decision**: JWT/S2S is a targeted v1.0.0 requirement.
- **Checkpoint**: A feasibility checkpoint will be performed after the core transport/auth-provider boundaries are established.
- **Fallback**: Fallback to v1.1.0 only with explicit rationale if implementation discovery shows material risk to core architecture or release stability.

### Endpoint Priority (Sign Requests and Webhooks)
- **Decision**: Sign Requests and Webhooks are v1.1.0 items.
- **Fallback**: Direct transport may be used as the temporary escape hatch for these endpoints in v1.0.0.

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
