# v1 Release Work Tracker

Roadmap reference: v1 Steps 10–16

## Purpose
This tracker covers the remaining work required before the v1.0.0 release of the `box-api-v2-sdk`. Having completed the core foundation refactoring, service layer hardening, and legacy architecture removal, this phase focuses on rationalizing interfaces, modernizing factories, aligning with API contracts through realistic fixtures, and implementing key missing features like JWT/S2S authentication.

## Scope
- Rationalization of resource interfaces (e.g., `FileInterface`).
- Modernization of factory patterns.
- Improving test fixture realism.
- Implementation of JWT/S2S Authentication.
- Webhook signature verification.
- Final release readiness and validation.

## Non-Goals
- Introducing major new architectural layers not specified in the roadmap.
- Implementing every possible Box API endpoint (focus remains on high-priority and v1 core).
- Breaking newly established v1 hardened APIs.

## Dependency on Completed Steps 7–9
This work assumes the completion of:
- **Step 7: Foundation Refinement** (Response/Transport hardening) ✓
- **Step 8: Service Layer Hardening** (Service normalization) ✓
- **Step 9: Legacy Architecture Removal** (Removal of `BaseModel`, `mapBoxToClass`, etc.) ✓

## Remaining Step List & Status

| Step | Title | Status |
| :--- | :--- | :--- |
| 10 | [Resource Namespace and Interface Rationalization](#step-10--resource-namespace-and-interface-rationalization) | Not Started |
| 11 | [Factory Modernization](#step-11--factory-modernization) | Not Started |
| 12 | [API Fixture Realism and Contract Alignment](#step-12--api-fixture-realism-and-contract-alignment) | Not Started |
| 13 | [JWT/S2S Feasibility and Dependency Review](#step-13--jwts2s-feasibility-and-dependency-review) | Not Started |
| 14 | [JWT/S2S Implementation](#step-14--jwts2s-implementation) | Not Started |
| 15 | [Webhook Verification](#step-15--webhook-verification) | Not Started |
| 16 | [v1 Release Readiness](#step-16--v1-release-readiness) | Not Started |

## Recommended Sequencing
The steps should generally be followed in numerical order. Step 10 and 11 are closely related as factories often return interfaces. Step 13 must precede Step 14. Step 16 is the final gate.

---

## Step 10 — Resource Namespace and Interface Rationalization

### Purpose
Audit and migrate remaining domain resource classes into the final v1 resource namespace, and rationalize resource interfaces such as `FileInterface`, `FolderInterface`, `GroupInterface`, `CollaborationInterface`, and related resource contracts to decide which are real v1 contracts and which are legacy one-class mirror interfaces.

### Scope
- Audit current resource namespaces:
    - `src/File`
    - `src/Folder`
    - `src/Group`
    - `src/Collaboration`
    - `src/Event`
    - `src/Item`
    - `src/Resource`
- Decide/confirm the final v1 namespace convention (e.g., `Box\Resource\...`).
- Move or replace old domain-root resources with `Box\Resource\...` resources where that is the target (aligning with existing `Box\Resource\User`).
- Update services, factories, tests, and docs to use final resource namespaces.
- Remove old namespace aliases/wrappers after migration.
- Review all interfaces in `src/Resource/`, `src/File/`, `src/Folder/`, etc.
- Identify "mirror interfaces" that merely replicate a single implementation's public methods without providing a useful abstraction or test seam.
- Remove or simplify legacy mirror interfaces where appropriate.
- Preserve interfaces that represent stable SDK contracts or meaningful extension points.

### Non-Goals
- Do not perform factory modernization here (Step 11). Factory modernization must happen *after* resource namespace cutover so that factories target final classes.
- Do not implement JWT/S2S (Step 14).
- Do not perform broad resource behavior rewrites without tests.

### Acceptance Criteria
- Minimalist set of resource interfaces that serve clear purposes.
- All implementations updated to reflect interface changes.
- Migration notes updated for any removed public interfaces.

### Likely Slices
- Slice 1: Interface Audit and Rationalization Plan.
- Slice 2: Implementation of Rationalized Interfaces (File/Folder focus).
- Slice 3: Implementation of Rationalized Interfaces (Other resources).

### Validation Expectations
- `composer review` passes.
- No regression in existing service tests.

### Draft Prompt Outline
> Refine Step 10: Resource Namespace and Interface Rationalization.
> 1. Audit current resource namespaces (`src/File`, `src/Folder`, etc.).
> 2. Confirm final v1 namespace convention (e.g., `Box\Resource`).
> 3. Migrate resources to the final namespace and update all references.
> 4. Audit all resource interfaces (e.g., `FileInterface`).
> 5. For each, determine: Keep as is, Simplify, or Remove (merge into implementation).
> 6. Implement changes, ensuring all usages (Services, Factories, Client) are updated.
> 7. Update migration docs.

---

## Step 11 — Factory Modernization

### Purpose
Audit and modernize factory patterns after interface rationalization to ensure clear construction boundaries.

### Scope
- Review `AbstractFactory`, static factory methods, and generic `new $class($options)` behavior.
- Replace legacy generic factories with explicit typed factories where useful.
- Preserve factories that are clear v1 construction boundaries.

### Non-Goals
- Do not remove useful factories just because they are simple.
- Do not bundle JWT/S2S implementation here.

### Acceptance Criteria
- Modernized factories following v1 patterns.
- Removal of legacy factory traits or obsolete base classes.

### Draft Prompt Outline
> Refine Step 11: Factory Modernization.
> 1. Audit existing factories in `src/Factory/`.
> 2. Modernize patterns to use explicit typed factories where beneficial.
> 3. Remove legacy factory base classes/traits if they no longer serve v1.

---

## Step 12 — API Fixture Realism and Contract Alignment

### Purpose
Ensure service and resource tests reflect actual Box API behavior by using realistic fixtures.

### Scope
- Review high-value service/resource tests against official Box API examples.
- Replace overly artificial mocked payloads where accuracy matters.
- Centralize reusable Box API-shaped fixtures in `tests/Fixtures/`.

### Non-Goals
- Do not create network integration tests.
- Do not chase low-value fixtures that don't affect release confidence.

### Acceptance Criteria
- At least one realistic fixture for each major resource (File, Folder, User, Group).
- Tests for core services (FileService, UserService, etc.) use these fixtures.

---

## Step 13 — JWT/S2S Feasibility and Dependency Review

### Purpose
Evaluate requirements and dependencies for implementing Box JWT/S2S authentication.

### Scope
- Review Box JWT/S2S auth requirements (RSA-4096, signing, etc.).
- Identify crypto dependencies (e.g., OpenSSL).
- Plan DTOs for configuration and token storage.
- Produce implementation slices for Step 14.

### Non-Goals
- Do not implement JWT/S2S logic in this step.

### Acceptance Criteria
- Feasibility report documented.
- Clear implementation plan for Step 14.

---

## Step 14 — JWT/S2S Implementation

### Purpose
Implement JWT/S2S authentication based on the feasibility study.

### Scope
- Implement JWT signing and token exchange.
- Integrate with `AuthProvider` / `Connection` flows.
- Add tests using fake keys only.

### Non-Goals
- Do not use real keys or real Box accounts.

### Acceptance Criteria
- Working JWT/S2S authentication flow.
- Unit tests cover signing and response handling.

---

## Step 15 — Webhook Verification

### Purpose
Implement Box webhook signature verification to ensure security for webhook consumers.

### Scope
- Implement signature verification logic according to Box documentation.
- Provide a utility or service for verifying incoming webhook requests.

### Non-Goals
- Do not implement framework-specific adapters (e.g., Symfony/Laravel) unless scoped as a sub-task.

### Acceptance Criteria
- Verification logic implemented and tested with mock signatures.

---

## Step 16 — v1 Release Readiness

### Purpose
Final polish and validation before tagging v1.0.0.

### Scope
- Final documentation pass (README, migration docs).
- Final changelog pass.
- Full validation (`composer review`).
- Security scan for credentials.
- Release metadata checks.

### Acceptance Criteria
- `composer review` passes 100%.
- No known release blockers.

---

## Release Blockers
- Step 10 Resource Namespace and Interface Rationalization complete.
- Step 11 Factory Modernization complete or explicitly deferred.
- Step 12 API Fixture Realism complete enough for release confidence.
- Step 13 JWT/S2S feasibility complete.
- Step 14 JWT/S2S implementation complete.
- Step 15 Webhook Verification decision and implementation complete.
- Step 16 final release readiness complete.
- `composer review` passes.
- No known credential leaks.
- Migration docs/changelog accurate.

## Deferred / Post-v1 Candidates
- Advanced auto-pagination.
- Full endpoint parity (all Box APIs).
- Framework-specific bundles.
