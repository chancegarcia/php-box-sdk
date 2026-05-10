# Step 10 — Resource Namespace and Interface Rationalization

Roadmap reference: v1 Step 10

## Purpose
This tracker covers the rationalization of the resource surface for the v1 release. This includes moving remaining domain resources into the final `Box\Resource` namespace and removing mirror interfaces that no longer serve a meaningful purpose in the v1 architecture.

## Scope
- Resource Namespace Cutover: Move resources from domain-root namespaces (e.g., `Box\File`) to `Box\Resource`.
- Interface Rationalization: Remove one-class mirror interfaces (e.g., `FileInterface`) and update all consumers to use concrete classes.
- Service Alignment: Update service signatures and implementation to use the final v1 resource types and move endpoint constants from interfaces to services.
- Migration Docs: Update documentation to reflect these breaking changes.

## Non-Goals
- Factory Modernization (Step 11).
- JWT/S2S Implementation (Step 14).
- Broad behavior changes unrelated to namespace or interface rationalization.

## Dependency on Steps 7–9
This work assumes the completion of Step 9 (Legacy Architecture Removal), ensuring no resources inherit from legacy base models.

## Final Namespace Policy
- All Box domain resources must reside in the `Box\Resource` namespace.
- Domain-root namespaces (e.g., `Box\File`, `Box\Folder`) are deprecated and will be removed in this step.

## Interface Rationalization Policy
- **Remove**: Mirror interfaces that merely replicate a single implementation's public methods (e.g., `FileInterface`, `FolderInterface`).
- **Retain**: Interfaces that represent stable SDK contracts, real extension points, or polymorphic boundaries used across multiple implementations.
- **Action**: All `URI` constants must move from resource interfaces to their respective services as `ENDPOINT` constants.

## Status Table

| Slice | Title | Status |
| :--- | :--- | :--- |
| 10.0 | [Tracker and Resource Surface Audit](#slice-100--tracker-and-resource-surface-audit) | Not Started |
| 10.1 | [User Resource Validation](#slice-101--user-resource-validation) | Not Started |
| 10.2 | [File Resource Namespace and Interface Rationalization](#slice-102--file-resource-namespace-and-interface-rationalization) | Not Started |
| 10.3 | [Folder Resource Namespace and Interface Rationalization](#slice-103--folder-resource-namespace-and-interface-rationalization) | Not Started |
| 10.4 | [Group and Collaboration Resource Rationalization](#slice-104--group-and-collaboration-resource-rationalization) | Not Started |
| 10.5 | [Shared Item and Event Resource Rationalization](#slice-105--shared-item-and-event-resource-rationalization) | Not Started |
| 10.6 | [Migration Docs and Baseline Cleanup](#slice-106--migration-docs-and-baseline-cleanup) | Not Started |
| 10.7 | [Final Integration Review](#slice-107--final-integration-review) | Not Started |

---

## Slice 10.0 — Tracker and Resource Surface Audit

**Purpose**: Finalize the tracker and perform a detailed audit of all remaining resource interfaces.

**Scope**:
- Finalize this planning document.
- Audit all remaining interfaces in `src/`.
- Confirm removal list for Slice 10.2–10.5.

**Acceptance Criteria**:
- Tracker is approved.
- Detailed list of interfaces to remove is documented.

---

## Slice 10.1 — User Resource Validation

**Purpose**: Ensure `Box\Resource\User` is the canonical template and all references are correct.

**Scope**:
- Check `UserService` and `Client` for any remaining `UserInterface` references (if any exist).
- Ensure `User` is correctly used in all tests.
- Confirm `Box\User` namespace is fully cleared.

**Acceptance Criteria**:
- `Box\Resource\User` is used consistently across the SDK.
- `Box\User` namespace is gone.

---

## Slice 10.2 — File Resource Namespace and Interface Rationalization

**Purpose**: Move File resource to `Box\Resource` and remove `FileInterface`.

**Scope**:
- Move `Box\File\File` to `Box\Resource\File`.
- Remove `Box\File\FileInterface`.
- Move `URI` and `UPLOAD_URI` constants from `FileInterface` to `FileService` as `ENDPOINT` and `UPLOAD_ENDPOINT`.
- Update `FileService`, `FileFactory`, `Client`, and tests.
- Update `upgrading-0.11-to-1.0.md`.

**Acceptance Criteria**:
- `Box\File` namespace is gone.
- `FileInterface` is removed.
- All tests pass with `Box\Resource\File`.

---

## Slice 10.3 — Folder Resource Namespace and Interface Rationalization

**Purpose**: Move Folder resource to `Box\Resource` and remove `FolderInterface`.

**Scope**:
- Move `Box\Folder\Folder` to `Box\Resource\Folder`.
- Remove `Box\Folder\FolderInterface`.
- Move `URI` constants to `FolderService`.
- Update `FolderService`, `FolderFactory`, `Client`, and tests.
- Update migration docs.

**Acceptance Criteria**:
- `Box\Folder` namespace is gone.
- `FolderInterface` is removed.
- All tests pass.

---

## Slice 10.4 — Group and Collaboration Resource Rationalization

**Purpose**: Rationalize Group and Collaboration resources.

**Scope**:
- Move `Group` and `Collaboration` to `Box\Resource`.
- Remove `GroupInterface` and `CollaborationInterface`.
- Update respective services and factories.
- Move endpoint constants to services.

**Acceptance Criteria**:
- `Box\Group` and `Box\Collaboration` namespaces are gone.
- Interfaces are removed.
- All tests pass.

---

## Slice 10.5 — Shared Item and Event Resource Rationalization

**Purpose**: Rationalize Shared Link, Item, and Event resources.

**Scope**:
- Review and move `SharedLink`, `Event`, and related sub-resources (Entries, Source) to `Box\Resource`.
- Remove corresponding mirror interfaces.
- Update `UserEventService` and `EventResponseMapper`.

**Acceptance Criteria**:
- All remaining domain resources are in `Box\Resource`.
- Mirror interfaces are removed.
- Tests pass.

---

## Slice 10.6 — Migration Docs and Baseline Cleanup

**Purpose**: Finalize documentation and clean up static analysis baseline.

**Scope**:
- Update `upgrading-0.11-to-1.0.md` with a complete list of removed interfaces.
- Update `CHANGELOG.md` following guidelines.
- Remove unmatched baseline entries from `phpstan-baseline.neon`.
- Run `composer review`.

**Acceptance Criteria**:
- Documentation is accurate and complete.
- Static analysis is clean.

---

## Slice 10.7 — Final Integration Review

**Purpose**: Close Step 10 and verify readiness for Step 11.

**Scope**:
- Full validation pass.
- Verify no remaining mirror interfaces.
- Confirm factories are ready for modernization.

**Acceptance Criteria**:
- `composer review` passes 100%.
- Step 10 is officially complete.

## Acceptance Criteria
- All Box domain resources are in `Box\Resource`.
- No one-class mirror interfaces remain for resources.
- Services return concrete resource classes.
- Endpoint constants live in Services, not Resource interfaces.
- `composer review` passes.
- Migration documentation is updated.

## Validation Expectations
- `composer lint`
- `composer test`
- `composer analyse`
- `composer cs:check`

## Important Guardrails
- Do not modernize factories in this step (except for return type updates).
- Do not break public API without documenting it in migration guides.
- Do not introduce fluent setters.
- Do not use real credentials or network calls in tests.
