# Release Task Lists

This document details the planned tasks for the `v0.11` transition release and the upcoming `v1.0` architectural release.

## v0.11 Release Task List
**Focus**: Stability, correctness, and functional transition.

### Architecture & Core
- **Must**: Stabilize current implemented SDK behavior. *Rationale: Ensure the transition from v0.10.x is reliable.*
- **Should**: Improve `Retry-After` handling in `Box\Service\Service`. *Rationale: Improve client resilience under rate limiting.*
- **Could**: Improve error messages in `BoxException` to include more context from `BoxResponse`. *Rationale: Better developer experience when debugging API errors.*

### Auth
- **Must**: Improve authentication and refresh-token flows. *Rationale: Reliable auth is the foundation of the SDK.*
- **Should**: Add more tests for `auth:refresh-token` and `auth:exchange-code` flows. *Rationale: Verify critical auth paths.*

### Uploads
- **Must**: Improve upload reliability and `FileStream` behavior. *Rationale: Uploads are a core feature that needs to be rock solid.*
- **Should**: Better validate upload arguments before making requests. *Rationale: Fail fast on client-side errors.*

### Models & Mapping
- **Must**: Add low-risk missing model properties to existing models. *Rationale: Increase API coverage without breaking changes.*
    - **File**: `is_externally_owned`, `allowed_invite_roles`, `has_collaborations`, `metadata`
    - **User**: `timezone`, `is_external_collab_restricted`, `is_exempt_from_device_limits`
    - **Folder**: `folder_upload_email`, `can_non_owners_invite`
- **Should**: Improve Hydrator tests to cover nested object mapping more thoroughly. *Rationale: Ensure the new recursive hydration works as expected.*

### CLI
- **Must**: Keep CLI as a verification/test harness. *Rationale: Focus on SDK quality over CLI features.*
- **Should**: Improve CLI error reporting for failed API calls. *Rationale: Make it easier to use the CLI for manual verification.*

### Tests & Documentation
- **Must**: Expand PHPUnit coverage for existing file, folder, and auth services. *Rationale: Regressions must be prevented during the transition.*
- **Should**: Update `docs/programmatic-usage.md` to reflect v0.11 changes. *Rationale: Keep documentation in sync with code.*
- **Should**: Add examples for using `FileStream` in documentation. *Rationale: Help users adopt the new streaming capability.*

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
- **Should**: Align HTTP layer with PSR-7, PSR-17, and PSR-18. *Rationale: Better interoperability with the PHP ecosystem.*
    - File Versions
    - Collections
    - Comments
    - Tasks
    - Metadata
- **Should**: Implement secondary endpoints.
    - Webhooks
    - Sign Requests
    - File Requests
- **Could**: Implement governance/security endpoints.
    - Retention Policies
    - Legal Holds
    - Classifications

### Auth & Features
- **Should**: Add JWT authentication support. *Rationale: Support server-to-server integrations.*
- **Should**: Add chunked upload support. *Rationale: Handle very large files reliably.*
- **Should**: Add token storage support or a clear extension point. *Rationale: Simplify token management for users.*

### Models & Mapping
- **Must**: Remove custom collection classes in favor of Doctrine Collections. *Rationale: Use standard, well-tested libraries.*
- **Should**: Use PHP Enums for roles, statuses, and types. *Rationale: Type safety and better IDE support.*
- **Should**: Add handling for Box-specific errors like `item_name_in_use` (409). *Rationale: Provide first-class support for common API error scenarios.*
