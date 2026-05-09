# Release Task Lists

This document details the planned tasks for the `v0.11` transition release and the upcoming `v1.0` architectural release.

## v0.11 Release Task List
**Focus**: Stability, correctness, and functional transition.

### Architecture & Core
- [x] Completed: Stabilize current implemented SDK behavior. *Rationale: Ensure the transition from v0.10.x is reliable. Implementation: Verified stable behavior in core services.*
- [~] Partially complete: Improve `Retry-After` handling in `Box\Service\Service`. *Rationale: Improve client resilience under rate limiting. Status: `Retry-After` header is parsed and added to `BoxResponseException` context (both seconds and date formats), but automatic retry loop is deferred to v1.0 to avoid breaking changes in service execution flow.*
- [x] Completed: Improve error messages in `BoxException` to include more context from `BoxResponse`. *Rationale: Better developer experience when debugging API errors. Implementation: `BoxResponseException` now parses `error` and `error_description` from Box API responses.*

### Auth
- [x] Completed: Improve authentication and refresh-token flows. *Rationale: Reliable auth is the foundation of the SDK. Implementation: Streamlined token exchange and refresh in `Client` and `Service`.*
- [x] Completed: Add more tests for `auth:refresh-token` and `auth:exchange-code` flows. *Rationale: Verify critical auth paths. Implementation: Added `AuthExchangeCommandTest` and improved `AuthRefreshCommandTest`.*

### Uploads
- [x] Completed: Improve upload reliability and `FileStream` behavior. *Rationale: Uploads are a core feature that needs to be rock solid. Implementation: `FileStream` provides a memory-efficient way to handle file uploads, verified in `Connection::postFile`.*
- [x] Completed: Better validate upload arguments before making requests. *Rationale: Fail fast on client-side errors. Implementation: Validation added to `FileStream` and `uploadFileToBox`.*

### Models & Mapping
- [x] Completed: Add low-risk missing model properties to existing models. *Rationale: Increase API coverage without breaking changes.*
    - **File**: `is_externally_owned`, `allowed_invite_roles`, `has_collaborations`, `metadata` [x]
    - **User**: `timezone`, `is_external_collab_restricted`, `is_exempt_from_device_limits` [x]
    - **Folder**: `folder_upload_email`, `can_non_owners_invite` [x]
- [x] Completed: Improve Hydrator tests to cover nested object mapping more thoroughly. *Rationale: Ensure the new recursive hydration works as expected. Implementation: Added `HydratorComplexTest` covering nested collections and deep object trees.*

### CLI
- **Must**: Preserve existing CLI command utility and align with v1 architecture.
- **Must**: Validate existing commands after v1 service/transport/auth refactors.
- **Must**: Ensure CLI output uses redaction rules; verify with redaction tests.
- **Should**: Support CLI-based JWT/S2S testing if practical.
- **Should**: Update CLI documentation for any changed command behavior.
- **Could**: Add more useful commands in v1.1.0 to improve practical SDK verification (useful workflows, not parity).

### Tests & Documentation
- [x] Completed: Expand PHPUnit coverage for existing file, folder, and auth services. *Rationale: Regressions must be prevented during the transition. Implementation: Significant test coverage added for models and commands.*
- [x] Completed: Update `docs/user/programmatic-usage.md` to reflect v0.11 changes. *Rationale: Keep documentation in sync with code.*
- [x] Completed: Add examples for using `FileStream` in documentation. *Rationale: Help users adopt the new streaming capability. Implementation: Examples added to `docs/user/programmatic-usage.md`.*

---

## v1.0 Release Task List
**Focus**: Architectural purity, removal of legacy baggage, and expanded coverage.

### Architecture
- **Must**: Make `Client` a facade over focused services (FileService, UserService, etc.). *Rationale: Decouple the God object and improve maintainability.*
- **Must**: Remove legacy namespaces and deprecated aliases. *Rationale: Clean up the API surface.*
- **Must**: Remove transition-layer array support for nested model fields. *Rationale: Enforce type safety and object-oriented patterns.*
- **Must**: Standardize IDs as `string`. *Rationale: Consistency across all resources.*
- **Must**: Standardize dates as `DateTimeImmutable`. *Rationale: Immutability and standard PHP types.*
- **Should**: Make `Connection` a raw request/response layer. *Rationale: Separate transport logic from model inheritance.*
- **Should**: Replace class-string setters and `validateClass()` with constructor injection/factories. *Rationale: Modernize dependency management.*
- **Could**: Support PSR-7 HTTP messages. *Rationale: Better interoperability with the PHP ecosystem.*

### API Coverage
- **Must**: Implement missing high-priority endpoints. *Rationale: Complete the SDK's utility.*
- **Must**: Achieve full PSR-12 compliance across the codebase. *Rationale: Modernize code quality and maintainability.*
- **Must**: Implement core foundation services according to hardened v1 strategy (Transport refactor with `send()` and `request()` support, thin Response wrapper replacement, Auth boundaries, JWT/S2S target with feasibility checkpoint, Logging/Redaction, Retry defaults).
- **Should**: Align HTTP layer with PSR-3, PSR-7, PSR-17, and PSR-18. *Rationale: Better interoperability with the PHP ecosystem.*
    - File Versions
    - Collections
    - Comments
    - Tasks
    - Metadata
- **Should**: Implement secondary endpoints.
    - File Requests
- **Could**: Implement governance/security endpoints.
    - Retention Policies
    - Legal Holds
    - Classifications

### Auth & Features
- **Must**: Add JWT authentication support (targeted for v1.0.0 foundation). *Rationale: Support server-to-server integrations.*
- **Should**: Add chunked upload support. *Rationale: Handle very large files reliably.*
- **Should**: Add token storage support or a clear extension point. *Rationale: Simplify token management for users.*

### Models & Mapping
- **Must**: Remove custom collection classes in favor of Doctrine Collections. *Rationale: Use standard, well-tested libraries.*
- **Should**: Use PHP Enums for roles, statuses, and types. *Rationale: Type safety and better IDE support.*
- **Should**: Add handling for Box-specific errors like `item_name_in_use` (409). *Rationale: Provide first-class support for common API error scenarios.*
