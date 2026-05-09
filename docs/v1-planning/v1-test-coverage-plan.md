# V1 Test Coverage Plan

## Purpose

This document defines the expected test coverage for the V1 implementation phases of the `box-api-v2-sdk`. It serves as a blueprint for preventing regressions during resource migration, service refactoring, API coverage expansion, and legacy model removal.

Key objectives:
- Provide a retrospective coverage audit for the completed User resource migration.
- Establish forward-looking test requirements for all remaining V1 phases.
- Distinguish between maintaining existing functionality and expanding API coverage.
- Define "Done" criteria for testing in each phase.

This is a planning document; it defines tests to be written or verified during implementation, but does not implement them itself.

## Service Scope Principle: Migration Parity vs Box API Coverage

To ensure a successful transition to V1, services must be tested against two distinct benchmarks.

### Migration parity tests

Migration parity tests verify that existing SDK/client capabilities are preserved when behavior moves into V1 resources and focused services.

These tests answer:
- What could the old client/service/resource already do?
- Does the new V1 service preserve that behavior?
- Are endpoint paths, HTTP methods, request parameters, response handling, and error behavior preserved where intended?
- Are return types now V1 resources/DTOs rather than legacy models/interfaces?
- Are deprecated or removed public APIs documented?

### Box API coverage tests

Box API coverage tests verify behavior based on what the Box API supports and what the V1 API coverage audit says is in scope.

These tests answer:
- What operations does the Box API support for this resource?
- Which operations are in scope for this V1 phase?
- Which operations are intentionally deferred?
- Are request DTOs, response DTOs, pagination, nested resources, enum fields, and error cases covered?
- Does the service expose enough of the API to meet the phase goal without over-expanding scope?

### Rule for service planning

For each service/resource phase, both **Migration parity service tests** and **Box API coverage service tests** must be planned. If a resource is new (no legacy equivalent), migration parity is marked "N/A". If a legacy feature is intentionally removed, it must be documented as a migration/compatibility item.

## General Testing Principles

- **Focus**: Prefer focused PHPUnit tests for resource/unit behavior.
- **Isolation**: Avoid live Box API calls in unit tests. Use mocks, fakes, fixtures, or test doubles for HTTP/service behavior.
- **Completeness**: Add service/behavior tests for endpoints, request delegation, response hydration, return types, and error paths.
- **Layers**: 
    - **Hydrator/mapper tests** for new mapping behavior.
    - **DTO tests** for request and response envelopes.
    - **Enum tests** for backed values and hydration.
- **Standards**: Keep tests aligned with PHPStan and PSR-12 expectations.
- **Validation**: Prefer Composer validation scripts (`composer test`, `composer analyse`).

## Standard Test Categories

### Resource/unit tests
Cover passive resource object behavior:
- Constructor, getters, and setters.
- Default values and nullable fields.
- Typed IDs (string|int) and `DateTimeImmutable` fields.
- Enum-backed fields and nested resource/DTO fields.
- Absence of dependency on legacy model base classes or traits.

### Service/behavior tests
Cover service behavior without live API calls:
- **Migration parity**: Preservation of old client capabilities, endpoint paths, and hydration.
- **Box API coverage**: Endpoint paths from API docs, request/response DTO shapes, pagination, and error cases.

### Hydrator/mapper tests
Cover:
- snake_case to camelCase mapping.
- Setter and property hydration.
- Nested object and collection item type inference.
- `DateTimeImmutable` and enum hydration.

### DTO and Enum tests
Cover:
- DTO serialization/deserialization and validation.
- Enum backed values and `from()`/`tryFrom()` behavior.

### Migration/compatibility tests
Cover:
- Removed interfaces and namespaces.
- Deprecated class behavior and breaking change documentation.
- Stale reference detection via static analysis.

## User resource migration coverage audit (Retrospective)

The User migration served as the pilot for the V1 architecture. This audit evaluates the coverage achieved during Step 5 of the implementation.

### Audit Table

| Expectation | Category | Covered? | Evidence | Gap / Follow-up | Class |
| :--- | :--- | :--- | :--- | :--- | :--- |
| Passive User Resource | Resource/Unit | ✓ | `Box\Resource\User` | None | Parity |
| Remove `UserInterface` | Migration | ✓ | `src/Resource/User.php` | None | Parity |
| User Accessors (IDs, Dates) | Resource/Unit | ✓ | `UserTest::testUserResourceAccessors` | None | Parity |
| User Status Enum | Enum | ✓ | `UserStatus` class | None | Parity |
| Scalar to Enum Hydration | Hydrator | ✓ | `UserTest::testUserHydrationFromScalarStatus` | None | Parity |
| Legacy Model Independence | Resource/Unit | ✓ | `Box\Resource\User` source | None | Parity |
| `UserService::getCurrentUser` | Service | ! | `UserService.php` exists | Missing `UserServiceTest` | Parity |
| `UserService::getUser` | Service | ! | `UserService.php` exists | Missing `UserServiceTest` | Parity |
| Enterprise User Endpoints | Service | ✓ | Audit Doc | Explicitly Deferred | Deferred |
| `UserInterface` removal check | Migration | ✓ | No references in `src/` | None | Parity |

### User migration follow-up test tasks

- **Add `UserServiceTest`**: Implement behavior tests for `getCurrentUser()` and `getUser()` using mocked connections to verify endpoint paths and V1 resource hydration.
- **Hydrator Nested Resource Test**: Verify that the Hydrator correctly handles nested resources if added to the User resource in the future (currently User fields are primarily scalars/enums).
- **Static Analysis check**: Ensure PHPStan baseline does not contain new User-related exclusions that mask missing type safety.

## Future phase coverage requirements

### Group and GroupMembership migration
- **Units**: `Box\Resource\Group`, `Box\Resource\GroupMembership`.
- **Tests**: 
    - Passive resource accessors (IDs as strings, name, etc.).
    - Hydration from API arrays (including nested User objects in memberships).
    - Service tests for `getGroup`, `createGroup`, `addMembership`.
    - Migration parity for existing Group client methods.

### Shared DTO foundation
- **Units**: `SharedLink`, `Permissions`, `PathCollection`.
- **Tests**:
    - DTO-specific hydration (e.g., `SharedLink` with `DateTimeImmutable` unshared_at).
    - Consumption by File/Folder resources.
    - Enum-backed access levels (`SharedLinkAccess`).

### File and FileVersion migration
- **Units**: `Box\Resource\File`, `Box\Resource\FileVersion`.
- **Tests**:
    - Complex hydration (nested DTOs, Collections of `FileVersion`).
    - Service behavior for `getFile`, `uploadFile` (if migrated), `getVersions`.
    - Migration parity for `Client::uploadFileToBox`.

### Folder and FolderItems migration
- **Units**: `Box\Resource\Folder`, `FolderItemsResponse`.
- **Tests**:
    - Mixed item collection hydration (File/Folder/WebLink).
    - Pagination metadata in `FolderItemsResponse`.
    - Service behavior for `getFolder`, `getFolderItems`.

### Collaboration migration
- **Units**: `Box\Resource\Collaboration`, `CollaborationRole` Enum.
- **Tests**:
    - Role hydration and validation.
    - Nested "accessible_item" hydration.
    - Service behavior for adding/removing/updating collaborations.

### Event migration
- **Units**: `Box\Resource\Event`.
- **Tests**:
    - Stream position handling.
    - Source object hydration (polymorphic item types).
    - Service behavior for user event polling.

### Client facade refactor
- **Tests**:
    - Verify `Client` delegates to `UserService`, `FileService`, etc.
    - Regression tests for public entry points (e.g., `getNewUser`).
    - Verify return types are now V1 resources across all refactored methods.

### Service interface and factory cleanup
- **Tests**:
    - Verification that services are instantiable without legacy factories.
    - Static analysis check for removed interface usage.

### Model abstraction removal and Box\Model namespace removal
- **Tests**:
    - Ensure all migrated resources pass tests without `Model` base classes.
    - Autoload validation after `src/Model` deletion.

### Deferred phases (Comments, Tasks, Metadata, etc.)
- **Requirements**:
    - Define Box API coverage service tests for each new resource.
    - Add request DTO tests for create/update payloads.
    - Error handling tests for resource-specific API errors (e.g., task already completed).

## Validation command policy

All V1 implementation and testing work must be validated using:

- `composer dump-autoload`
- `composer test`
- `composer analyse`
- `composer cs:check`

If style issues are found, run `composer cs:fix` followed by `composer cs:check`. Do not use ad hoc tool commands.
