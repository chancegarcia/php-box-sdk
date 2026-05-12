# v1 Release Roadmap

**Strategic Status: Authenticated Request Boundary Cleanup (Step 13.4)**

Roadmap reference: v1 Steps 10–17

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
| 11 | [Factory Modernization and Service Boundaries](#step-11--factory-modernization-and-service-boundaries) | ✓ |

### Status Table

| Slice | Title | Status |
| :--- | :--- | :--- |
| 11.0 | [Factory, Construction, Hydration, and Service-Boundary Audit](11-factory-service-boundary-audit.md) | ✓ |
| 11.1 | Factory Interface Decision Pass | ✓ |
| 11.2 | AbstractFactory Removal and ConnectionFactory Modernization | ✓ |
| 11.2.1 | ConnectionFactory Namespace Canonicalization | ✓ |
| 11.3 | Resource Passive State and Hydration Cleanup | ✓ |
| 11.3.1 | Resource Getter Mutation Cleanup | ✓ |
| 11.4 | Factory Hydration Support | ✓ |
| 11.5 | Resource URI Helper Relocation | ✓ |
| 11.6 | Client Service Delegation (Phase 1: Folders) | ✓ |
| 11.6.1 | [v1 Service Coverage and Auth Boundary Audit](docs/audits/11-v1-service-coverage-auth-boundary-audit.md) | ✓ |
| 11.6.2 | Auth Boundary Hardening (AuthenticatedServiceInterface) | ✓ |
| 11.6.3 | Service Interface and Client Boundary Cleanup | ✓ |
| 11.7 | Client Service Delegation (Phase 2: Others) | ✓ |
| 11.7.1 | Client Registry Usage and v1 Test Alignment Cleanup | ✓ |
| 11.7.2 | Client Facade Readability, Resource Boundary, and Type Cleanup | ✓ |
| 11.7.3 | Mixed Type Reduction and Client Factory Convenience Review | ✓ |
| 11.8 | Documentation, Migration, and Planning Drift Cleanup | ✓ |
| 11.9 | Final Integration Review, Code/Plan Conformance, and New-Chat Handoff | ✓ |
| 12 | [Token Storage Completion and Integration](#step-12--token-storage-completion-and-integration) | ✓ |
| 13 | [Auth Lifecycle/Auth Provider Extraction](#step-13--auth-lifecycleauth-provider-extraction) | In Progress |
| 13.0 | [Auth Lifecycle/Auth Provider Extraction Discovery](docs/audits/13-auth-lifecycle-provider-extraction-audit.md) | ✓ |
| 13.1 | [Roadmap Step Naming and Documentation Drift Cleanup](#step-131--roadmap-step-naming-and-documentation-drift-cleanup) | ✓ |
| 13.2 | [Guzzle Default Transport Cleanup](#step-132--guzzle-default-transport-cleanup) | ✓ |
| 13.3 | [Connection Interface Modernization (Step 13.3)](#step-133--connection-interface-modernization-step-133) | ✓ |
| 13.4 | Authenticated Request Boundary Cleanup | Next |
| 15 | [JWT/S2S Implementation](#step-15--jwts2s-implementation) | Not Started |
| 15.1 | [Box API Coverage Alignment](#step-151--box-api-coverage-alignment) | Not Started |
| 15.2 | [API Fixture Realism and Contract Alignment](#step-152--api-fixture-realism-and-contract-alignment) | Not Started |
| 16 | [Webhook Verification and Evaluation](#step-16--webhook-verification-and-evaluation) | Not Started |
| 17 | [v1 Release Readiness](#step-17--v1-release-readiness) | Not Started |

## Remaining v1 Sequence

| Order | Work item | v1 classification | Depends on | Expected outcome |
|---:|---|---|---|---|
| 1 | **Token Storage Completion** | Required | Step 11 | Passive storage (PDO/In-Memory); Client orchestration; Service independence. |
| 2 | **Auth Lifecycle Extraction** | Required | Step 12 | Dedicated `AuthProvider` component; Client coordinates auth+storage. |
| 3 | **JWT / Server-to-Server Auth** | Required | Step 13 (Auth boundary) | RSA-4096 signing; Enterprise/App User support; Config/Tests/Docs. |
| 4 | **API Coverage Alignment** | Required | Step 15 | Audit SDK vs Box API; Prioritize core resource value; Matrix produced. |
| 5 | **API Fixture Realism** | Required | Step 11 | Realistic fixtures for core resources (File, Folder, User, Group). |
| 6 | **Webhook Evaluation** | Evaluation | Step 15.1 | Verification implementation; Decision to include/defer full service. |
| 7 | **Release Readiness** | Required | Step 16 | Final docs, changelog, security scan, `composer review`. |

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

## Step 11 — Factory Modernization and Service Boundaries [✓]

### Status
- 11.1 | Factory Interface Decision Pass | ✓ |
- 11.2 | AbstractFactory Removal and ConnectionFactory Modernization | ✓ |
- 11.2.1 | ConnectionFactory Namespace Canonicalization | ✓ |
- 11.3 | Resource Passive State and Hydration Cleanup | ✓ |
- 11.3.1 | Resource Getter Mutation Cleanup | ✓ |
- 11.4 | Factory Hydration Support | ✓ |
- 11.5 | Resource URI Helper Relocation | ✓ |
- 11.6 | Client Service Delegation (Phase 1: Folders) | ✓ |
- 11.6.1 | [v1 Service Coverage and Auth Boundary Audit](docs/audits/11-v1-service-coverage-auth-boundary-audit.md) | ✓ |
- 11.6.2 | Auth Boundary Hardening (AuthenticatedServiceInterface) | ✓ |
- 11.6.3 | Service Interface and Client Boundary Cleanup | ✓ |
- 11.7 | Client Service Delegation (Phase 2: Others) | ✓ |
- 11.7.1 | Client Registry Usage and v1 Test Alignment Cleanup | ✓ |
- 11.7.2 | Client Facade Readability, Resource Boundary, and Type Cleanup | ✓ |
- 11.7.3 | Mixed Type Reduction and Client Factory Convenience Review | ✓ |
- 11.8 | Documentation, Migration, and Planning Drift Cleanup | ✓ |
- 11.9 | Final Integration Review, Code/Plan Conformance, and New-Chat Handoff | ✓ |

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

### Step 12 — Token Storage Completion and Integration

#### Status
- **Required for v1 release**
- **In Progress**: Planning and Audit completed.
- See [Step 12 Audit and Plan](docs/audits/12-token-storage-completion-audit.md) for detailed requirements and slices.

#### Slices

| Slice | Title | Status |
| :--- | :--- | :--- |
| 12.0 | [Token Storage Audit and Planning](docs/audits/12-token-storage-completion-audit.md) | ✓ |
| 12.1 | Storage Contract Finalization | ✓ |
| 12.2 | In-Memory Storage Completion | ✓ |
| 12.3 | PDO Storage Implementation | ✓ |
| 12.4 | Service Storage-Independence Cleanup | ✓ |
| 12.5 | Client Integration Hooks | ✓ |
| 12.6 | CLI/Auth Harness Storage Integration | ✓ |
| 12.7 | Type-Safety, Docs, and Final Review | ✓ |

#### Purpose
Audit and finalize token storage behavior for v1. This ensures the SDK provides reliable, out-of-the-box token management for persistent integrations (PDO) and ephemeral usage (In-Memory), while ensuring storage is properly integrated with the Client configuration and CLI/harness flows.

#### Scope
1. **Token Storage Completion**:
    - Finalize `TokenStorageInterface` for v1.
    - Implement/complete `InMemoryTokenStorage` (required).
    - Implement/complete `PdoTokenStorage` (required).
    - Evaluate `FilesystemTokenStorage` (support, defer, or exclude).
    - Ensure all storage is **passive** (no network, no refresh logic).
    - Support `TokenStorageContext` (one active token per context).
2. **Client Orchestration Integration**:
    - Review and implement Client configuration for token storage.
    - Ensure Client can load tokens from storage to initialize auth state.
    - Ensure Client can persist refreshed tokens back to storage via `AuthProvider` callbacks or coordinated flow.
3. **Service Independence Verification**:
    - Verify that Services remain completely independent of token storage.
4. **CLI/Auth Harness Review and Configuration**:
    - Review CLI token storage configuration (options, env/config, or config provider).
    - Ensure CLI can run and be discovered without storage configured.
    - Confirm CLI commands (Resource and Auth) follow the documented auth resolution priority.
    - Determine if CLI persistence is implemented through Client orchestration or explicit command orchestration.
    - Implement or explicitly defer CLI persistence with a clear rationale.
    - **Step 12 Design Decision**: Backend selection mechanism and context selection for CLI.
5. **Static Analysis**:
    - Resolve storage-related PHPStan/static-analysis issues.

#### Non-Goals
- Do not implement Symfony bundle or Doctrine integration.
- Do not implement JWT/S2S in this step (Step 14/15).
- Do not refactor core auth logic unless required for orchestration.

#### Acceptance Criteria
- `TokenStorageInterface` is finalized for v1.
- In-memory and PDO storage are complete and tested.
- Filesystem storage decision is documented.
- Token storage remains strictly passive.
- Services do not depend on token storage.
- Client coordinates token load/refresh/persist convenience flow.
- CLI token storage is optional/configurable and does not block command discovery.
- CLI resource commands follow auth resolution order (Storage -> Explicit -> Graceful Failure).
- Auth exchange/refresh commands are storage-aware when storage is configured.
- CLI/auth harness persistence is reviewed and either implemented or explicitly deferred.
- No secrets are introduced in docs, tests, fixtures, or summaries.
- PHPStan/static-analysis issues for storage code are resolved.

---

## Step 13 — Auth Lifecycle/Auth Provider Extraction [In Progress]

### Purpose
Move auth lifecycle responsibilities out of `Client` into a dedicated provider/boundary to prepare for JWT/S2S work.

### Status Table

| Slice | Title | Status |
| :--- | :--- | :--- |
| 13.0 | Auth Lifecycle/Auth Provider Extraction Discovery | ✓ |
| 13.1 | Roadmap Step Naming and Documentation Drift Cleanup | ✓ |
| 13.2 | Guzzle Default Transport Cleanup | ✓ |
| 13.3 | [Connection Interface Modernization (Step 13.3)](#step-133--connection-interface-modernization-step-133) | ✓ |
| 13.4 | Authenticated Request Boundary Cleanup | Next |
- Move authorization-code exchange, refresh, and revoke/destroy orchestration behind an `AuthProviderInterface`.
- Preserve `Client` as a facade while reducing direct auth orchestration inside it.
- Maintain compatibility for current OAuth2 usage via a default provider.
- **Guzzle Default Transport**: Make Guzzle the default and only bundled transport path.
- **Connection Boundary**: Clean up `ConnectionInterface` to remove transport-specific (curl) leakage.

### Non-Goals
- Do not implement JWT/S2S in this step (Step 14/15).
- Do not change token storage persistence contracts.

### Acceptance Criteria
- `AuthProviderInterface` defined.
- `Client` delegates auth lifecycle operations to an auth provider.
- All existing auth tests pass with the new boundary.

---

## Step 14 — JWT/S2S Feasibility and Dependency Review

#### Status
- **Required for v1 release**
- Implementation has **NOT** started.

#### Purpose
Evaluate requirements and dependencies for implementing Box JWT/S2S authentication. This is a primary v1 feature, not an optional follow-up.

#### Scope
- Review Box JWT/S2S auth requirements (RSA-4096, signing, etc.).
- Identify crypto dependencies (e.g., OpenSSL).
- Plan DTOs for configuration and token storage.
- Produce implementation slices for Step 15.
- **Client Integration**: Plan how JWT configuration (Enterprise ID, App Auth, Private Key) integrates with `Client` and `Connection`.

#### Acceptance Criteria
- Feasibility report documented.
- Clear implementation plan for Step 15.

---

## Step 15 — JWT/S2S Implementation

#### Status
- **Required for v1 release**

#### Purpose
Implement JWT/S2S authentication based on the feasibility study.

#### Scope
- Implement JWT signing and token exchange.
- Integrate with `AuthProvider` / `Connection` / `Client` flows.
- **JWT Configuration**: Support JWT-specific config (Client ID/Secret, Enterprise ID, Public Key ID, Private Key, Passphrase).
- **Service Account Support**: Support Enterprise and App User auth modes.
- **CLI/Harness**: Evaluate or implement CLI support for JWT auth.
    - **Deferred JWT/S2S CLI configuration note**: When JWT/S2S auth is implemented, evaluate whether the CLI/auth harness should support separate environment-variable groups or named auth profiles for OAuth2 versus JWT credentials. This would allow CLI testing of JWT and OAuth2 without manually swapping shared `BOX_CLIENT_ID` / `BOX_CLIENT_SECRET` values and reduce the risk of mismatched credential pairs or accidentally combining OAuth2 and JWT configuration. Do not implement this during Step 12 token storage unless a later approved plan explicitly includes CLI auth profile work.
- **Security**: Redaction review for private keys, client secrets, assertions, and tokens.
- **Tests**: Add tests using placeholder fixtures only.

#### Acceptance Criteria
- Working JWT/S2S authentication flow.
- Unit tests cover signing, response handling, and configuration.
- Public docs and migration notes include JWT usage.

---

## Step 15.1 — Box API Coverage Alignment

#### Status
- **Required for v1 release**
- Implementation has **NOT** started.

#### Purpose
Audit the current SDK against current Box API documentation to ensure core resources provide high value per resource. This is about depth of support for core resources, not full API parity.

#### Scope
1. **Audit**:
    - Compare current `FileService`, `FolderService`, `UserService`, `GroupService`, `CollaborationService`, and `EventService` against current Box API docs.
    - Identify missing common/basic CRUD operations or important fields.
2. **Prioritization**:
    - **Required for v1 baseline**: Files, Folders, Users, Groups, Collaborations, Shared Links, Search, Events.
    - **Evaluate for v1 if feasible**: Comments, Tasks, Metadata, Webhooks, Collections.
    - **Defer**: Specialized enterprise/compliance APIs, large new API families.
3. **Outcome**:
    - Produce an endpoint coverage matrix.
    - Explicitly defer unsupported/non-core endpoints.
    - Ensure core resources provide more value per supported resource than prior releases.

#### Acceptance Criteria
- Endpoint coverage matrix produced.
- Core resources (Files, Folders, Users, Groups, Collabs) audited and aligned with basic Box API CRUD.
- Unsupported endpoints explicitly deferred in documentation.

---

## Step 15.2 — API Fixture Realism and Contract Alignment

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

## Step 16 — Webhook Verification and Evaluation

### Purpose
Implement Box webhook signature verification and evaluate whether a full `WebhookService` is required for v1.

### Scope
- Implement signature verification logic (Security requirement).
- Provide a utility or service for verifying incoming webhook requests.
- **Evaluation**: Decide if full webhook management (CRUD via API) is required for v1 or can be deferred.

### Acceptance Criteria
- Verification logic implemented and tested with mock signatures.
- Decision on v1 Webhook management recorded in `decision-index.md`.

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
- Step 10 Resource Namespace and Interface Rationalization complete. ✓
- Step 11 Factory Modernization and Service Boundaries complete. ✓
- Step 12 Token Storage Completion and Integration complete. ✓
- Step 13 Auth Lifecycle/Auth Provider Extraction complete.
- Step 14 JWT/S2S feasibility complete.
- Step 15 JWT/S2S implementation complete.
- Step 15.1 API Coverage Alignment complete.
- Step 15.2 API Fixture Realism complete enough for release confidence.
- Step 16 Webhook Verification decision and implementation complete.
- Step 17 final release readiness complete.
- `composer review` passes.
- No known credential leaks.
- Migration docs/changelog accurate.

| Capability | v1 status | Notes |
|---|---|---|
| OAuth2 authorization-code auth | Required / existing | Keep compatible |
| OAuth2 refresh-token auth | Required / existing | Auth layer refreshes; Client may coordinate |
| Passive token storage | Required / Step 12 | Storage only loads/saves |
| Client token-storage orchestration | Required / Step 12 | Client convenience path |
| Service-level manual auth control | Required / architectural boundary | Services stay storage-independent |
| CLI/auth harness token persistence | Evaluate in Step 12 | Implement or explicitly defer |
| PDO token storage | Required / Step 12 | Tested |
| In-memory token storage | Required / Step 12 | Tested |
| Filesystem token storage | Evaluate in Step 12 | Support/defer/exclude decision |
| JWT / Server-to-Server auth | Required before v1 | Dedicated required v1 step (Steps 14-15) |

## Deferred / Post-v1 Candidates
- Advanced auto-pagination.
- Full endpoint parity (all Box APIs).
- Framework-specific bundles.
