# V1.0 Implementation Checklist

This document tracks the implementation progress of the V1.0 architecture refactor.

This checklist is the operational progress tracker. Architectural decisions belong in `v1-architecture-rules.md`; audit details belong in `v1-interface-and-model-audit.md`; API coverage details belong in `v1-api-coverage-audit.md`.

## Status Markers

- Not started
- In progress
- Completed
- Blocked
- Deferred

## Validation Policy

For implementation steps, use Composer scripts for validation:

- `composer dump-autoload`
- `composer test`
- `composer analyse`
- `composer cs:check`

If code style violations are automatically fixable, run:

- `composer cs:fix`
- `composer cs:check`

Do not use ad hoc `vendor/bin/phpcs --standard=PSR12 ...` commands as a replacement for `composer cs:check`.

## 1. Planning and Documentation Checkpoint

- [x] Status: Completed
- Goal: Establish source of truth for V1.0 architecture, migration strategy, and test coverage.
- Scope: Create and align architecture rules, interface audits, API coverage audits, and test coverage plan.
- Dependencies: None.
- Validation: All planning docs present and consistent in `docs/v1-planning/`.
- Documentation updates: `ai-assistant-planning-context.md`, `v1-architecture-rules.md`, `v1-interface-and-model-audit.md`, `v1-api-coverage-audit.md`, `v1-test-coverage-plan.md`, `v1-strategy-and-contracts.md` (Hardened strategy), `v1-decision-index.md`.
- Test updates: N/A.
- Completion notes: Planning docs are finalized and represent the starting state for the refactor. Added `v1-test-coverage-plan.md` to define expected parity and API coverage tests. Hardened v1.0 strategy and contracts in `v1-strategy-and-contracts.md` covering glossary, boundaries, retry, logging, and direct transport. Reconciled all planning docs with the hardened strategy and created `v1-decision-index.md`.

## 2. Foundation Namespace Skeleton

- [x] Status: Completed
- Goal: Create the directory structure for V1.0 namespaces.
- Scope: Create `Resource`, `Dto`, `Enum`, `Contract`, `Http`, `Mapper`, `Service` under `src/`. Establish `src/Dto` as the casing standard.
- Dependencies: 1.
- Validation: Directories exist; `composer dump-autoload` passes.
- Documentation updates: This checklist.
- Test updates: N/A.
- Completion notes: Skeleton directories created. `src/DTO` was removed and all references normalized to `src/Dto` / `Box\Dto`. Added `.gitkeep` to empty `Resource` and `Enum` directories.

## 3. Hydrator/Mapper Plain-Object Readiness

- [x] Status: Completed
- Goal: Ensure the Hydrator can populate concrete resource objects without requiring legacy model traits or interfaces.
- Scope: Update `Mapper\Hydrator` to handle hydration of passive objects.
- Dependencies: 2.
- Validation: Unit tests for Hydrator with non-model-based objects.
- Documentation updates: `v1-architecture-rules.md` (Hydration section).
- Test updates: `HydratorTest`, `HydratorV1ReadinessTest`.
- Completion notes: Verified that `Hydrator` is independent of legacy `Model` abstractions. Added support for `DateTimeImmutable` hydration from strings. Verified hydration of plain objects via setters and public properties, nested objects, and collection item type inference via PHPDoc. No blockers found for User resource migration.

## 4. Core Enum Foundation

- [x] Status: Completed
- Goal: Implement first set of PHP 8.4 enums for common Box API values.
- Scope: `BoxItemType`, `CollaborationRole`, `UserStatus`, `SharedLinkAccess`.
- Dependencies: 2.
- Validation: `composer analyse`, `composer test`.
- Documentation updates: None.
- Test updates: Added `tests/Enum/EnumTest.php`.
- Completion notes: Created `BoxItemType`, `UserStatus`, `CollaborationRole`, and `SharedLinkAccess` backed enums. Verified against Box API reference. Unblocks Step 5 (User Resource Migration).

## 5. User Resource Migration

- [x] Status: Completed
- Goal: Migrate Users to V1.0 architecture as a pilot resource.
- Scope: Move `Box\User\User` to `Box\Resource\User`, remove `UserInterface`, update `UserService`.
- Dependencies: 3, 4.
- Validation: `composer test`, `composer analyse`.
- Documentation updates: Migration notes for Users.
- Test updates: `UserTest`.
- Completion notes: Implemented `Box\Resource\User` as a passive resource. Created `Box\Service\UserService`. Updated `Hydrator` to support Enum hydration. Removed `UserInterface` and updated all internal references. Deprecated legacy `Box\User\User` model.

## 6. PHP 8.4 Modernization: Implicit Nullable Parameters

- [x] Status: Completed
- Goal: Fix PHP 8.4 implicit nullable parameter deprecations.
- Scope: Update signatures to use explicit nullability (`?Type $p = null`).
- Dependencies: 1.
- Validation: `composer review` shows no implicit-nullability deprecations.
- Documentation updates: None.
- Test updates: N/A.
- Completion notes: Fixed 13 occurrences across 9 files including `TokenStorageInterface`, `SharedLinkInterface`, `FileServiceInterface`, `UserEventServiceInterface`, and `TokenStorageException`. Verified with `composer review`.

## 7. V1.0 Foundation Refinement (Strategy Alignment)

- [ ] Status: Not started
- Goal: Implement core foundation services according to hardened v1 strategy.
- Scope:
    - Transport refactor: Public `TransportInterface` with `send()` and `request()` support; PSR-18 integration; support for options (headers, query, json, body, auth, retry).
    - Response Wrapper Replacement: REPLACE the current `BoxResponse` (Symfony-inherited) with a new thin PSR-7 wrapper named `BoxResponse` / `BoxResponseInterface` in `Box\Http`; implement required SDK helpers (`getPsrResponse`, `getStatusCode`, `isSuccessful`, `getHeaders`, `getHeader`, `getHeaderLine`, `hasHeader`, `getBody`, `getContent`, `getRetryAfter`, `json`, etc.); remove legacy Symfony methods.
    - `json()` Helper: Implement in `BoxResponse` with proper error handling (throws on invalid JSON).
    - Auth provider boundary: `AuthProviderInterface` and `TokenStorageInterface` with clear responsibility separation (Storage is passive); define `TokenStorageContext` DTO/value object strategy.
    - Token Storage implementations: Implement or harden `InMemoryTokenStorage` and `PdoTokenStorage` with multiple context support and one active token per context enforcement.
    - Token Storage Security: Ensure NO encryption at rest in core; document security guidance; provide extension points for encrypted storage.
    - PDO Schema: Document required PDO schema and provide optional helper SQL; no framework migration ownership in core.
    - Multi-Token Support: Defer token history and multi-token-per-context features to future integration work.
    - JWT/S2S Auth: Targeted v1.0.0; include feasibility checkpoint after foundation.
    - Exception taxonomy: Base `BoxException` hierarchy (Client, Transport, Api, etc.); redaction of secrets in string output.
    - Logging and Redaction Policy: PSR-3 integration; automatic token/secret redaction in logs and exceptions.
    - Retry and Rate-Limit: Disabled by default; honors `Retry-After`; safe retries only by default.
- Dependencies: 5.
- Validation: `composer test`.
- Documentation updates: Update `v1-architecture-rules.md`.
- Test updates: New foundation tests (Redaction, Retry, Transport, Response Wrapper, JSON helper, Auth workflow).

## 7. Group and Group Membership Migration

- [ ] Status: Not started
- Goal: Migrate Groups and fold in GroupMemberships.
- Scope: Create `Box\Resource\Group` and `Box\Resource\GroupMembership`. Update `GroupService`.
- Dependencies: 5.
- Validation: `composer test`.
- Documentation updates: Migration notes for Groups.
- Test updates: `GroupTest`, `GroupServiceTest`.
- Completion notes: 

## 7. Shared DTO Foundation

- [ ] Status: Not started
- Goal: Create foundation DTOs used across resources.
- Scope: `SharedLink`, `Permissions`, `PathCollection`.
- Dependencies: 3.
- Validation: `composer analyse`.
- Documentation updates: N/A.
- Test updates: DTO unit tests.
- Completion notes: 

## 8. File and FileVersion migration

- [ ] Status: Not started
- Goal: Migrate Files and fold in Versions.
- Scope: `Box\Resource\File`, `Box\Resource\FileVersion`. Update `FileService`.
- Dependencies: 7.
- Validation: `composer test`.
- Documentation updates: Migration notes for Files.
- Test updates: `FileTest`, `FileServiceTest`.
- Completion notes: 

## 9. Folder and FolderItems migration

- [ ] Status: Not started
- Goal: Migrate Folders and List Items response.
- Scope: `Box\Resource\Folder`, `Box\Dto\Response\FolderItemsResponse`. Update `FolderService`.
- Dependencies: 8.
- Validation: `composer test`.
- Documentation updates: Migration notes for Folders.
- Test updates: `FolderTest`, `FolderServiceTest`.
- Completion notes: 

## 10. Collaboration migration

- [ ] Status: Not started
- Goal: Migrate Collaborations.
- Scope: `Box\Resource\Collaboration`. Update `CollaborationService`.
- Dependencies: 8, 9.
- Validation: `composer test`.
- Documentation updates: Migration notes for Collaborations.
- Test updates: `CollaborationTest`, `CollaborationServiceTest`.
- Completion notes: 

## 11. Event migration

- [ ] Status: Not started
- Goal: Migrate Events.
- Scope: `Box\Resource\Event`. Update `EventService`.
- Dependencies: 10.
- Validation: `composer test`.
- Documentation updates: Migration notes for Events.
- Test updates: `EventTest`, `EventServiceTest`.
- Completion notes: 

## 12. Client facade refactor

- [ ] Status: Not started
- Goal: Refactor `Client` to a lightweight facade.
- Scope: Update `Box\Client` to use new services and return concrete resources.
- Dependencies: 11.
- Validation: `composer test`.
- Documentation updates: README, Migration Guide.
- Test updates: `ClientTest`.
- Completion notes: 

## 13. Service interface and factory cleanup

- [ ] Status: Not started
- Goal: Clean up service interfaces and remove redundant factories.
- Scope: Refactor `src/Service` and `src/Factory`.
- Dependencies: 12.
- Validation: `composer analyse`.
- Documentation updates: N/A.
- Test updates: N/A.
- Completion notes: 

## 14. Model abstraction removal

- [ ] Status: Not started
- Goal: Remove legacy model traits and base classes.
- Scope: Delete `ModelTrait`, `BoxModelTrait`, `BaseModelTrait`, `BaseModel`, `Model`, `BoxModel`.
- Dependencies: 13.
- Validation: `composer test`.
- Documentation updates: N/A.
- Test updates: Remove legacy model tests.
- Completion notes: 

## 15. Box\Model namespace removal

- [ ] Status: Not started
- Goal: Complete removal of `src/Model` directory.
- Scope: Delete `src/Model` and all remaining files.
- Dependencies: 14.
- Validation: `composer dump-autoload`.
- Documentation updates: N/A.
- Test updates: N/A.
- Completion notes: 

## 16. Deferred missing resource phase: Comments and Tasks

- [ ] Status: Not started
- Goal: Implement missing Comments and Tasks resources.
- Scope: `Box\Resource\Comment`, `Box\Resource\Task`.
- Dependencies: 15.
- Validation: `composer test`.
- Documentation updates: API coverage update.
- Test updates: New tests for Comments/Tasks.
- Completion notes: 

## 17. Deferred missing resource phase: Metadata service

- [ ] Status: Not started
- Goal: Implement full Metadata service.
- Scope: `MetadataService`, Template management.
- Dependencies: 15.
- Validation: `composer test`.
- Documentation updates: API coverage update.
- Test updates: New tests for Metadata.
- Completion notes: 

## 18. Deferred missing resource phase: Collections, File Requests

- [ ] Status: Not started
- Goal: Implement remaining high-priority API resources.
- Scope: File Requests, etc. (Sign Requests and Webhooks deferred to v1.1.0; use direct transport fallback).
- Dependencies: 15.
- Validation: `composer test`.
- Documentation updates: API coverage update.
- Test updates: New tests.
- Completion notes: 

## 19. Deferred enterprise/governance phase

- [ ] Status: Not started
- Goal: Implement Enterprise features (Shield, Governance).
- Scope: Retention Policies, Legal Holds, etc.
- Dependencies: 18.
- Validation: `composer test`.
- Documentation updates: API coverage update.
- Test updates: New tests.
- Completion notes: 

## 20. V1 upgrade documentation

- [ ] Status: Not started
- Goal: Finalize migration guide for end users.
- Scope: Complete `docs/v1-migration.md`.
- Dependencies: 19.
- Validation: Manual review.
- Documentation updates: `docs/v1-migration.md`.
- Test updates: N/A.
- Completion notes: 

## 21. Final quality and release review

- [ ] Status: Not started
- Goal: Final verification of V1.0 release readiness.
- Scope: `composer review`, full test suite, doc audit, and package/repository rename.
- Dependencies: 20.
- Validation: `composer review` passes.
- Documentation updates: CHANGELOG.md, `v1-package-rename-plan.md`.
- Test updates: N/A.
- Completion notes: 
