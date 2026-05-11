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
| 10 | [Resource Namespace and Interface Rationalization](#step-10--resource-namespace-and-interface-rationalization) | ✓ |
| 11 | [Factory Modernization and Service Boundaries](#step-11--factory-modernization-and-service-boundaries) | In Progress |

### Status Table

| Slice | Title | Status |
| :--- | :--- | :--- |
| 11.0 | [Factory, Construction, Hydration, and Service-Boundary Audit](11-factory-service-boundary-audit.md) | ✓ |
| 11.1 | Factory Interface Decision Pass | Done |
| 11.2 | AbstractFactory Removal and ConnectionFactory Modernization | ✓ |
| 11.2.1 | ConnectionFactory Namespace Canonicalization | ✓ |
| 11.3 | Resource Passive State and Hydration Cleanup | ✓ |
| 11.3.1 | Resource Getter Mutation Cleanup | ✓ |
| 11.4 | Factory Hydration Support | ✓ |
| 11.5 | Resource URI Helper Relocation | |
| 11.6 | Client Service Delegation (Phase 1: Folders) | |
| 11.7 | Client Service Delegation (Phase 2: Others) | |
| 11.8 | Documentation and Migration Cleanup | |
| 12 | [Token Storage Completion](#step-12--token-storage-completion) | Not Started |
| 13 | [API Fixture Realism and Contract Alignment](#step-13--api-fixture-realism-and-contract-alignment) | Not Started |
| 14 | [JWT/S2S Feasibility and Dependency Review](#step-14--jwts2s-feasibility-and-dependency-review) | Not Started |
| 15 | [JWT/S2S Implementation](#step-15--jwts2s-implementation) | Not Started |
| 16 | [Webhook Verification](#step-16--webhook-verification) | Not Started |
| 17 | [v1 Release Readiness](#step-17--v1-release-readiness) | Not Started |

## Recommended Sequencing
The steps should generally be followed in numerical order. Step 10 and 11 are closely related as factories often return interfaces. Step 13 must precede Step 14. Step 16 is the final gate.

---

## Step 10 — Resource Namespace and Interface Rationalization

See [Detailed Step 10 Tracker](10-resource-namespace-interface-rationalization.md) for the full implementation plan.

### Purpose
Audit and migrate remaining domain resource classes into the final v1 resource namespace, and rationalize resource interfaces such as `FileInterface`, `FolderInterface`, `GroupInterface`, `CollaborationInterface`, and related resource contracts to decide which are real v1 contracts and which are legacy one-class mirror interfaces.

### Scope
- Resource Namespace Cutover: Move resources to `Box\Resource`.
- Interface Rationalization: Remove mirror interfaces.
- Service Alignment: Update signatures and move constants to services.
- Migration Docs: Update documentation.

### Non-Goals
- Do not perform factory modernization here (Step 11).
- Do not implement JWT/S2S (Step 14).
- Do not perform broad resource behavior rewrites without tests.

### Acceptance Criteria
- All resources in `Box\Resource`.
- No one-class mirror interfaces remain.
- Box upload preflight/GCM scope behavior should be evaluated during API contract/upload hardening.
- `composer review` passes.

### Status Table

| Slice | Title | Status |
| :--- | :--- | :--- |
| 10.0 | [Tracker and Resource Surface Audit](10-resource-namespace-interface-rationalization.md#slice-100--tracker-and-resource-surface-audit) | ✓ |
| 10.1 | [User Resource Validation](10-resource-namespace-interface-rationalization.md#slice-101--user-resource-validation) | ✓ |
| 10.2 | [File Resource Namespace and Interface Rationalization](10-resource-namespace-interface-rationalization.md#slice-102--file-resource-namespace-and-interface-rationalization) | ✓ |
| 10.3 | [Folder Resource Namespace and Interface Rationalization](10-resource-namespace-interface-rationalization.md#slice-103--folder-resource-namespace-and-interface-rationalization) | ✓ |
| 10.4 | [Group and Collaboration Resource Rationalization](10-resource-namespace-interface-rationalization.md#slice-104--group-and-collaboration-resource-rationalization) | ✓ |
| 10.5 | [Shared Item and Event Resource Rationalization](10-resource-namespace-interface-rationalization.md#slice-105--shared-item-and-event-resource-rationalization) | ✓ |
| 10.6 | [Migration Docs and Baseline Cleanup](10-resource-namespace-interface-rationalization.md#slice-106--migration-docs-and-baseline-cleanup) | In Progress |
| 10.7 | [Final Integration Review](10-resource-namespace-interface-rationalization.md#slice-107--final-integration-review) | ✓ |

---

## Step 11 — Factory Modernization and Service Boundaries

### Status
- 11.1 | Factory Interface Decision Pass | ✓ |
- 11.2 | AbstractFactory Removal and ConnectionFactory Modernization | ✓ |
- 11.2.1 | ConnectionFactory Namespace Canonicalization | ✓ |
- 11.3 | Resource Passive State and Hydration Cleanup | ✓ |
- 11.3.1 | Resource Getter Mutation Cleanup | ✓ |
- 11.4 | Factory Hydration Support | ✓ |
- 11.5 | Resource URI Helper Relocation | |
- 11.6 | Client Service Delegation (Phase 1: Folders) | |
- 11.7 | Client Service Delegation (Phase 2: Others) | |
- 11.8 | Documentation and Migration Cleanup | |

### Purpose
Audit and modernize factory patterns and service boundaries after interface rationalization to ensure clear construction and operational responsibilities. This step addresses "architecture smells" identified during Step 10, specifically around factory interface proliferation and resource self-hydration.

### Scope
- **Factory Inventory and Rationalization**:
    - List every factory and factory interface.
    - Rationalize factory interfaces: retain only those representing meaningful public extension points or stable contracts.
    - Remove one-class mirror factory interfaces where they add complexity without value.
    - Review `AbstractFactory` consumers and usages.
- **Factory Pattern Modernization**:
    - Review static factory methods and generic `new $class($options)` behavior.
    - Replace legacy generic factories with explicit typed factories where useful.
    - Preserve factories that are clear v1 construction boundaries.
- **Resource Construction Policy**:
    - Audit resources with array-accepting/self-hydrating constructors.
    - Decide whether resource constructors should be passive for v1 (state-only).
    - Determine where hydration belongs: services, mappers, factories, or resource constructors.
    - Document and test any retained constructor hydration as an intentional ergonomic choice.
- **Service Boundaries & Resource Purity**:
    - Review and enforce service ownership of URI construction and API operations.
    - Remove resource dependencies on service constants or internal URI builders (resource purity).
    - Audit `Client` to decide which methods remain as high-level facade methods and which should delegate to dedicated services.
    - Move operation logic from `Client` to appropriate services.

### Non-Goals
- Do not remove useful factories just because they are simple.
- Do not bundle JWT/S2S implementation here.

### Acceptance Criteria
- Factory interfaces audited and either retained with rationale or removed.
- Resource constructor self-hydration audited and resolved or intentionally documented.
- Construction and hydration responsibilities documented.
- Resources no longer build their own API URIs.
- `Client` methods delegate to services for resource-specific operations.
- Removal of legacy factory traits or obsolete base classes.
- Public API and migration implications are captured.

### Draft Prompt Outline
> Refine Step 11: Factory Modernization and Service Boundaries.
> 1. Audit existing factories, factory interfaces, and resource constructors.
> 2. Rationalize factory interfaces; remove redundant mirror interfaces.
> 3. Modernize factory patterns to use explicit typed factories.
> 4. Resolve resource self-hydration vs. passive object design.
> 5. Move URI construction and API orchestration from Resources/Client to Services.
> 6. Remove legacy factory base classes/traits if they no longer serve v1.

---

## Step 12 — Token Storage Completion

### Purpose
Audit and finalize token storage behavior for v1. This ensures the SDK provides reliable, out-of-the-box token management for persistent integrations (PDO) and ephemeral usage (In-Memory), while preserving extension points for future framework integrations.

### Scope
- **Storage Inventory and Audit**:
    - Inventory all classes/interfaces under `src/Storage`.
    - Identify current consumers in services/client/auth/token flows.
    - Identify unused, incomplete, or misleading public classes.
    - Identify PHPStan baseline entries related to storage classes.
    - Identify any existing future notes about Symfony bundles, Doctrine, storage extension points, or framework integration.
- **Supported v1 Storage Implementation**:
    - Finalize supported v1 storage contracts.
    - Implement/complete In-Memory token storage (required for v1).
    - Implement/complete PDO token storage (required for v1).
    - Evaluate Filesystem token storage feasibility for v1.
    - Complete, remove, or explicitly defer unsupported/incomplete storage APIs.
- **Extension Points and Future Compatibility**:
    - Preserve and harden storage interfaces as intentional extension points.
    - Ensure storage contracts allow for future Symfony bundle and Doctrine-backed integrations.
    - Resolve missing-return and stale PHPStan baseline issues for storage classes.

### Non-Goals
- Do not implement the Symfony bundle or Doctrine integration in this step.
- Do not refactor core auth/client logic unless necessary for storage integration.

### Acceptance Criteria
- Supported v1 storage contracts are finalized.
- In-memory token storage is implemented/tested.
- PDO token storage is implemented/tested.
- Filesystem token storage is evaluated and either implemented/tested or explicitly deferred.
- Unsupported/incomplete storage APIs are either completed, removed, or clearly marked/deferred.
- Supported storage implementations have focused tests.
- Token store/retrieve/update/delete or revoke-related flows are tested as appropriate.
- Refresh-token persistence behavior is tested or explicitly documented.
- Extension-point expectations are documented.
- Missing-return and stale-baseline issues are resolved for storage classes.
- Migration/user docs explain final storage behavior.

---

## Step 13 — API Fixture Realism and Contract Alignment

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

## Step 14 — JWT/S2S Feasibility and Dependency Review

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

## Step 15 — JWT/S2S Implementation

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

## Step 16 — Webhook Verification

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

## Step 17 — v1 Release Readiness

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
- Step 12 Token Storage Completion complete.
- Step 13 API Fixture Realism complete enough for release confidence.
- Step 14 JWT/S2S feasibility complete.
- Step 15 JWT/S2S implementation complete.
- Step 16 Webhook Verification decision and implementation complete.
- Step 17 final release readiness complete.
- `composer review` passes.
- No known credential leaks.
- Migration docs/changelog accurate.

## Deferred / Post-v1 Candidates
- Advanced auto-pagination.
- Full endpoint parity (all Box APIs).
- Framework-specific bundles.
