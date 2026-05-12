# Audit: Step 12 — Token Storage Completion

## Status
- **Date**: 2026-05-12
- **Status**: Planning Completed / Implementation Pending
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
- `Box\Storage\Token\BaseTokenStorageInterface`: Current base contract. Needs renaming to `TokenStorageInterface` for v1.
- `Box\Storage\Token\Pdo\TokenStorageInterface`: Extension for PDO-specific storage.

### Implementations
- `Box\Storage\Token\Container\TokenStorageContainer`: In-memory implementation. Currently context-unaware (manages a single token).
- `Box\Storage\Token\Pdo\TokenStorage`: Incomplete. `storeToken` has broken SQL, other methods are `TODO`.

### Token Model
- `Box\Connection\Token\TokenInterface`: The primary DTO used for token data.
- `Box\Connection\Token\Token`: Concrete implementation.

### Exceptions
- `Box\Exception\BoxStorageException`: General storage exception. Needs review for redaction of secrets in messages.

## Usage Inventory

### Client
- `Box\Client`: Manages internal token state but lacks formal `TokenStorageInterface` orchestration hooks.

### Services
- `Box\Service\Service`: **Critical Gap**. Currently depends on `BaseTokenStorageInterface` and attempts to persist refreshed tokens. This violates the passive-storage boundary and service-independence rule.

### CLI / Commands
- `src/Command/AuthExchangeCommand`, `src/Command/AuthRefreshCommand`: Manually write secrets to files via `ConfigProvider`. Should be updated to optionally use the formal storage layer.

## Architecture Gap Analysis
1. **Service Dependency**: Services are too "active" regarding storage. Orchestration must move to `Client` or a future `AuthProvider`.
2. **Context DTO**: Lack of a formal `TokenStorageContext` DTO. Lookups are currently implicit or missing.
3. **PDO Implementation Debt**: The existing PDO storage is non-functional and requires a complete rewrite.
4. **Redaction**: No explicit enforcement of secret redaction in storage-level exceptions or logs.

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

### Step 12.3 — PDO Storage Implementation
- **Goal**: Full rewrite of `Pdo\TokenStorage` with parameterized queries.
- **Scope**: Implementation and SQLite-based unit tests.
- **Files**: `src/Storage/Token/Pdo/TokenStorage.php`.

### Step 12.4 — Service Storage-Independence Cleanup
- **Goal**: Remove storage references from `Box\Service\Service`.
- **Scope**: Refactor service base class to be storage-agnostic. Address untyped properties and methods (Service type-safety follow-up).
- **Files**: `src/Service/Service.php`.

### Step 12.5 — Client Integration Hooks
- **Goal**: Add minimal storage configuration to `Client`.
- **Scope**: Update `Client` to accept storage and provide load/save hooks.
- **Files**: `src/Client.php`.

### Step 12.6 — CLI/Auth Harness Storage Integration
- **Goal**: Allow CLI commands to use storage.
- **Scope**: Update auth-related commands.
- **Files**: `src/Command/*.php`.

### Step 12.7 — Type-Safety, Docs, and Final Review
- **Goal**: PHPStan fixes and documentation updates.
- **Scope**: Final project review for Step 12.

## Draft Implementation Prompt: Slice 12.1

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
