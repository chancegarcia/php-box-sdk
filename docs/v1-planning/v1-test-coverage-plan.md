# V1 Test Coverage Plan

## Purpose

This document defines the expected test coverage for the V1 implementation phases of the `php-box-sdk`. It serves as a reliable source of truth for User migration follow-up testing and all remaining V1 phase test planning.

Key objectives:
- Define tests to write or verify later.
- Include a retrospective User migration coverage audit.
- Guide future V1 test planning.
- Distinguish migration parity from Box API coverage.
- Establish "Done" criteria for each phase.

This is a planning document; it defines tests to be written or verified during implementation, but does not implement them itself.

## Service Scope Principle: Migration Parity vs Box API Coverage

To ensure a successful transition to V1, services must be tested against two distinct benchmarks.

### Migration parity service tests

These preserve existing SDK/client capabilities while moving behavior into V1 resources and focused services. They ensure that what worked in the legacy SDK still works in V1, albeit with improved types and structure.

### Box API coverage service tests

These cover operations based on the Box API and the V1 API coverage audit. They ensure the SDK correctly implements the targeted subset of the Box API for a given resource.

### Deferred API coverage

These identify endpoints or behaviors that are known but intentionally out of scope for the current phase. This prevents "scope creep" while ensuring awareness of what remains to be implemented.

> Each service/resource phase must identify migration parity tests, Box API coverage tests, and deferred API coverage. If a resource has no legacy SDK equivalent, migration parity is “N/A”. If a legacy capability is intentionally removed or changed, document it as a migration/compatibility item.

## General Testing Principles

- **PHPUnit-focused resource/unit tests**: Prefer focused tests for resource/unit behavior.
- **Service/behavior tests with mocks/fakes**: Avoid live Box API calls in unit tests. Use mocked connections/responses to verify service behavior.
- **Hydrator/mapper tests**: Verify mapping behavior, especially for complex or nested structures.
- **DTO tests**: Verify request and response objects, ensuring correct serialization and typing.
- **Enum tests**: Verify backed values and hydration behavior.
- **Migration/compatibility checks**: Ensure removed interfaces/namespaces are truly gone and breaking changes are documented.
- **Documentation checks**: Ensure migration guides and audits are updated.
- **Composer script validation**: Always validate using `composer test`, `composer analyse`, and `composer cs:check`.
- **V1 ID Typing**: V1 resource IDs should be typed as strings. Legacy `string|int` compatibility should be called out only where it is intentionally retained during transition.
- **Redaction**: Tests must verify that sensitive data (tokens, secrets) are never exposed in logs, exception messages, or public metadata.
- **Retry Defaults**: Tests must verify that retry is disabled by default.

## Standard Test Categories

### Transport and Connection Tests
Include:
- `TransportInterface` implementation with PSR-18 client.
- Auth header injection.
- Retry policy execution (and disabled-by-default behavior).
- `BoxResponseInterface` thin wrapper behavior (PSR-7 proxy, `getRetryAfter` parsing, `isSuccessful`, etc.).
- Direct transport public API usage supporting both `send()` and `request()` patterns.
- Direct transport returns the thin SDK wrapper.

### Resource/unit tests
Include:
- Passive resource construction.
- Getters/setters.
- Default and nullable fields.
- V1 string IDs.
- `DateTimeImmutable` fields.
- Enum-backed fields.
- Nested resource/DTO fields.
- Collection fields.
- No dependency on legacy model base classes or traits.

### Service/behavior tests

#### Migration parity service tests
Include:
- Existing client/service capability preservation.
- Endpoint paths and HTTP methods from old behavior.
- Request parameters and payloads preserved where intended.
- Response decoding and hydration preserved where intended.
- Concrete V1 resource/DTO return types.
- Public API migration behavior.
- Deprecated/removed behavior documented.

#### Box API coverage service tests
Include:
- Endpoint paths and HTTP methods from Box API coverage docs.
- Request DTO shape.
- Response DTO/resource shape.
- Query parameters.
- Pagination.
- Nested resources.
- Enum-backed fields.
- Common Box API error cases.
- In-scope vs deferred endpoint decisions.

### Hydrator/mapper tests
Include:
- snake_case to camelCase mapping.
- Setter hydration.
- Property hydration.
- Nested object hydration.
- Collection item hydration.
- `DateTimeImmutable` hydration.
- Enum hydration.
- Invalid or unsupported value behavior.
- Strict typing preservation.

### DTO tests
Include:
- Request DTO shape.
- Response DTO shape.
- Nullable/optional fields.
- Nested DTOs.
- Serialization/array conversion if supported.
- Validation rules if implemented.

### Enum tests
Include:
- Backed values.
- `from()` / `tryFrom()` behavior where relevant.
- Service/resource hydration use.
- Invalid value behavior if defined.

### Migration/compatibility tests
Include:
- Removed interfaces.
- Removed legacy namespaces or aliases.
- Deprecated class behavior if temporarily retained.
- Documented breaking changes.
- Stale reference detection where practical.

### Security and Redaction Tests
Include:
- Token redaction in `BoxException` messages and context.
- Secret redaction in PSR-3 logs.
- CLI output masking.
- Verification that raw PSR-7 messages are only exposed via opt-in/debug modes.

### Documentation checks
Include:
- Migration guide updates.
- Checklist completion notes.
- API coverage audit updates when API scope changes.
- No stale class names, namespaces, interfaces, or commands.
- Clear migration parity vs API coverage/deferred scope notes.

## User Resource Migration Coverage Audit

### Audit Table

| Expectation | Category | Covered? | Evidence | Evidence Type | Gap / Follow-up | Parity/API/Deferred |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| Passive User Resource | Resource/Unit | ✓ | `UserMigrationTest::testUserResourceIsIndependentOfLegacyModels` | Test | No legacy model extension via reflection test | Parity |
| Remove `UserInterface` | Migration | ✓ | `UserMigrationTest::testUserInterfaceIsRemoved` | Test | No `UserInterface.php` | Parity |
| User Accessors (IDs, Dates) | Resource/Unit | ✓ | `UserTest::testUserResourceAccessors` | Test | IDs are `string` in Resource | Parity |
| User Status Enum | Enum | ✓ | `UserStatus` class | Source | Covered by hydration and accessor tests | Parity |
| Scalar to Enum Hydration | Hydrator | ✓ | `UserTest::testUserHydrationFromScalarStatus` | Test | None | Parity |
| Legacy Model Independence | Resource/Unit | ✓ | `UserMigrationTest::testUserResourceIsIndependentOfLegacyModels` | Test | No legacy model usage via reflection test | Parity |
| User Date fields use `DateTimeImmutable` | Resource/Unit | ✓ | `UserTest::testUserHydrationFromScalarStatus` | Test | Verified via hydration and property tests | Parity |
| User status scalar hydration tested | Hydrator | ✓ | `UserTest::testUserHydrationFromScalarStatus` | Test | None | Parity |
| User `created_at` / `modified_at` hydration tested | Hydrator | ✓ | `UserTest::testUserHydrationFromScalarStatus` | Test | None | Parity |
| snake_case to camelCase hydration tested | Hydrator | ✓ | `UserTest::testUserSnakeCaseHydration` | Test | None | Parity |
| Nullable/default field behavior tested | Resource/Unit | ✓ | `UserTest::testUserResourceIsNullSafe` | Test | None | Parity |
| Type defaults to `user` | Resource/Unit | ✓ | `UserTest::testUserResourceTypeDefault` | Test | None | Parity |
| `UserService::getCurrentUser` | Service | ✓ | `UserServiceTest::testGetCurrentUserReturnsUserResource` | Test | None | Parity |
| `UserService::getUser` (by ID) | Service | ✓ | `UserServiceTest::testGetUserByIdReturnsUserResource` | Test | None | Parity |
| User Service returns concrete V1 resources | Service | ✓ | `UserServiceTest::testGetUserByIdReturnsUserResource` | Test | None | Parity |
| User Service does not type against `UserInterface` | Service | ✓ | `UserServiceTest::testServiceDoesNotDependOnLegacyUserModel` | Test | Verified via static reflection check | Parity |
| User Service does not depend on legacy User model | Service | ✓ | `UserServiceTest::testServiceDoesNotDependOnLegacyUserModel` | Test | Verified isolation via static check | Parity |
| Enterprise User Endpoints | Service | N | API Coverage Audit | Documentation | Classified as Deferred | Deferred |

### User migration follow-up test tasks (Complete)

- ✓ **Add or confirm reflection/static test that `Box\Resource\User` does not extend legacy model classes**: Implemented in `UserMigrationTest`.
- ✓ **Add or confirm reflection/static test that `Box\Resource\User` does not implement removed User interfaces**: Implemented in `UserMigrationTest`.
- ✓ **Add or confirm static/search check for no `UserInterface` references**: Verified removal; remaining references are in unrelated interfaces or documentation.
- ✓ **Add or confirm User service tests assert concrete `Box\Resource\User` return type for current-user and get-user-by-ID**: Implemented in `UserServiceTest`.
- ✓ **Add or confirm User service tests assert no legacy User model dependency**: Implemented in `UserServiceTest`.
- ✓ **V1 ID Typing Check**: Verified in `UserServiceTest` and `UserTest`.
- **Document deferred broader User API endpoint coverage**: Ensure `v1-api-coverage-audit.md` explicitly lists which User endpoints (like Enterprise, Invite, etc.) are deferred.

## Phase-by-phase test matrix

### 1. Group and GroupMembership migration
- **Goal**: Migrate Groups and fold in GroupMemberships to V1.
- **Primary units under test**: `Box\Resource\Group`, `Box\Resource\GroupMembership`, `Box\Service\GroupService`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors (ID, name, group_type), nested User hydration in memberships.
- **Migration parity service tests needed**: retrieval/list/create/update/delete behavior where in scope for parity.
- **Box API coverage service tests needed**: in-scope Group and GroupMembership operations per API coverage audit.
- **Hydrator/mapper tests needed**: Nested User resource hydration within GroupMembership.
- **DTO/enum tests needed**: `GroupType` enum.
- **Migration/compatibility tests needed**: Removal of `GroupInterface`, `GroupMembershipInterface`.
- **Negative/error tests needed**: Group not found, membership already exists.
- **Documentation checks**: Update migration guide and API coverage audit for Groups.
- **Deferred API coverage**: Enterprise-level group settings or any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: All Group/Membership logic moved to V1, legacy models deprecated, parity coverage for in-scope legacy behavior.

### 2. Shared DTO foundation
- **Goal**: Establish common DTOs used by multiple resources.
- **Primary units under test**: `SharedLink`, `Permissions`, `PathCollection`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: N/A (mostly DTOs).
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: N/A.
- **Hydrator/mapper tests needed**: Nested DTO hydration (e.g., SharedLink within File).
- **DTO/enum tests needed**: `SharedLinkAccess` enum, `Permissions` fields.
- **Migration/compatibility tests needed**: Check consumers (File/Folder) use new DTOs.
- **Negative/error tests needed**: Invalid date format for `unshared_at`.
- **Documentation checks**: Document DTO usage patterns in architecture rules. Consuming resource phases must test DTO integration.
- **Deferred API coverage**: N/A.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Foundation DTOs present and used by Hydrator.

### 3. File and FileVersion migration
- **Goal**: Migrate Files and Versions to V1.
- **Primary units under test**: `Box\Resource\File`, `Box\Resource\FileVersion`, `Box\Service\FileService`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors, version collection hydration.
- **Migration parity service tests needed**: retrieval/upload/update/delete/copy behavior where in scope for parity.
- **Box API coverage service tests needed**: Version management and chunked upload (if in scope per API coverage audit).
- **Hydrator/mapper tests needed**: Collection of `FileVersion` hydration within `File`.
- **DTO/enum tests needed**: File-related enums.
- **Migration/compatibility tests needed**: Removal of `FileInterface`, `FileVersionInterface`.
- **Negative/error tests needed**: File lock conflicts, version not found.
- **Documentation checks**: Update migration guide and API coverage audit for Files.
- **Deferred API coverage**: Any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Files and Versions fully migrated, legacy models deprecated.

### 4. Folder and FolderItems migration
- **Goal**: Migrate Folders and List Items to V1.
- **Primary units under test**: `Box\Resource\Folder`, `Box\Dto\Response\FolderItemsResponse`, `Box\Service\FolderService`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors, items collection hydration.
- **Migration parity service tests needed**: retrieval/list/create/update/delete/copy behavior where in scope for parity.
- **Box API coverage service tests needed**: Pagination (offset/limit/marker) and mixed item hydration coverage.
- **Hydrator/mapper tests needed**: Polymorphic collection hydration (File/Folder/WebLink).
- **DTO/enum tests needed**: `FolderItemsResponse` structure.
- **Migration/compatibility tests needed**: Removal of `FolderInterface`.
- **Negative/error tests needed**: Folder not empty on delete.
- **Documentation checks**: Update migration guide and API coverage audit for Folders.
- **Deferred API coverage**: Any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Folders fully migrated, legacy models deprecated.

### 5. Collaboration migration
- **Goal**: Migrate Collaborations to V1.
- **Primary units under test**: `Box\Resource\Collaboration`, `Box\Service\CollaborationService`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors, role enum.
- **Migration parity service tests needed**: creation/retrieval/update/delete behavior where in scope for parity.
- **Box API coverage service tests needed**: Pending/Accepted status handling per API coverage audit.
- **Hydrator/mapper tests needed**: Nested "accessible_item" hydration.
- **DTO/enum tests needed**: `CollaborationRole` enum tests.
- **Migration/compatibility tests needed**: Removal of `CollaborationInterface`.
- **Negative/error tests needed**: User already collaborated.
- **Documentation checks**: Update migration guide and API coverage audit.
- **Deferred API coverage**: Any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Collaborations fully migrated.

### 6. Event migration
- **Goal**: Migrate Events to V1.
- **Primary units under test**: `Box\Resource\Event`, `Box\Service\EventService`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors, source object hydration.
- **Migration parity service tests needed**: User and Enterprise event retrieval where parity exists.
- **Box API coverage service tests needed**: Stream-position and event-source coverage; long polling (only if in scope per API coverage audit).
- **Hydrator/mapper tests needed**: Polymorphic "source" object hydration.
- **DTO/enum tests needed**: Event type enums.
- **Migration/compatibility tests needed**: Removal of `EventInterface`.
- **Negative/error tests needed**: Invalid stream position.
- **Documentation checks**: Update migration guide and API coverage audit.
- **Deferred API coverage**: Real-time webhooks (separate phase) or any endpoints not implemented.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Events fully migrated.

### 7. Client facade refactor
- **Goal**: Refactor `Client` to a lightweight facade delegating to services.
- **Primary units under test**: `Box\Client`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: N/A.
- **Migration parity service tests needed**: existing public Client capabilities that are intentionally preserved.
- **Box API coverage service tests needed**: N/A (API expansion belongs in focused services, not directly in the facade).
- **Hydrator/mapper tests needed**: N/A.
- **DTO/enum tests needed**: N/A.
- **Migration/compatibility tests needed**: Verify no legacy models are returned.
- **Negative/error tests needed**: Auth failures handled by facade.
- **Documentation checks**: Update README with new Client usage examples.
- **Deferred API coverage**: N/A.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: `Client` is thin, all logic in services.

### 8. Service interface and factory cleanup
- **Goal**: Remove redundant factories and clean up service interfaces.
- **Primary units under test**: `Box\Service\*`, `Box\Factory\*`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: N/A.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: N/A.
- **Hydrator/mapper tests needed**: N/A.
- **DTO/enum tests needed**: N/A.
- **Migration/compatibility tests needed**: Removal of legacy factories (e.g., UserFactory, FileFactory).
- **Negative/error tests needed**: N/A.
- **Documentation checks**: Update architecture rules regarding service instantiation; emphasize static analysis and stale reference checks.
- **Deferred API coverage**: N/A.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Factories removed, services directly instantiable.

### 9. Model abstraction removal
- **Goal**: Remove legacy model base classes and traits.
- **Primary units under test**: `Box\Model\BaseModel`, `Box\Model\ModelTrait`, etc.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: N/A.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: N/A.
- **Hydrator/mapper tests needed**: N/A.
- **DTO/enum tests needed**: N/A.
- **Migration/compatibility tests needed**: Verify all resources still pass tests without these bases.
- **Negative/error tests needed**: N/A.
- **Documentation checks**: Update migration guide and remove stale class/interface references.
- **Deferred API coverage**: N/A.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Legacy model core deleted.

### 10. Box\Model namespace removal
- **Goal**: Final deletion of the legacy `Box\Model` namespace.
- **Primary units under test**: `Box\Model\*`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: N/A.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: N/A.
- **Hydrator/mapper tests needed**: N/A.
- **DTO/enum tests needed**: N/A.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: N/A.
- **Documentation checks**: Update migration guide and remove stale namespace references.
- **Deferred API coverage**: N/A.
- **Validation commands**: `composer dump-autoload` and Standard V1 validation commands.
- **Done criteria**: `src/Model` directory deleted and autoload validated.

### 11. Deferred missing resource phase: Comments and Tasks
- **Goal**: Implement Comments and Tasks (missing in legacy SDK).
- **Primary units under test**: `Box\Resource\Comment`, `Box\Resource\Task`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors for new resources.
- **Migration parity service tests needed**: N/A (New).
- **Box API coverage service tests needed**: in-scope Comment and Task operations as defined by the API coverage audit.
- **Hydrator/mapper tests needed**: Standard hydration, date/enum hydration, and nested objects.
- **DTO/enum tests needed**: Request DTOs and task resolution enums.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: Comment on non-existent file.
- **Documentation checks**: Update API coverage audit and migration guide.
- **Deferred API coverage**: Any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Comments and Tasks available in V1.

### 12. Deferred missing resource phase: Metadata service
- **Goal**: Implement full Metadata service.
- **Primary units under test**: `Box\Service\MetadataService`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: `MetadataTemplate` resource.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: in-scope Template and Instance management as defined by the API coverage audit.
- **Hydrator/mapper tests needed**: Dynamic metadata attribute hydration.
- **DTO/enum tests needed**: Request DTOs and list responses.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: Metadata template already exists.
- **Documentation checks**: Update API coverage audit and migration guide.
- **Deferred API coverage**: Any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Metadata service available.

### 13. Deferred missing resource phase: Collections, File Requests
- **Goal**: Implement remaining high-priority API resources.
- **Primary units under test**: `Box\Resource\FileRequest`, etc. (Sign Requests and Webhooks deferred to v1.1.0).
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Passive accessors for each.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: in-scope operations for each resource as defined by the API coverage audit.
- **Direct Transport Fallback**: Verify direct transport can be used for Sign Requests and Webhooks.
- **Hydrator/mapper tests needed**: Standard hydration.
- **DTO/enum tests needed**: Request DTOs and list responses with pagination.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: Invalid request parameters.
- **Documentation checks**: Update API coverage audit and migration guide.
- **Deferred API coverage**: Sign Requests, Webhooks, and any endpoints not implemented.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Resources available in V1.

### 14. Deferred enterprise/governance phase
- **Goal**: Implement Enterprise and Governance features.
- **Primary units under test**: `RetentionPolicy`, `LegalHold`.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: Policy resources.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: selected governance lifecycle operations as defined by the API coverage audit.
- **Hydrator/mapper tests needed**: Standard hydration and nested objects.
- **DTO/enum tests needed**: Disposition action enums, request DTOs, and pagination metadata.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: Policy already retired.
- **Documentation checks**: Update API coverage audit and migration guide.
- **Deferred API coverage**: Any endpoints not implemented in this phase must be listed in the API coverage audit as deferred.
- **Validation commands**: Standard V1 validation commands.
- **Done criteria**: Governance features available.

### 15. V1 upgrade documentation
- **Goal**: Finalize migration guide and documentation.
- **Primary units under test**: Documentation files.
- **Current coverage status**: In progress.
- **Resource/unit tests needed**: N/A.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: N/A.
- **Hydrator/mapper tests needed**: N/A.
- **DTO/enum tests needed**: N/A.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: N/A.
- **Documentation checks**: Peer review of migration guide and final audit alignment.
- **Deferred API coverage**: N/A.
- **Validation commands**: For documentation-only edits, perform manual Markdown review and run any repository-required documentation checks if available. If full Composer validation is skipped, note that explicitly in the final summary.
- **Done criteria**: Documentation complete and accurate.

### 16. Final quality and release review
- **Goal**: Final verification of V1.0 release readiness.
- **Primary units under test**: Whole project.
- **Current coverage status**: Not started.
- **Resource/unit tests needed**: N/A.
- **Migration parity service tests needed**: N/A.
- **Box API coverage service tests needed**: N/A.
- **Hydrator/mapper tests needed**: N/A.
- **DTO/enum tests needed**: N/A.
- **Migration/compatibility tests needed**: N/A.
- **Negative/error tests needed**: N/A.
- **Documentation checks**: Full doc audit and checklist completion verification.
- **Deferred API coverage**: N/A.
- **Validation commands**: Standard V1 validation commands and `composer review`.
- **Done criteria**: All checks pass, SDK ready for V1.0 tag.

## Validation command policy

All V1 implementation and testing work must be validated using the Standard V1 validation commands:

- `composer dump-autoload`
- `composer test`
- `composer analyse`
- `composer cs:check`

If style issues are found, run `composer cs:fix` followed by `composer cs:check`. Do not use ad hoc tool commands.

### Documentation-only changes
For documentation-only edits:
- At minimum perform a manual Markdown review and run any repository-required documentation checks if available.
- If full Composer validation is skipped for docs-only edits, note that explicitly in the final summary.

Do not recommend replacing Composer scripts with ad hoc direct tool commands.
