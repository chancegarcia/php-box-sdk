# Legacy Architecture Removal Plan

Roadmap reference: v1 Step 9 (Sequenced from Step 8)

## Purpose

This initiative is the intentional v1 major-version cutover that removes legacy pre-v1 / v0.x architecture and APIs. v1 is the clean target architecture. This does not mean removing newly established v1 APIs (e.g., those following the hardened patterns from Step 7 and 8). The goal is to completely remove the old legacy API architectural layer as part of the v1 major-version cutover.

Legacy removal is required before v1 release. Removals must be intentional, tested, documented, and sequenced. Work must proceed in atomic slices.

## 1. Legacy Inventory by Category

### Base Model Architecture
- **Files**: 
    - `src/Model/BaseModel.php`
    - `src/Model/BaseModelInterface.php`
    - `src/Model/BaseModelTrait.php`
    - `src/Model/Model.php`
    - `src/Model/ModelInterface.php`
    - `src/Model/ModelTrait.php`
    - `src/Model/BoxModel.php`
    - `src/Model/BoxModelInterface.php`
- **Usages**: Extended/Implemented by nearly all legacy resource models and services.
- **v1 Replacement**: 
    - Resources: `Box\Resource\...` (e.g., `Box\Resource\User`)
    - Services: `Box\Service\Service` (hardened base)
    - DTOs: `Box\Dto\...`
- **Risk**: High (Core foundation)
- **Dependency**: Must migrate all services and resources first.

### Service Base Patterns & Stateful APIs
- **Files**: `src/Service/Service.php`
- **Impacted Methods**: 
    - `getLastResult`
    - `lastResult` (property)
    - `lastResultType` (property)
    - `lastResultResponse` (property)
    - `getDefaultReturnType`
    - `refreshToken` (move to Auth/Connection)
    - `refreshTokenIfExpired`
- **Usages**: Consumers checking `getLastResult()` after service calls.
- **v1 Replacement**: Typed return values from service methods; Auth handled by `Connection`.
- **Risk**: Medium
- **Dependency**: Update `Client` and CLI commands.

### Legacy Hydration & Mapping Flows
- **Symbols**: 
    - `mapBoxToClass` (on models and `ModelMapper`)
    - `classArray` (on models and `ModelMapper`)
    - `toBoxArray` (on models)
    - `toClassVar` / `toBoxVar` (where used for legacy mapping)
    - `buildQuery` (on models)
- **Files**: 
    - `src/Mapper/ModelMapper.php` (legacy methods)
    - `src/Model/ModelTrait.php`
    - `src/Model/BaseModelTrait.php`
- **v1 Replacement**: `Box\Mapper\Hydrator::hydrate()`, `Hydrator::extract()` (or DTO `toArray()`)
- **Risk**: Medium
- **Dependency**: Migration of `UserEventService` and `Folder::classArray`.

### Custom Collection & Event Layers
- **Files**: 
    - `src/Event/Collection/*`
    - `src/Event/User/*`
    - `src/Event/Admin/*`
    - `src/Collection/*` (Legacy collection layer)
- **v1 Replacement**: Doctrine Collections, `Box\Resource\Event`.
- **Risk**: Medium/High
- **Dependency**: Full overhaul of `UserEventService`.

### Compatibility Aliases
- **Files**: 
    - `src/User/User.php` (deprecated)
    - Other legacy resource aliases in non-flattened namespaces.
- **v1 Replacement**: Flattened namespace equivalents (e.g., `Box\Resource\User`).
- **Risk**: Low (Documentation/Migration impact)

## 2. Non-goals
- Implementing new major features (auto-pagination, JWT/S2S auth) unless explicitly required for a removal.
- Unrelated refactors of newly established v1 APIs.
- Broad behavior changes not required for legacy cutover.

## 3. Dependency Graph / Removal Order

1.  **Stage 1: Characterization & Readiness**
    - Add characterization tests for high-risk legacy areas (`UserEventService`, legacy collections).
    - Audit `Client` and CLI for legacy state usage.
2.  **Stage 2: UserEventService & Collection Overhaul**
    - Migrate `UserEventService` (removes dependency on `mapBoxToClass` and legacy collection).
    - Replace legacy `EventCollection` etc. with modern patterns.
3.  **Stage 3: Service & Resource Cutover**
    - Finalize all `Box\Resource\...` classes.
    - Update all services to return `Box\Resource` instead of legacy `Box\Model`.
    - Remove stateful service APIs (`getLastResult`).
4.  **Stage 4: Infrastructure Removal**
    - Remove `mapBoxToClass` and `classArray` usage in `Client` and services.
    - Remove `ModelTrait` and `BaseModelTrait`.
    - Remove `BaseModel`, `Model`, `BoxModel` and their interfaces.
    - Remove `Box\Model` namespace entirely.
5.  **Stage 5: Cleanup & Validation**
    - Remove compatibility aliases.
    - Remove PHPStan baseline entries for removed code.
    - Final docs/migration drift pass.

## 4. Atomic Slices

| Slice | Name | Status |
|---|---|---|
| 9.0 | [Tracker & Readiness Review](#slice-90-tracker--readiness-review) | ✓ |
| 9.1 | [UserEventService Characterization Tests](#slice-91-usereventservice-characterization-tests) | ✓ |
| 9.2 | [UserEventService & Event Collection Overhaul](#slice-92-usereventservice--event-collection-overhaul) | ✓ |
| 9.3 | [Service Stateful API Removal](#slice-93-service-stateful-api-removal) | ✓ |
| 9.4 | [Model Trait & Mapping Infrastructure Removal](#slice-94-model-trait--mapping-infrastructure-removal) | ✓ |
| 9.5 | [Base Architecture & Box\Model Removal](#slice-95-base-architecture--boxmodel-removal) | ✓ | Completion: Removed legacy `BaseModel`, `Model`, `BoxModel` and interfaces. Migrated core infrastructure and resource models. Updated tests and migration docs. |
| 9.6 | [Compatibility Alias Removal](#slice-96-compatibility-alias-removal) | ✓ | Completion: Removed `Box\User\User` alias; confirmed `src/Model` is clear. Updated references in `File` resource and tests. Updated migration docs and added removal verification tests. |
| 9.7 | [Docs & Migration Drift Pass](#slice-97-docs--migration-drift-pass) | ✓ | Completion: Updated migration guide with before/after examples. Cleaned up stale references in README and user guides. Updated CHANGELOG following prompt. |
| 9.8 | [Final Type-Safety & Baseline Cleanup](#slice-98-final-type-safety--baseline-cleanup) | |
| 9.9 | [Final Integration Review](#slice-99-final-integration-review) | |

---

## Slice 9.0: Tracker & Readiness Review

**Purpose**: Review and refine the Step 9 tracker.

**Scope**:
- Planning documentation only.
- No source changes.

**Acceptance Criteria**:
- Tracker is refined and sequenced.
- v1 removal policy is clearly stated.
- Draft prompts are provided for each slice.

---

## Slice 9.1: UserEventService Characterization Tests

**Purpose**: Ensure baseline behavior for `UserEventService` is documented via tests before overhaul.

**Goal**: Add characterization tests that capture current behavior (even if messy) to prevent regressions during removal.

**Scope**:
- `tests/Service/Event/UserEventServiceTest.php` (new or updated)
- Mocking Box API responses for events.

**Non-goals**:
- Fixing bugs found during testing (document them instead).

**Acceptance Criteria**:
- Core `UserEventService` flows are covered.
- Event collection hydration is tested.

**Completion Note (2026-05-09)**:
- Added `tests/Service/Event/UserEventServiceTest.php` with 17 characterization tests.
- Checked API semantics against official Box Events API documentation (2024.0).
- Identified legacy behaviors to be addressed in Slice 9.2:
    - Default `stream_position` is `0` in SDK, while API defaults to "now" if omitted.
    - Error message for `setStreamPosition` incorrectly references "limit".
    - `getEvents()` returns `null` when using a mapping collection (legacy `mapBoxToClass` behavior).
    - `getLimit()` returns a numeric string if the input was a numeric string.
- Adjusted tests to use realistic Box response fixtures (`chunk_size`, `next_stream_position`, `entries`).
- Next recommended slice: 9.2 (UserEventService & Event Collection Overhaul).

**Validation**:
- `composer test`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.1 — UserEventService Characterization Tests.

Goal: Add characterization tests for UserEventService and its legacy collection/event layer to ensure baseline behavior is captured before removal.

Scope:
- Create or update tests for UserEventService.
- Cover getEvents() with different stream types and positions.
- Document how EventCollection and Event models are currently hydrated.

Validation:
- composer test
```

---

## Slice 9.2: UserEventService & Event Collection Overhaul

**Status**: Completed

**Notes**:
- Modernized `UserEventService` to return `EventResponse` DTO.
- Introduced `EventResponseMapper` to decouple service from response mapping/hydration.
- `EventResponse` DTO is immutable by design using defensive copies of internal collections.
- Removed dependency on `mapBoxToClass` and `getLastResult`.
- Aligned default `stream_position` and error messages with Box API semantics.
- `next_stream_position` is handled as a string to support large cursors.
- Updated `UserEventServiceInterface` to use strict types where practical while preserving limited legacy string compatibility for numeric parameters.
- Updated migration documentation.

**Acceptance Criteria**:
- `UserEventService` returns modern resource/collection objects. ✓
- Characterization tests from 9.1 pass (or are updated to reflect intentional API changes). ✓

**Validation**:
- `composer test`
- `composer analyse`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.2 — UserEventService & Event Collection Overhaul.

Goal: Modernize UserEventService and the event collection layer, removing reliance on legacy mapping.

Scope:
- Refactor UserEventService to return modern Event resources.
- Replace legacy EventCollection with a modern alternative.
- Remove mapBoxToClass usage from the event layer.

Validation:
- composer test
- composer analyse
```

---

## Slice 9.3: Service Stateful API Removal

**Status**: Completed

**Notes**:
- Verified that `getLastResult()`, `getDefaultReturnType()`, `setDefaultReturnType()`, and associated properties were already removed in prior steps.
- Removed `validateReturnType()` from `ServiceInterface` to encapsulate internal validation logic.
- Hardened `Service::validateReturnType()` by changing visibility to `protected` and fixing a bug where it attempted to `explode()` an array (now uses `implode()` for error messaging).
- Updated migration documentation to explicitly confirm these removals and provide guidance on using `ApiException` for response inspection.
- All service tests pass, confirming that methods rely on explicit return values.

**Acceptance Criteria**:
- Stateful APIs are removed. ✓
- All core consumers (Client, CLI) use return values instead. ✓

**Validation**:
- `composer review` ✓
- `composer test` ✓
- `composer analyse` ✓

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.3 — Service Stateful API Removal.

Goal: Remove stateful APIs (getLastResult, etc.) from the Service layer.

Scope:
- Remove lastResult properties and getLastResult/getDefaultReturnType methods.
- Update Client and CLI to use method return values.
- Update tests that rely on stateful service behavior.

Validation:
- composer review
```

---

## Slice 9.4: Model Trait & Mapping Infrastructure Removal

**Purpose**: Remove the legacy mapping traits and `ModelMapper` legacy methods.

**Goal**: Delete `ModelTrait`, `BaseModelTrait` and their methods like `classArray`, `toBoxArray`, `mapBoxToClass`.

**Scope**:
- `src/Model/ModelTrait.php`, `src/Model/BaseModelTrait.php`
- `src/Mapper/ModelMapper.php` (cleanup)

**Status**: Completed

**Notes**:
- Removed legacy traits `BaseModelTrait` and `ModelTrait`.
- Updated `BaseModel`, `Model`, `BaseModelInterface`, and `ModelInterface` to implement legacy methods by proxying to `ModelMapper` or using native PHP/v1 alternatives, preparing for their eventual removal in Slice 9.5.
- **Bridge Update**: `Model::mapBoxToClass()` and `Model::toArray()` are explicitly marked as deprecated bridge methods to support remaining resource dependencies until Slice 9.5.
- Replaced `mapBoxToClass` usage in `Client` and `Service` with `Hydrator::hydrate`.
- Replaced `buildQuery` usage in `Client` and `Connection` with native `http_build_query`.
- Replaced `isInt` usage in `UserEventService` with native `is_numeric`.
- Replaced `toBoxArray` usage in `AuthExchangeCommand`, `AuthRefreshCommand`, and `FileService` with `toArray()` or mapper extraction.
- Introduced `toArray()` to `TokenInterface` and `Token` to support clean serialization.
- Updated migration documentation to reflect mapping infrastructure changes.

**Acceptance Criteria**:
- Mapping traits are removed. ✓
- No remaining usages in the SDK. ✓

**Validation**:
- `composer test` ✓
- `composer analyse` ✓
- `composer cs:check` ✓

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.4 — Model Trait & Mapping Infrastructure Removal.

Goal: Remove legacy mapping traits (ModelTrait, BaseModelTrait) and clean up ModelMapper.

Scope:
- Delete src/Model/ModelTrait.php and src/Model/BaseModelTrait.php.
- Remove legacy mapping methods from ModelMapper.
- Ensure no code relies on these traits.

Validation:
- composer review
```

---

## Slice 9.5: Base Architecture & Box\Model Removal

**Purpose**: Final removal of the legacy model base classes and namespace.

**Goal**: Delete `BaseModel`, `Model`, `BoxModel` and the entire `src/Model/` directory.

**Scope**:
- `src/Model/`

**Acceptance Criteria**:
- `src/Model/` directory is gone.
- SDK compiles and tests pass using `Box\Resource` and `Box\Dto`.

**Validation**:
- `composer review`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.5 — Base Architecture & Box\Model Removal.

Goal: Remove the legacy BaseModel architecture and the Box\Model namespace.

Scope:
- Delete all files in src/Model/.
- Ensure all resources have been migrated to Box\Resource.

Validation:
- composer review
```

---

## Slice 9.6: Compatibility Alias Removal

**Purpose**: Remove legacy aliases to enforce the flattened namespace.

**Goal**: Delete deprecated alias files like `src/User/User.php`.

**Scope**:
- Legacy alias files.

**Acceptance Criteria**:
- All v0.x compatibility aliases are removed.

**Validation**:
- `composer review`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.6 — Compatibility Alias Removal.

Goal: Remove all remaining v0.x compatibility aliases.

Scope:
- Identify and remove files that serve only as deprecated aliases to modern resources.

Validation:
- composer review
```

---

## Slice 9.7: Docs & Migration Drift Pass

**Purpose**: Ensure documentation and migration guides reflect the final v1 state.

**Goal**: Update migration guides with "before/after" examples for the removed legacy APIs.

**Scope**:
- `docs/migration/upgrading-0.11-to-1.0.md`
- `README.md`
- `CHANGELOG.md`

**Acceptance Criteria**:
- Migration guide clearly documents removals and replacements.
- No references to legacy symbols remain in documentation.

**Validation**:
- `composer lint`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.7 — Docs & Migration Drift Pass.

Goal: Update documentation to reflect the final removal of legacy architecture.

Scope:
- Update the migration guide with final removal details and before/after examples.
- Cleanup any remaining legacy references in README and user guides.
- Update CHANGELOG following guidelines.

Validation:
- composer lint
```

---

## Slice 9.8: Final Type-Safety & Baseline Cleanup

**Purpose**: Clean up PHPStan baseline and improve final type safety.

**Goal**: Remove baseline entries that were tied to legacy code and ensure the project passes analysis at the target level.

**Scope**:
- `phpstan-baseline.neon`
- `phpstan.neon.dist`

**Acceptance Criteria**:
- `composer analyse` passes with a significantly reduced (or eliminated for core areas) baseline.

**Validation**:
- `composer analyse`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.8 — Final Type-Safety & Baseline Cleanup.

Goal: Clean up the PHPStan baseline and perform final type-safety improvements.

Scope:
- Remove now-invalid baseline entries.
- Improve type hints in areas where legacy code previously blocked it.

Validation:
- composer analyse
```

---

## Slice 9.9: Final Integration Review

**Purpose**: Close Step 9.

**Goal**: Run the full suite and confirm the project is v1-ready from an architecture standpoint.

**Scope**:
- Full project review.

**Acceptance Criteria**:
- All tests green.
- Static analysis clean.
- Style check passes.

**Validation**:
- `composer review`

**Draft Prompt (Refinement Required)**:
```markdown
Implement Step 9 Slice 9.9 — Final Integration Review.

Goal: Final validation and closure of the Legacy Architecture Removal initiative.

Scope:
- Run full validation suite (composer review).
- Verify no legacy symbols remain in the codebase.

Validation:
- composer review
```

## Release Readiness Follow-ups

### Factory Modernization

Review factory patterns after core legacy model removal.

- Review `AbstractFactory` and related generic factory behavior.
- Determine whether it is legacy infrastructure or still useful.
- Remove or modernize it if it exists only to support pre-v1 architecture.
- Preserve only factories that are clear v1 creation boundaries.

### Resource Interface Rationalization

Audit resource interfaces for legacy overengineering.

- Review interfaces such as `FileInterface` and similar resource mirror interfaces.
- Keep interfaces that serve real extension points, mocking seams, or stable contracts.
- Remove one-class mirror interfaces if they are legacy overengineering.
- Document public API breaks in migration docs if removed.

### API Fixture Realism and Contract Alignment

Before final v1 release, a later pass should:

- Review high-value service/resource tests against official Box API examples.
- Replace overly artificial mocked payloads where accuracy matters.
- Centralize reusable Box API-shaped fixtures.
- Keep tests isolated with no real network calls.
- Avoid real credentials, account IDs, private paths, and private data.
- Focus first on services affected by v1 cutover work.
- Defer broad fixture cleanup until after legacy architecture removal unless a fixture is needed for the current slice.
