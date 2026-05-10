# Service Layer Hardening

Roadmap reference: v1 Step 8

## Purpose
The **Service Layer Hardening** initiative aims to stabilize and modernize the SDK's service and resource layer, building on the foundation established in Step 7. This involves clarifying service contracts, standardizing response handling, centralizing hydration/mapping logic, and improving type safety across resource-specific services.

## Scope Statement
This initiative covers:
- Inventorying and auditing existing service contracts and patterns.
- Stabilizing the `Base Service` contract to use the refined foundation consistently.
- Standardizing hydration and mapper boundaries to reduce duplicated logic.
- Migrating representative read and write services to hardened patterns.
- Ensuring consistent error and retry semantics across services.
- Improving type safety in touched service and resource areas.
- Updating documentation and migration guides for service usage.

## Non-goals
- Rewriting all resource models or all services in one pass.
- Broadly rewriting the core hydration/mapping engine (beyond usage boundaries).
- Implementing new major features like JWT/S2S auth (unless assigned here).
- Performing broad, unrelated PHPStan cleanup.
- Changing the underlying transport/connection foundation established in Step 7.

## Compatibility Constraints
- Preserve behavior during individual slices unless the slice explicitly removes pre-v1 legacy architecture/API as part of the v1 cutover.
- Pre-v1 legacy removals must be intentional, tested, and documented.
- Removals must include migration guidance that identifies the v1 replacement or explains that there is no direct replacement.
- Avoid introducing fluent setter chains in services or models.
- Use **Service Layer Hardening** as the human-readable initiative name.
- Retain **v1 Step 8** as a roadmap reference for traceability.

## Dependencies on Foundation Refinement
- Relies on the PSR-7-backed `BoxResponseInterface`.
- Relies on the refined exception taxonomy (`ApiException`, `BoxResponseException`).
- Relies on the hardened auth and transport layers.

## Status Table

| Slice | Name | Status |
|---|---|---|
| 0 | [Tracker](#service-layer-hardening-slice-0---tracker) | Completed |
| 1 | [Service Inventory and Legacy Architecture Removal Audit](#service-layer-hardening-slice-1---service-inventory-and-legacy-architecture-removal-audit) | Completed |
| 2 | [Base Service Contract Stabilization](#service-layer-hardening-slice-2---base-service-contract-stabilization) | Completed |
| 3 | [Hydration and Mapper Boundary](#service-layer-hardening-slice-3---hydration-and-mapper-boundary) | Completed |
| 4 | [Representative Read Service Migration](#service-layer-hardening-slice-4---representative-read-service-migration) | Completed |
| 5 | [Representative Write/Update Service Migration](#service-layer-hardening-slice-5---representative-writeupdate-service-migration) | Completed |
| 6 | [File/Upload Service Compatibility Pass](#service-layer-hardening-slice-6---fileupload-service-compatibility-pass) | Completed ✓ |
| 7 | [Service Error and Retry Semantics](#service-layer-hardening-slice-7---service-error-and-retry-semantics) | Completed ✓ |
| 8 | [Legacy Architecture Removal and Cutover Planning](#service-layer-hardening-slice-8---legacy-architecture-removal-and-cutover-planning) | Completed ✓ |
| 9 | [Service Documentation and Migration Drift Pass](#service-layer-hardening-slice-9---service-documentation-and-migration-drift-pass) | Completed ✓ |
| 10 | [Service Type-Safety Cleanup](#service-layer-hardening-slice-10---service-type-safety-cleanup) | Completed ✓ |
| 11 | [Final Integration Review](#service-layer-hardening-slice-11---final-integration-review) | |

---

## Dependencies between slices
- Slice 0 is the prerequisite for all other slices.
- Slice 1 provides the audit data for Slices 2-6.
- Slice 2 is a prerequisite for Slices 3-7.
- Slices 4 and 5 serve as templates for other service migrations.
- Slice 8 and 9 depend on the implementation slices.
- Slice 11 is the final step.

---

## Service Layer Hardening Slice 0 — Tracker

**Purpose**: Create this tracker.

**Scope**:
- Planning documentation only.
- No source changes.

**Acceptance Criteria**:
- Tracker exists (this file).
- Roadmap reference to v1 Step 8 is retained.
- Slices are sequenced and detailed.
- Each slice has scope, non-goals, acceptance criteria, validation expectations, and a draft prompt.

**Validation Expectations**:
- Document is valid Markdown.
- Links and references are correct.

**Implementation Prompt**:
(Already executed to create this tracker)

---

## Service Layer Hardening Slice 1 — Service Inventory and Legacy Architecture Removal Audit

**Purpose**: Inventory service classes and identify inconsistent patterns and legacy APIs for removal.

**Goal**: Document the current state of service contracts, return types, hydration patterns, and catalog legacy pre-v1 / v0.x APIs that must be removed before v1.

**Scope**:
- Documentation/planning and possibly temporary audit scripts or tests.
- All classes in `src/Service/`.
- No broad source changes.

**Non-goals**:
- Fixing identified issues in this slice.

**Acceptance Criteria**:
- Current service contracts and public methods are documented.
- Return types (Resource, DTO, array, bool) are cataloged.
- Manual vs. automated hydration usage is identified.
- Legacy APIs, compatibility aliases, and patterns targeted for v1 removal are cataloged.
- High-risk or inconsistent services are flagged.
- A candidate service for Slice 4 (Read) and Slice 5 (Write) is confirmed.

**Completion Note**:
Audit completed. Findings documented in `docs/audits/08-service-layer-hardening-audit.md`.
- Key services identified: `UserService` (Read candidate), `FileService` (Write candidate), `UserEventService` (Legacy overhaul candidate).
- Legacy removal candidates cataloged, including `BaseModel`, `ModelTrait`, and stateful service methods like `getLastResult`.
- Test gaps identified for `FileService` and `UserEventService`.
- Cutover sequence refined.

**Validation Expectations**:
- Audit report is clear and actionable.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 1 — Service Inventory and Legacy Architecture Removal Audit.

Goal: Inventory service classes, identify inconsistent patterns, and catalog legacy pre-v1 / v0.x APIs for removal as part of the v1 cutover.

Scope:
- Audit all service classes in `src/Service/`.
- Document public methods, return types, and hydration patterns.
- Identify services using legacy `BaseModel` traits vs. new DTO/Resource patterns.
- Identify where `getFromBox`/`sendUpdateToBox` are used and how they handle responses.
- Catalog legacy pre-v1 APIs and compatibility aliases that should be removed during the v1 cutover.

Non-goals:
- Do not make broad source changes or refactors yet.

Acceptance Criteria:
- A clear summary of the service layer state is provided.
- Patterns needing stabilization are listed.
- A comprehensive list of legacy APIs targeted for v1 removal is provided.
- Representative services for template migration (Slices 4 & 5) are selected.

Validation:
- Report findings clearly in the update.
```

---

## Service Layer Hardening Slice 2 — Base Service Contract Stabilization

**Purpose**: Stabilize the base service behavior on top of Foundation Refinement.

**Goal**: Refactor `Box\Service\Service` to use the refined foundation (PSR-7 responses, hardened exceptions) consistently while maintaining BC.

**Scope**:
- `src/Service/Service.php`
- `src/Service/ServiceInterface.php`
- Base response/error handling logic in services.

**Non-goals**:
- Broadly rewriting all resource-specific services.
- Removing legacy return modes like 'original' or 'flat' if still needed for BC.

**Acceptance Criteria**:
- `Base Service` uses `BoxResponseInterface` for internal handling.
- `Base Service` leverages the refined exception taxonomy.
- `handleBoxResponse` and `handleResponseContent` are clean and predictable.
- Existing service return types remain compatible.

**Completion Note**:
Slice 2 completed. Base service behavior stabilized on Foundation Refinement.
- `handleBoxResponse` and `handleResponseContent` updated to use `BoxResponseInterface`.
- `processResponseError` helper added for consistent exception creation.
- Stateful properties (`lastResult*`) and methods (`getLastResult`, `getDefaultReturnType`) marked as deprecated.
- Visibility of `handleResponseContent` reduced to `protected`.
- Full compatibility maintained for existing return modes ('decoded', 'flat', 'original').
- Existing tests pass.

**Test Expectations**:
- Unit tests for base service response/error handling.
- Verify that legacy return modes still function as expected.

**Validation Commands**:
- `composer test`
- `composer analyse`

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 2 — Base Service Contract Stabilization.

Goal: Stabilize the base service behavior to use the refined foundation consistently while maintaining BC.

Scope:
- Refactor `Box\Service\Service` and its interface.
- Ensure `getFromBox`, `putIntoBox`, `queryBox`, and `sendUpdateToBox` use the refined response wrapper and exceptions.
- Clean up `handleBoxResponse` and `handleResponseContent`.

Non-goals:
- Do not break backward compatibility for existing service users.
- Do not introduce fluent setter chains.

Acceptance Criteria:
- Base service consistently uses the Step 7 foundation.
- All existing service tests pass.
- No regression in return type behavior.

Validation:
- `composer test`
- `composer analyse`
- `composer cs:check`
```

---

## Service Layer Hardening Slice 3 — Hydration and Mapper Boundary

**Purpose**: Clarify where resource hydration should happen and reduce duplicated logic.

**Goal**: Establish a clear boundary for when raw API data should be converted to typed Resources/DTOs, ideally using the `Hydrator`.

**Scope**:
- Hydration logic in services.
- Interaction between Services and `Box\Mapper\Hydrator`.
- Selected services used for demonstration.

**Non-goals**:
- Broadly rewriting the `Hydrator` itself.
- Removing legacy array compatibility where required by the transition layer.

**Acceptance Criteria**:
- Hydration is centralized in the service layer where practical.
- Duplicated mapping logic (e.g., manual array-to-object copies) is reduced.
- Type hints for returned resources are consistent.

**Completion Note**:
Slice 3 completed. Hydration boundary pattern established.
- Protected `hydrate` helper added to base `Service` class.
- `UserService` refactored to use the new `hydrate` helper.
- Standardized use of `Box\Mapper\Hydrator` via service-layer helpers.
- Legacy mapping (e.g., in `UserEventService`) and recursive hydration deferred to later slices or v1 removal.
- All existing tests pass.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 3 — Hydration and Mapper Boundary.

Goal: Clarify hydration boundaries and reduce duplicated mapping logic in services.

Scope:
- Audit and refine how services use the `Hydrator`.
- Standardize the pattern for converting API responses to Resources/DTOs.
- Ensure services do not contain complex recursive mapping logic that belongs in the Hydrator.

Acceptance Criteria:
- Clearer service methods that return typed objects.
- Reduced code duplication in response handling.
- Legacy array support is explicitly preserved where needed.

Validation:
- `composer test`
- `composer analyse`
```

---

## Service Layer Hardening Slice 4 — Representative Read Service Migration

**Purpose**: Apply hardened patterns to a read-only service as a template.

**Goal**: Migrate a low-risk read service (e.g., `UserService`) to fully use the hardened base contract and hydration patterns.

**Scope**:
- One representative service (e.g., `Box\Service\UserService`).
- Related read methods (`getUser`, `getCurrentUser`).

**Acceptance Criteria**:
- The service uses the hardened `Base Service` methods.
- Hydration follows the Slice 3 pattern.
- Public signatures and return types are preserved.
- The service serves as a documented template.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 4 — Representative Read Service Migration.

Goal: Migrate a representative read service to hardened patterns as a template.

Scope:
- Update `UserService` (or other selected service) to use hardened patterns.
- Ensure all read operations return typed Resources/DTOs via standard hydration.

Acceptance Criteria:
- Full compatibility with Step 7 foundation.
- Consistent hydration pattern.
- All related tests pass.

Validation:
- `composer test`
- `composer analyse`
```

---

## Service Layer Hardening Slice 5 — Representative Write/Update Service Migration

**Purpose**: Apply hardened patterns to a write/update service.

**Goal**: Migrate a representative service method involving request payloads to the hardened pattern.

**Scope**:
- One representative write method (e.g., in `FolderService` or `UserService` if applicable).
- Payload generation and response handling.

**Acceptance Criteria**:
- Request payloads are generated consistently.
- Response handling uses the refined foundation.
- Error states are handled via the refined exception taxonomy.
- No credential leakage in logs or exceptions.

**Completion Note**:
Slice 5 completed. Representative write/update migration pattern established.
- `FileService::createSharedLink()` migrated to use hardened patterns.
- `Service::sendUpdateAndHydrate()` helper implemented to centralize hydration and avoid `mapBoxToClass`.
- Request normalization pattern handles `CreateSharedLinkRequest`, `array`, and legacy `SharedLinkInterface`.
- Response hydration pattern uses `sendUpdateAndHydrate()` returning typed `File` resource.
- `ServiceInterface` updated to fix type mismatch in `error()` method.
- Unit tests added in `tests/Service/File/FileServiceTest.php` covering all input variants.
- Full validation suite (`composer review` equivalents) passed.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 5 — Representative Write/Update Service Migration.

Goal: Migrate a representative write/update service method to hardened patterns.

Scope:
- Update a service method that sends data to Box (e.g., create/update).
- Ensure payload handling and response verification follow hardened patterns.

Acceptance Criteria:
- Correct payload generation.
- Hardened response/error handling.
- No regression in public API.

Validation:
- `composer test`
- `composer analyse`
```

---

## Service Layer Hardening Slice 6 — File/Upload Service Compatibility Pass

**Purpose**: Verify file/upload service behavior after hardening.

**Goal**: Ensure multipart and streaming upload behaviors remain compatible with the hardened service layer.

**Scope**:
- `FileService` or related upload logic.
- Multipart handling boundaries.

**Non-goals**:
- Rewriting the `FileStream` or multipart implementation unless a blocker is found.

**Acceptance Criteria**:
- Upload behavior remains stable and compatible.
- No real network calls in tests.
- Large file/streaming support is preserved.

**Completion Note**:
Slice 6 completed. File/upload compatibility verified.
- Verified `FileService::createSharedLink()` remains compatible with hardened patterns.
- Verified `Connection::postFile()` correctly handles multipart payloads for both Guzzle and Curl transports.
- Added `tests/Connection/ConnectionUploadCompatibilityTest.php` covering:
    - Guzzle resource-based multipart uploads.
    - Curl `CURLFile`-based multipart uploads.
    - `FileStream` integration for both transports.
    - Validation for parent ID and file path.
- Confirmed `FileStream` correctly manages resources and temporary file cleanup.
- No regressions found in upload functionality.

**Validation Expectations**:
- `composer test` passes.
- `composer analyse` passes.
- `composer cs:check` passes.

---

## Service Layer Hardening Slice 7 — Service Error and Retry Semantics

**Purpose**: Ensure consistent error and retry behavior across services.

**Goal**: Services should consistently propagate refined exceptions and respect retry-after/401-refresh signals.

**Scope**:
- Error propagation in services.
- Retry-after handling in service methods.
- Token refresh retry behavior integration.

**Acceptance Criteria**:
- Exceptions from services always contain response context where available.
- Services do not swallow 401/429/5xx errors silently.
- Retry-after data is accessible to consumers when a `RateLimitException` is thrown.

**Completion Note**:
Slice 7 completed. Service error and retry semantics verified and hardened via tests.
- Verified `BoxResponseException` propagation and context preservation (status code, retry-after).
- Verified 401 refresh/retry logic in `getFromBox` and `sendUpdateToBox`.
- Verified `TransportException` propagation.
- Verified refresh failure context preservation.
- Added comprehensive unit tests in `tests/Service/ServiceErrorTest.php`.
- No source changes required; confirmed existing `Service` implementation meets v1 hardening goals.
- Validation: `composer test`, `composer analyse`, `composer cs:check`.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 7 — Service Error and Retry Semantics.

Goal: Ensure consistent error and retry behavior across services.

Scope:
- Refine how services handle and throw API exceptions.
- Ensure `Retry-After` headers are correctly exposed via exceptions.
- Verify that 401 refresh retries work seamlessly at the service level.

Acceptance Criteria:
- Consistent exception behavior across all hardened services.
- No silent failures.

Validation:
- `composer test`
```

---

## Service Layer Hardening Slice 8 — Legacy Architecture Removal and Cutover Planning

**Purpose**: Perform focused legacy architecture removal cutover planning and partitioning.

**Goal**: Convert the high-level legacy removal requirement into a concrete, sequenced removal plan with safe implementation sub-slices.

**Scope**:
- Planning and documentation only.
- Cutover sequencing and risk analysis.
- Tracker refinement.
- Identifying safe removal sub-slices.

**Non-goals**:
- Broad source code removal in this slice.
- Deleting `src/Model/` or `BaseModel`.

**Acceptance Criteria**:
- A structured removal plan exists (e.g., `docs/planning/09-legacy-architecture-removal.md`).
- Dependency graph and removal order are defined.
- Removal work is broken into atomic sub-slices.
- Handoff from Service Layer Hardening to Legacy Removal is clear.

**Completion Note**:
Slice 8 completed as a planning and handoff slice.
- Legacy inventory and removal sequence documented in `docs/planning/09-legacy-architecture-removal.md`.
- `UserEventService` identified as the primary blocker for core legacy removal.
- Service Layer Hardening Slice 8 reframed as planning; execution moved to a dedicated Step 9 tracker.

**Validation Expectations**:
- Planning documents are valid Markdown.
- Sequencing is logical and addresses dependencies.

**Junie Prompt**:
(Handled via Slice 8 implementation)

---

## Service Layer Hardening Slice 9 — Service Documentation and Migration Drift Pass

**Purpose**: Update service usage documentation.

**Goal**: Align README, migration guides, and programmatic examples with the hardened service layer.

**Scope**:
- `README.md`
- `docs/user/programmatic-usage.md`
- `docs/migration/upgrading-0.11-to-1.0.md`
- `CHANGELOG.md` (following `changelog-prompt.md`)

**Acceptance Criteria**:
- Documentation matches implementation.
- No fluent setter chains in examples.
- Placeholder credentials only.
- Migration notes for service-layer changes are clear.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 9 — Service Documentation and Migration Drift Pass.

Goal: Update documentation to reflect hardened service layer patterns.

Scope:
- Update service usage examples in README and programmatic guides.
- Document any service-level migration requirements in the v1 guide.
- Update CHANGELOG following the project's guidelines.

Acceptance Criteria:
- Documentation is accurate and follows all style rules.
- No leaked secrets.

Validation:
- `composer lint`
- `composer cs:check`
```

---

## Service Layer Hardening Slice 10 — Service Type-Safety Cleanup

**Purpose**: Address type-safety in touched service areas.

**Goal**: Resolve PHPStan and type-hint issues in the files modified during this initiative.

**Scope**:
- All files touched in Slices 2-7.
- Related tests.

**Acceptance Criteria**:
- `composer analyse` passes for touched files.
- Stricter type hints are used where possible without breaking BC.
- No broad unrelated model cleanup.

**Completion Note**:
Slice 10 completed. Type-safety improved across base service and resource-specific services.
- Refined `Service::hydrate` and other base helpers with PHPDoc generics (`template T`).
- Standardized imports in `Service.php`.
- Synchronized `void` return types for all setters in `ServiceInterface` and `Service`.
- Added explicit return types to `FileServiceInterface` and `FileService`.
- Improved test type-safety by adding type hints to anonymous callback parameters in `UserServiceTest` and `FileServiceTest`.
- Cleaned up PHPStan baseline by removing unnecessary `return.missing` ignores for `Service.php`.
- Full validation suite (`composer analyse`, `composer test`, `composer cs:check`) passed with zero errors.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 10 — Service Type-Safety Cleanup.

Goal: Resolve type-safety and analysis issues in touched service areas.

Scope:
- Fix PHPStan findings in services and resources touched during hardening.
- Improve type hints and return types.

Acceptance Criteria:
- `composer analyse` passes for implementation files.
- Improved code quality without BC breaks.

Validation:
- `composer analyse`
- `composer test`
```

---

## Service Layer Hardening Slice 11 — Final Integration Review

**Purpose**: Close the Service Layer Hardening initiative.

**Goal**: Final validation and tracker update.

**Scope**:
- Full project validation.
- Tracker status update.
- Deferred follow-up documentation.

**Acceptance Criteria**:
- All slices completed.
- Full `composer review` passes.
- Deferred work is clearly listed.

**Junie Prompt**:
```markdown
Implement Service Layer Hardening Slice 11 — Final Integration Review.

Goal: Final validation and closure of the Service Layer Hardening initiative.

Scope:
- Run full validation suite.
- Update tracker status.
- Document any deferred follow-ups.

Acceptance Criteria:
- All items green.
- Project is stable and ready for the next step.

Validation:
- `composer review`
```

## Status Table Progress
- Slice 0: Completed ✓
- Slice 1: Completed ✓
- Slice 2: Completed ✓
- Slice 3: Completed ✓
- Slice 4: Completed ✓
- Slice 5: Completed ✓
- Slice 6: Completed ✓
- Slice 7: Completed ✓
- Slice 8: Completed ✓
- Slice 9: Completed ✓
- Slice 10: Completed ✓
- Slice 11: Pending

## Deferred Follow-up
- Broad removal of legacy pre-v1 / v0.x architecture: This remains a v1 release requirement. If the remaining scope exceeds the current Service Layer Hardening tracker, it will be moved to a dedicated tracker, but it MUST be completed before v1 release.
- v1 is the clean target architecture. Removals specifically target pre-v1 / v0.x legacy APIs and patterns. Newly established v1 APIs (e.g., those following the hardened patterns) are not being removed.
- Full auto-pagination implementation (deferred).
- Advanced multipart features (deferred).
