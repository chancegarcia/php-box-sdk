# Foundation Refinement

Roadmap reference: v1 Step 7

## Purpose
The **Foundation Refinement** initiative aims to harden the core infrastructure of the SDK, aligning it with the hardened v1 strategy. This involves refactoring the response handling, transport layer, error boundaries, and authentication foundation to ensure a robust, type-safe, and PSR-compliant base for all SDK services.

## Scope Statement
This initiative covers:
- Refactoring the SDK's response abstraction into a thin PSR-7-backed wrapper.
- Normalizing transport behavior to consistently return the new response abstraction.
- Hardening connection-level error handling and exception taxonomy.
- Stabilizing service-level response handling and decoding.
- Improving the authentication foundation (OAuth2, token storage, and auth providers).
- Ensuring CLI compatibility with the refined foundation.
- Updating documentation to reflect these architectural improvements.
- Addressing static analysis and type-safety issues in the touched areas.

## Non-goals
- Rewriting all service classes or resource models.
- Implementing full JWT/S2S support (unless specifically addressed in a slice).
- Performing a broad, unrelated PHPStan cleanup of the entire codebase.
- Changing the public API of services unless required by the refined foundation.
- Introducing new external dependencies beyond PSR interfaces.

## Compatibility Constraints
- Preserve existing public behavior of the SDK where practical.
- Maintain backward compatibility for response methods used by existing consumers.
- Ensure existing CLI commands continue to function correctly.
- Use **Foundation Refinement** as the human-readable initiative name.
- Retain **v1 Step 7** as a roadmap reference for traceability.

## Status Table

| Slice | Name | Status |
|---|---|---|
| 0 | [Tracker](#foundation-refinement-slice-0---tracker) | Completed |
| 1 | [Response Wrapper Foundation](#foundation-refinement-slice-1---response-wrapper-foundation) | Completed |
| 2 | [Transport Response Normalization](#foundation-refinement-slice-2---transport-response-normalization) | Completed |
| 3 | [Connection Error Boundaries](#foundation-refinement-slice-3---connection-error-boundaries) | Completed |
| 4 | [Service Response Handling Compatibility](#foundation-refinement-slice-4---service-response-handling-compatibility) | Not Started |
| 5 | [Auth Foundation Hardening](#foundation-refinement-slice-5---auth-foundation-hardening) | Not Started |
| 6 | [CLI Compatibility Pass](#foundation-refinement-slice-6---cli-compatibility-pass) | Not Started |
| 7 | [Documentation and Migration Drift Pass](#foundation-refinement-slice-7---documentation-and-migration-drift-pass) | Not Started |
| 8 | [Foundation PHPStan/Type-Safety Cleanup](#foundation-refinement-slice-8---foundation-phpstan-type-safety-cleanup) | Not Started |
| 9 | [Final Integration Review](#foundation-refinement-slice-9---final-integration-review) | Not Started |

---

## Dependencies between slices
- Slice 0 is the prerequisite for all other slices.
- Slices 1 and 2 are prerequisites for Slice 3 and 4.
- Slices 1, 2, and 3 are prerequisites for Slice 5.
- Slices 1-5 are prerequisites for Slice 6.
- Slice 7 depends on all implementation slices (1-6).
- Slice 8 focuses on cleanup of areas touched in 1-5.
- Slice 9 is the final step.

---

## Foundation Refinement Slice 0 — Tracker

**Purpose**: Create the tracker itself.

**Scope**:
- Planning documentation only.
- No source code changes.

**Acceptance Criteria**:
- Foundation Refinement tracker exists (this file).
- Roadmap reference to v1 Step 7 is retained.
- Slices are sequenced and detailed.
- Each slice has scope, non-goals, acceptance criteria, and validation expectations.

**Validation Expectations**:
- Document is valid Markdown.
- Links and references are correct.

**Implementation Prompt**:
(Already executed to create this tracker)

---

## Foundation Refinement Slice 1 — Response Wrapper Foundation

**Purpose**: Refactor the SDK response foundation toward a thin PSR-7-backed wrapper while preserving existing public behavior.

**Goal**: Replace the current Symfony-inherited `BoxResponse` with a new `BoxResponseInterface` and `BoxResponse` class in `Box\Http` that wraps a PSR-7 `ResponseInterface`.

**Scope**:
- `src/Http/BoxResponse.php` (Refactor/Replace)
- `src/Http/BoxResponseInterface.php` (Create)
- Existing response behavior and related tests.

**Non-goals**:
- Do not rewrite transport architecture.
- Do not change auth behavior.
- Do not perform broad PHPStan cleanup.

**Compatibility Constraints**:
- Existing response public contract (e.g., methods used by services) remains compatible.
- `BoxResponse` should no longer inherit from Symfony's `Response`.

**Implementation Guidance**:
- Create `BoxResponseInterface` with methods: `getPsrResponse()`, `getStatusCode()`, `isSuccessful()`, `getHeaders()`, `getHeader()`, `getHeaderLine()`, `hasHeader()`, `getBody()`, `getContent()`, `getRetryAfter()`, `json()`.
- Implement `BoxResponse` wrapping a `Psr\Http\Message\ResponseInterface`.
- Implement `json()` helper that throws `Box\Exception\ApiException` (or a more specific one if defined) on invalid JSON.
- Ensure `getRetryAfter()` correctly parses the `Retry-After` header.

**Test Expectations**:
- Unit tests for `BoxResponse` covering all interface methods.
- Test `json()` with valid JSON, invalid JSON (expect exception), and empty body (expect null).
- Test `getRetryAfter()` with various header values (seconds, dates).

**Validation Commands**:
- `composer test`
- `composer analyse`
- `composer cs:check`

**Completion Note**:
- Refactored `BoxResponse` to wrap `Psr\Http\Message\ResponseInterface` and removed Symfony inheritance.
- Implemented `BoxResponseInterface` with `json()`, `getRetryAfter()`, and PSR-7 methods.
- Preserved legacy constructor and Symfony-like status methods for backward compatibility.
- Updated `BoxException` with `INVALID_JSON` constant.
- Improved `ResponseParser` robustness (handling missing status lines, trimming).
- Full validation (`composer review`) passed.

**Final Summary Requirements**:
- Confirmation that `BoxResponse` no longer inherits from Symfony.
- Summary of new interface and helper methods.
- Test results for response behavior.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 1 — Response Wrapper Foundation.

Goal: Refactor the SDK response foundation toward a thin PSR-7-backed wrapper while preserving existing public behavior. Replace the current Symfony-inherited `BoxResponse` with a new `BoxResponseInterface` and `BoxResponse` class in `Box\Http` that wraps a PSR-7 `ResponseInterface`.

Scope:
- Create `src/Http/BoxResponseInterface.php`.
- Refactor `src/Http/BoxResponse.php` to implement the interface and wrap a PSR-7 response.
- Update/Add tests in `tests/Http/BoxResponseTest.php`.

Non-goals:
- Do not rewrite transport architecture.
- Do not change auth behavior.
- Do not perform broad PHPStan cleanup.

Compatibility Constraints:
- Existing response public contract remains compatible.
- `BoxResponse` must not inherit from `Symfony\Component\HttpFoundation\Response`.

Implementation Guidance:
- `BoxResponseInterface` should include: `getPsrResponse()`, `getStatusCode()`, `isSuccessful()`, `getHeaders()`, `getHeader()`, `getHeaderLine()`, `hasHeader()`, `getBody()`, `getContent()`, `getRetryAfter()`, `json(bool $assoc = true)`.
- `BoxResponse` must wrap `Psr\Http\Message\ResponseInterface`.
- `json()` helper must throw an SDK exception (e.g., `Box\Exception\ApiException`) on invalid JSON.
- `getRetryAfter()` should return `int|null`.
- `isSuccessful()` should return true for 2xx status codes.

Test Expectations:
- Cover all new interface methods.
- Test `json()` with valid/invalid/empty data.
- Test `getRetryAfter()` with integer and date values.
- Ensure no real network calls.

Validation:
- `composer test`
- `composer analyse`
- `composer cs:check`

Final Summary:
- Report changes to `BoxResponse`.
- Confirm removal of Symfony inheritance.
- Provide test execution summary.
```

---

## Foundation Refinement Slice 2 — Transport Response Normalization

**Purpose**: Ensure all transports consistently return the SDK response abstraction.

**Goal**: Update transport implementations (Guzzle, etc.) to wrap PSR-7 responses in the new `BoxResponse`.

**Scope**:
- `src/Connection/TransportInterface.php` (Verify/Update)
- `src/Connection/GuzzleTransport.php` (Update)
- `src/Connection/Connection.php` (Update if needed)
- Tests for transports.

**Acceptance Criteria**:
- All supported transports return `BoxResponseInterface`.
- Header/status/body behavior is consistent across transports.
- Transport selection remains backward-compatible.

**Test Expectations**:
- Mock PSR-18 client/Guzzle to return PSR-7 responses and verify they are wrapped correctly.
- Ensure transport error handling still functions.

**Validation Commands**:
- `composer test`
- `composer analyse`

**Completion Note**:
- Ensured all transports consistently return `BoxResponseInterface`.
- Optimized `GuzzleTransport` by using the new `psrResponse` named parameter in `BoxResponse` constructor.
- Refined `CurlTransport` to support `query` options, multi-value headers, and custom options via `initAdditionalCurlOpts`.
- Verified that `Connection::getCurlData` returns the refined `BoxResponse`.
- Reviewed and removed unnecessary `curl_close()` calls for PHP 8.5 compatibility.
- Added unit tests for `GuzzleTransport` and `CurlTransport`.
- Full validation (`composer review`) passed.
- Removed stale `@todo` comments in `BoxResponse`.
- Deferred full exception taxonomy to Slice 3.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 2 — Transport Response Normalization.

Scope:
- Update `src/Connection/TransportInterface.php` to type-hint `BoxResponseInterface` as return type for `send()` and `request()`.
- Update `src/Connection/GuzzleTransport.php` (and any other transport implementations) to wrap PSR-7 responses in `BoxResponse`.
- Update `src/Connection/Connection.php` if it handles response creation.

Acceptance Criteria:
- All transports return `BoxResponseInterface`.
- Behavior is consistent across implementations.

Test Expectations:
- Use mocks to verify wrapping of PSR-7 responses.
- Verify that transport options (headers, query, etc.) are correctly passed to the underlying client.
- No real network calls.

Validation:
- `composer test`
- `composer analyse`
```

---

## Foundation Refinement Slice 3 — Connection Error Boundaries

**Purpose**: Harden connection-level error handling and exception wrapping.

**Goal**: Implement the exception taxonomy defined in the v1 strategy and ensure transports/connections throw descriptive exceptions that include response context.

**Scope**:
- `src/Exception/` (New exception classes)
- Transport/Connection error handling logic.
- Exception redaction policy implementation.

**Acceptance Criteria**:
- SDK exceptions follow the defined hierarchy (`BoxException` -> `ClientException`, `TransportException`, `ApiException`).
- `ApiException` and its subclasses (401, 403, 404, etc.) carry the `BoxResponseInterface`.
- Secrets (tokens) are redacted from exception messages.
- No errors are silently swallowed.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 3 — Connection Error Boundaries.

Goal: Harden connection-level error handling and implement the SDK exception taxonomy.

Scope:
- Define/Update exception hierarchy in `src/Exception/`: `BoxException`, `ClientException`, `TransportException`, `ApiException` (with subclasses for 401, 403, 404, 409, 429).
- Update Transports to catch PSR-18/Guzzle exceptions and re-throw SDK-specific ones.
- Ensure `ApiException` includes the `BoxResponseInterface`.
- Implement redaction of tokens in exception messages.

Acceptance Criteria:
- Clear exception hierarchy.
- Response context preserved in API exceptions.
- Redaction of sensitive data in logs/exceptions.

Test Expectations:
- Test various error status codes (401, 404, 500) and verify correct SDK exception is thrown.
- Test network timeouts/failures and verify `TransportException`.
- Verify redaction of tokens in exception messages.

Validation:
- `composer test`
- `composer analyse`

**Completion Note**:
- Implemented exception hierarchy: `BoxException` -> `BoxResponseException` -> `ApiException` (with 401, 403, 404, 409, 429 subclasses).
- Added `TransportException` for network/execution failures.
- Updated `GuzzleTransport` and `Connection` (Curl) to wrap transport failures in `TransportException`.
- Implemented optional `throw_on_error` behavior in `Connection::request()` to throw `ApiException` while preserving BC by default.
- Implemented `Redactor` utility and integrated it into `BoxException` to mask sensitive data (tokens, secrets) in messages and context.
- Ensured `ApiException` preserves `BoxResponseInterface` access for troubleshooting.
- Added comprehensive unit tests for error boundaries and redaction.
- Full validation (`composer review`) passed.
```

---

## Foundation Refinement Slice 4 — Service Response Handling Compatibility

**Purpose**: Stabilize service-level response handling while preserving legacy behavior.

**Goal**: Ensure services use the new `BoxResponse` features while maintaining their existing return types (Resources/DTOs).

**Scope**:
- `src/Service/` base service or specific services.
- Response decoding logic in services.
- Tests for service response handling.

**Acceptance Criteria**:
- Existing service response behavior remains compatible.
- Response decoding behavior is deterministic (using `BoxResponse::json()`).
- Exceptions still carry response information where expected.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 4 — Service Response Handling Compatibility.

Goal: Stabilize service-level response handling using the refined foundation while preserving existing public behavior.

Scope:
- Update service response handling (e.g., in `UserService` or a base service class) to use `BoxResponse` methods.
- Ensure resource hydration still works correctly with the new response wrapper.

Acceptance Criteria:
- Services return the same Resource/DTO types as before.
- Decoding uses the `BoxResponse::json()` helper.
- Public service contracts remain stable.

Test Expectations:
- Regression tests for `UserService` and any other migrated services.
- Verify that API errors still result in expected exceptions.

Validation:
- `composer test`
- `composer analyse`
```

---

## Foundation Refinement Slice 5 — Auth Foundation Hardening

**Purpose**: Improve auth/token behavior on top of the stable response/transport/connection foundation.

**Goal**: Refactor `AuthProvider` and `TokenStorage` to align with the v1 strategy (passive storage, context support, redaction).

**Scope**:
- `src/Connection/AuthProviderInterface.php`
- `src/Storage/TokenStorageInterface.php`
- `src/Storage/InMemoryTokenStorage.php`, `src/Storage/PdoTokenStorage.php`.
- `src/Dto/TokenStorageContext.php` (Create if needed).

**Acceptance Criteria**:
- `AuthProvider` handles token lifecycle (refresh) using the refined transport.
- `TokenStorage` is passive and supports `TokenStorageContext`.
- One active token per context rule is enforced.
- Auth errors are descriptive and redacted.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 5 — Auth Foundation Hardening.

Goal: Refactor auth/token behavior to align with the v1 strategy (passive storage, context support, redaction).

Scope:
- Refine `AuthProviderInterface` and `TokenStorageInterface`.
- Implement `TokenStorageContext` DTO.
- Update `InMemoryTokenStorage` and `PdoTokenStorage` to support contexts and the "one active token per context" rule.
- Ensure `AuthProvider` uses the refined transport and handles refreshes safely.
- Implement redaction for tokens in auth-related logs/exceptions.

Acceptance Criteria:
- Clear separation between Provider (active) and Storage (passive).
- Context-aware token storage.
- Redaction of secrets.

Test Expectations:
- Test token refresh flow with mocked transport.
- Test storage with multiple contexts.
- Verify redaction of tokens in auth exceptions.

Validation:
- `composer test`
- `composer analyse`
```

---

## Foundation Refinement Slice 6 — CLI Compatibility Pass

**Purpose**: Ensure CLI commands remain compatible with the refined foundation.

**Goal**: Verify and fix CLI commands after the core foundation changes.

**Scope**:
- `src/Command/`
- CLI transport/auth configuration.
- CLI output masking/redaction.

**Acceptance Criteria**:
- Existing CLI commands remain functional.
- CLI output does not leak secrets (using SDK redaction).
- Transport options in CLI still work.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 6 — CLI Compatibility Pass.

Goal: Ensure CLI commands remain compatible with the refined foundation and adhere to redaction rules.

Scope:
- Run CLI commands and verify they still work with the new transport/response/auth layers.
- Ensure CLI output redacts tokens and secrets.
- Fix any broken command logic caused by foundation changes.

Acceptance Criteria:
- CLI functional parity with previous state.
- No leaked secrets in console output.

Validation:
- `composer test` (Command tests)
- Manual verification of key commands (e.g., `user:get`, `auth:token`).
```

---

## Foundation Refinement Slice 7 — Documentation and Migration Drift Pass

**Purpose**: Update public docs to reflect foundation changes.

**Acceptance Criteria**:
- `README.md` and migration docs match implemented behavior.
- Migration notes are clear about response wrapper and exception changes.
- All examples use placeholder credentials.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 7 — Documentation and Migration Drift Pass.

Goal: Update public documentation to reflect implementation changes in the foundation.

Scope:
- Update `docs/migration/v1-migration.md` (or relevant migration doc).
- Update `README.md` if response wrapper or exception usage examples are present.
- Ensure all docs use the new "Foundation Refinement" terminology where appropriate.

Acceptance Criteria:
- Documentation matches implementation.
- No credential leaks in examples.
```

---

## Foundation Refinement Slice 8 — Foundation PHPStan/Type-Safety Cleanup

**Purpose**: Address static-analysis/type-safety issues in touched foundation areas.

**Scope**: Files touched in Slices 1-5.

**Acceptance Criteria**:
- PHPStan passes for touched areas without new baseline entries.
- Type hints are strict and accurate.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 8 — Foundation PHPStan/Type-Safety Cleanup.

Goal: Address static-analysis and type-safety issues in the refined foundation areas.

Scope:
- All files modified during Slices 1-5.
- Corresponding tests.

Acceptance Criteria:
- `composer analyse` passes for touched files.
- No unrelated rewrites.
```

---

## Foundation Refinement Slice 9 — Final Integration Review

**Purpose**: Confirm Foundation Refinement is complete and coherent.

**Junie Prompt**:
```markdown
Implement Foundation Refinement Slice 9 — Final Integration Review.

Goal: Perform a final review and validation of the entire Foundation Refinement initiative.

Scope:
- Run full project validation (`composer review`).
- Update the Foundation Refinement tracker status table.
- Document any deferred work or identified risks.

Acceptance Criteria:
- All slices marked as completed.
- Full validation suite passes.
```
