# Audit: Step 12 — Token Storage Completion

## Status
- **Date**: 2026-05-12
- **Status**: Completed
- **Step**: 12 (v1 Release)

## Step 12 Requirements Summary
Step 12 aims to finalize the passive token storage layer for the v1 release. 
- Token storage must remain **passive persistence only**.
- No network calls, refreshes, or auth-code exchanges within the storage layer.
- Services must be decoupled from storage.
- Support for context-aware token lookups (e.g., account/user contexts).
- PDO and In-Memory implementations must be completed and tested.

## Existing Token Storage Inventory

### Interfaces
- `Box\Storage\Token\TokenStorageInterface`: Final v1 base contract. Introduced `TokenStorageContext` requirement.
- `Box\Storage\Token\Pdo\TokenStorageInterface`: Extension for PDO-specific storage. Updated to extend v1 contract.

### Implementations
- `Box\Storage\Token\Container\TokenStorageContainer`: In-memory implementation. Now fully context-aware and implements v1 contract.
- `Box\Storage\Token\Pdo\TokenStorage`: Updated with v1 method stubs. Final implementation deferred to 12.3.

### Token Model
- `Box\Connection\Token\TokenInterface`: The primary DTO used for token data.
- `Box\Connection\Token\Token`: Concrete implementation.

### Exceptions
- `Box\Exception\TokenStorageException`: Updated to use `TokenStorageContext` and enforce secret redaction (no token fields in exception).

## Usage Inventory

### Client
- `Box\Client`: Manages internal token state but lacks formal `TokenStorageInterface` orchestration hooks.

### Services
- `Box\Service\Service`: **Rationalized**. Storage references were removed in Slice 12.4. Services are now completely storage-independent.

### CLI / Commands
- `src/Command/AuthExchangeCommand`, `src/Command/AuthRefreshCommand`: Manually write secrets to files via `ConfigProvider`. Should be updated to optionally use the formal storage layer.

## Architecture Gap Analysis
1. **Service Dependency**: Resolved in 12.4. Orchestration moved to `Client` (12.5).
2. **Context DTO**: Resolved in 12.1.
3. **PDO Implementation Debt**: Resolved in 12.3.
4. **Redaction**: Resolved across all storage layers and exceptions.

## Risk Assessment
- **BC Risks**: Changing `BaseTokenStorageInterface` to `TokenStorageInterface` is a breaking change (planned for v1).
- **Security**: Accidentally logging or storing tokens in plaintext without proper redaction in logs/exceptions.
- **Portability**: Ensuring `Pdo\TokenStorage` works across different PDO drivers (primarily SQLite for testing and MySQL/Postgres for production).

## Contract Design Questions & Answers

1. **What is the final v1 storage interface name?**
   - `Box\Storage\Token\TokenStorageInterface`.
2. **Should existing legacy storage interfaces remain?**
   - No, they should be replaced or adapted for v1.
3. **What is the canonical token storage context type?**
   - A new `Box\Dto\TokenStorageContext` DTO.
4. **What fields identify a storage context?**
   - Minimally: `userId`, `enterpriseId`, and `clientId`.
5. **Is one-active-token-per-context retained?**
   - Yes. Enforced by unique keys in the storage backend.
6. **Expected behavior when no token exists?**
   - `retrieveToken` returns `null`.
7. **How are token secrets redacted?**
   - Storage should never log the token object itself. Exceptions should only include context keys, never the access/refresh tokens.
8. **Does PDO storage own schema creation?**
   - No. It should operate on an existing table, but provide a sample schema in documentation.

## Proposed Sliced Step 12 Plan

### Step 12.1 — Storage Contract Finalization
- **Goal**: Rename/Refactor to `TokenStorageInterface` and introduce `TokenStorageContext`.
- **Scope**: Interfaces and DTOs only.
- **Files**: `src/Storage/Token/TokenStorageInterface.php`, `src/Dto/TokenStorageContext.php`.

### Step 12.2 — In-Memory Storage Completion
- **Goal**: Support context-aware storage in `TokenStorageContainer`.
- **Scope**: Update `TokenStorageContainer` implementation.
- **Files**: `src/Storage/Token/Container/TokenStorageContainer.php`.
- **Status**: Completed (Slice 12.2)

### Step 12.3 — PDO Storage Implementation
- **Goal**: Full rewrite of `Pdo\TokenStorage` with parameterized queries.
- **Scope**: Implementation and SQLite-based unit tests.
- **Files**: `src/Storage/Token/Pdo/TokenStorage.php`.
- **Status**: Completed (Slice 12.3)

### Step 12.4 — Service Storage-Independence Cleanup
- **Goal**: Remove storage references from `Box\Service\Service`.
- **Scope**: Refactor service base class to be storage-agnostic. Address untyped properties and methods (Service type-safety follow-up).
- **Files**: `src/Service/Service.php`.
- **Status**: Completed (Slice 12.4)

### Step 12.5 — Client Integration Hooks
- **Goal**: Add minimal storage configuration to `Client`.
- **Scope**: Update `Client` to accept storage and provide load/save hooks.
- **Files**: `src/Client.php`.
- **Status**: Completed (Slice 12.5)

### Step 12.6 — CLI/Auth Harness Storage Integration
- **Goal**: Allow CLI commands to use storage.
- **Scope**: Update auth-related commands.
- **Files**: `src/Command/*.php`.
- **Status**: Completed (Slice 12.6)

### Step 12.7 — Type-Safety, Docs, and Final Review
- **Goal**: PHPStan fixes and documentation updates.
- **Scope**: Final project review for Step 12.
- **Status**: Completed (Slice 12.7)

## Final Conclusions (Step 12 Completion)
1. **Passive Storage Architecture**: Verified that `TokenStorageInterface` and its implementations (In-Memory, PDO) remain strictly passive. They handle persistence but do not initiate network calls or auth lifecycle operations.
2. **Service Independence**: Confirmed that the `Service` layer is completely decoupled from token storage. Services receive tokens but do not know how they are stored or retrieved.
3. **Client Orchestration**: `Client` now serves as the optional coordination point for loading, saving, and removing tokens from storage.
4. **CLI Integration**: CLI commands now optionally integrate with token storage using `ConfigProvider` for DSN/credentials and supporting command-line overrides for context (User ID/Enterprise ID).
5. **Security**: Redaction of sensitive fields in exceptions and logs is enforced.
6. **Defereals**: 
    - **Auth Lifecycle/Auth Provider extraction**: Deferred to Step 13+ as planned.
    - **FilesystemTokenStorage**: Excluded from v1 core; PDO and In-Memory are deemed sufficient for initial v1 release.
    - **JWT/S2S**: Deferred to Step 14/15.

## Step 12.6 Verification Log
- `AuthExchangeCommand` and `AuthRefreshCommand` correctly use `Client` storage hooks.
- ConfigProvider correctly supplies `BOX_STORAGE_PDO_DSN`, `BOX_STORAGE_PDO_USER`, and `BOX_STORAGE_PDO_PASSWORD`.
- CLI overrides take precedence over config.
- No raw `$_ENV` usage in commands.
- One-active-token-per-context behavior verified via PDO and In-Memory tests.

```markdown
### Task: Slice 12.1 — Storage Contract Finalization

### Context
Finalize the v1 `TokenStorageInterface` and introduce a formal `TokenStorageContext` DTO.

### Scope
- Rename/Refactor `BaseTokenStorageInterface` to `TokenStorageInterface`.
- Create `Box\Dto\TokenStorageContext` with properties for `userId`, `enterpriseId`, and `clientId`.
- Update interface methods to accept `TokenStorageContext`.
- Ensure write methods return `void` and read methods return `?TokenInterface`.

### Requirements
- PHP 8.4 strict types.
- No implementation logic yet (mock/stub only if needed for interface check).
- PSR-12 formatting.

### Validation
- composer lint
- composer analyse
```

## Deferred JWT/S2S CLI Configuration Note
- When JWT/S2S auth is implemented, evaluate supporting separate environment-variable groups or named auth profiles (e.g., `BOX_OAUTH_CLIENT_ID` vs `BOX_JWT_CLIENT_ID`) to prevent credential collisions in CLI testing.
