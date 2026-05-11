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
- Validation: All planning docs present and consistent in current directory.
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

- [x] Status: Completed
- Goal: Implement core foundation services according to hardened v1 strategy.
- Scope:
    - [x] Transport refactor: Public `TransportInterface` with `send()` and `request()` support; PSR-18 integration; support for options (headers, query, json, body, auth, retry).
    - [x] Response Wrapper Replacement: REPLACE the current `BoxResponse` (Symfony-inherited) with a new thin PSR-7 wrapper named `BoxResponse` / `BoxResponseInterface` in `Box\Http`; implement required SDK helpers (`getPsrResponse`, `getStatusCode`, `isSuccessful`, `getHeaders`, `getHeader`, `getHeaderLine`, `hasHeader`, `getBody`, `getContent`, `getRetryAfter`, `json`, etc.); remove legacy Symfony methods.
    - [x] `json()` Helper: Implement in `BoxResponse` with proper error handling (throws on invalid JSON).
    - [x] Auth provider boundary: `AuthProviderInterface` and `TokenStorageInterface` with clear responsibility separation (Storage is passive); define `TokenStorageContext` DTO/value object strategy.
    - [x] Token Storage implementations: Implement or harden `InMemoryTokenStorage` and `PdoTokenStorage` with multiple context support and one active token per context enforcement.
    - [x] Token Storage Security: Ensure NO encryption at rest in core; document security guidance; provide extension points for encrypted storage.
    - [x] PDO Schema: Document required PDO schema and provide optional helper SQL; no framework migration ownership in core.
    - [x] Multi-Token Support: Defer token history and multi-token-per-context features to future integration work.
    - [x] JWT/S2S Auth: Targeted v1.0.0; include feasibility checkpoint after foundation.
    - [x] Exception taxonomy: Base `BoxException` hierarchy (Client, Transport, Api, etc.); redaction of secrets in string output.
    - [x] Logging and Redaction Policy: PSR-3 integration; automatic token/secret redaction in logs and exceptions.
    - [x] Retry and Rate-Limit: Disabled by default; honors `Retry-After`; safe retries only by default.
    - [x] CLI Validation: Validate existing commands after v1 service/transport/auth refactors; ensure CLI output uses redaction rules; verify CLI docs for changed behavior.
    - [x] JWT/S2S CLI Testing: Support CLI-based JWT/S2S testing if practical (linked to JWT auth implementation).
- Dependencies: 5.
- Validation: `composer test`.
- Documentation updates: Update `v1-architecture-rules.md`.
- Test updates: New foundation tests (Redaction, Retry, Transport, Response Wrapper, JSON helper, Auth workflow).
- Completion notes: Foundation Refinement completed. Transport, Response, and Auth boundaries hardened. CLI validation performed. JWT/S2S feasibility checkpoint identified as Step 14.

## 8. Group and Group Membership Migration

- [x] Status: Completed
- Goal: Migrate Groups and fold in GroupMemberships.
- Scope: Create `Box\Resource\Group` and `Box\Resource\GroupMembership`. Update `GroupService`.
- Dependencies: 5.
- Validation: `composer test`.
- Documentation updates: Migration notes for Groups.
- Test updates: `GroupTest`, `GroupServiceTest`.
- Completion notes: Groups and memberships migrated to v1 resource/service pattern.

## 9. Shared DTO Foundation

- [x] Status: Completed
- Goal: Create foundation DTOs used across resources.
- Scope: `SharedLink`, `Permissions`, `PathCollection`.
- Dependencies: 3.
- Validation: `composer analyse`.
- Documentation updates: N/A.
- Test updates: DTO unit tests.
- Completion notes: Shared DTOs implemented and used across resources.

## 10. File and FileVersion migration

- [x] Status: Completed
- Goal: Migrate Files and fold in Versions.
- Scope: `Box\Resource\File`, `Box\Resource\FileVersion`. Update `FileService`.
- Dependencies: 7.
- Validation: `composer test`.
- Documentation updates: Migration notes for Files.
- Test updates: `FileTest`, `FileServiceTest`.
- Completion notes: Files and versions migrated.

## 11. Folder and FolderItems migration

- [x] Status: Completed
- Goal: Migrate Folders and List Items response.
- Scope: `Box\Resource\Folder`, `Box\Dto\Response\FolderItemsResponse`. Update `FolderService`.
- Dependencies: 8.
- Validation: `composer test`.
- Documentation updates: Migration notes for Folders.
- Test updates: `FolderTest`, `FolderServiceTest`.
- Completion notes: Folders and item collections migrated.

## 12. Collaboration migration

- [x] Status: Completed
- Goal: Migrate Collaborations.
- Scope: `Box\Resource\Collaboration`. Update `CollaborationService`.
- Dependencies: 8, 9.
- Validation: `composer test`.
- Documentation updates: Migration notes for Collaborations.
- Test updates: `CollaborationTest`, `CollaborationServiceTest`.
- Completion notes: Collaborations migrated.

## 13. Event migration

- [x] Status: Completed
- Goal: Migrate Events.
- Scope: `Box\Resource\Event`. Update `EventService`.
- Dependencies: 10.
- Validation: `composer test`.
- Documentation updates: Migration notes for Events.
- Test updates: `EventTest`, `EventServiceTest`.
- Completion notes: Event resources and service implemented.

## 14. Client facade refactor

- [x] Status: Completed
- Goal: Refactor `Client` to a lightweight facade.
- Scope: Update `Box\Client` to use new services and return concrete resources.
- Dependencies: 11.
- Validation: `composer test`.
- Documentation updates: README, Migration Guide.
- Test updates: `ClientTest`.
- Completion notes: Client refactored to delegate to registry-managed services.

## 15. Service interface and factory cleanup

- [x] Status: Completed
- Goal: Clean up service interfaces and remove redundant factories.
- Scope: Refactor `src/Service` and `src/Factory`.
- Dependencies: 12.
- Validation: `composer analyse`.
- Documentation updates: N/A.
- Test updates: N/A.
- Completion notes: Factory modernization complete (Step 11 of main tracker). ConnectionFactory canonicalized.

## 16. Model abstraction removal

- [x] Status: Completed
- Goal: Remove legacy model traits and base classes.
- Scope: Delete `ModelTrait`, `BoxModelTrait`, `BaseModelTrait`, `BaseModel`, `Model`, `BoxModel`.
- Dependencies: 13.
- Validation: `composer test`.
- Documentation updates: N/A.
- Test updates: Remove legacy model tests.
- Completion notes: Legacy Model architecture removed (Step 9 of main tracker).

## 17. Box\Model namespace removal

- [x] Status: Completed
- Goal: Complete removal of `src/Model` directory.
- Scope: Delete `src/Model` and all remaining files.
- Dependencies: 14.
- Validation: `composer dump-autoload`.
- Documentation updates: N/A.
- Test updates: N/A.
- Completion notes: `src/Model` directory removed.

## 18. Token Storage Completion and Integration

- [ ] Status: Not started
- Goal: Audit and finalize token storage behavior for v1.
- Scope:
    - Finalize `TokenStorageInterface` for v1.
    - Implement/complete `InMemoryTokenStorage` and `PdoTokenStorage`; evaluate `FilesystemTokenStorage`.
    - **Passive Storage**: Ensure storage does not make network calls or contain refresh logic.
    - **Integration**: Implement Client-level orchestration for loading/persisting tokens.
    - **Independence**: Verify services remain storage-independent.
    - **CLI/Harness**: Review CLI token storage configuration. Confirm CLI can run without storage. Define fallback behavior for resource commands. Define storage behavior for auth exchange and refresh. Preserve redaction/masking.
    - **Deferred JWT/S2S CLI configuration note**: When JWT/S2S auth is implemented, evaluate whether the CLI/auth harness should support separate environment-variable groups or named auth profiles for OAuth2 versus JWT credentials. This would allow CLI testing of JWT and OAuth2 without manually swapping shared `BOX_CLIENT_ID` / `BOX_CLIENT_SECRET` values and reduce the risk of mismatched credential pairs or accidentally combining OAuth2 and JWT configuration. Do not implement this during Step 12 token storage unless a later approved plan explicitly includes CLI auth profile work.
- Dependencies: 17.
- Validation: `composer test`, `composer analyse`.
- Documentation updates: Architecture rules, strategy, and user docs.
- Test updates: Focused storage and orchestration tests.
- Completion notes: Corresponds to Step 12 in `10-v1-release-work.md`.

## 18.1 Auth Lifecycle Extraction

- [ ] Status: Not started
- Goal: Extract auth lifecycle management into a dedicated provider layer.
- Scope:
    - Create `AuthProvider` / `AuthLifecycle` component.
    - Move token exchange/refresh/revoke logic behind the auth boundary.
    - Coordinate Client + Auth + Storage orchestration.
- Dependencies: 18.
- Completion notes: Required for v1 release.

## 18.2 JWT/S2S Auth Foundation and Implementation

- [ ] Status: Not started
- Goal: Implement JWT/Server-to-Server authentication (Required for v1).
- Scope: Feasibility study, JWT signing, token exchange, Client/Connection integration, and CLI/harness support.
- Dependencies: 18.1.
- Validation: `composer test`, `composer analyse`.
- Documentation updates: JWT usage guide and migration notes.
- Test updates: Signing and exchange tests with placeholder fixtures.
- Completion notes: Required for v1 release. Corresponds to Steps 14-15 in `10-v1-release-work.md`.

## 19. Box API Coverage Alignment

- [ ] Status: Not started
- Goal: Audit SDK against Box API to ensure high value per core resource.
- Scope:
    - Audit core services (Files, Folders, Users, Groups, Collabs, Events).
    - Align with basic Box API CRUD operations.
    - Produce coverage matrix.
- Dependencies: 18.2.
- Completion notes: Corresponds to Step 15.1 in `10-v1-release-work.md`.

## 20. Evaluation Phase: Missing Resources and Webhooks

- [ ] Status: Not started
- Goal: Evaluate and potentially implement remaining resources.
- Scope:
    - **Comments and Tasks**: `Box\Resource\Comment`, `Box\Resource\Task`.
    - **Metadata Service**: Template management.
    - **Webhooks**: Signature verification (Required) and CRUD management (Evaluation).
- Dependencies: 19.
- Completion notes: Corresponds to Step 16 and deferred resource steps.
