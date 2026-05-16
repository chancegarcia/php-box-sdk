# v1 Release Roadmap

**Strategic Status: JWT/S2S Implementation (Step 15)**

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
| 13 | [Auth Lifecycle/Auth Provider Extraction](#step-13--auth-lifecycleauth-provider-extraction) | ✓ |
| 13.0 | [Auth Lifecycle/Auth Provider Extraction Discovery](docs/audits/13-auth-lifecycle-provider-extraction-audit.md) | ✓ |
| 13.1 | [Roadmap Step Naming and Documentation Drift Cleanup](#step-131--roadmap-step-naming-and-documentation-drift-cleanup) | ✓ |
| 13.2 | [Guzzle Default Transport Cleanup](#step-132--guzzle-default-transport-cleanup) | ✓ |
| 13.3 | [Connection Interface Modernization (Step 13.3)](#step-133--connection-interface-modernization-step-133) | ✓ |
| 13.4 | [Authenticated Request Boundary Cleanup](#step-134--authenticated-request-boundary-cleanup) | ✓ |
| 13.5 | [AuthProvider Extraction (OAuth2)](#step-135--authprovider-extraction-oauth2) | ✓ |
| 13.6 | [Client Facade and Legacy Surface Review](#step-136--client-facade-and-legacy-surface-review) | ✓ |
| 14 | [JWT/S2S Feasibility and Dependency Review](#step-14--jwts2s-feasibility-and-dependency-review) | ✓ |
| 15 | [JWT/S2S Implementation](#step-15--jwts2s-implementation) | ✓ |
| 15.1 | [Dependency and Core JWT Support](#step-151--dependency-and-core-jwt-support) | ✓ |
| 15.2 | [JwtProvider Implementation](#step-152--jwtprovider-implementation) | ✓ |
| 15.3 | [Factory and Client Integration](#step-153--factory-and-client-integration) | ✓ |
| 15.4 | [CLI Support and Env Var Alignment](#step-154--cli-support-and-env-var-alignment) | ✓ |
| 15.4.1 | [FilesystemTokenStorage CLI Support](#step-1541--filesystemtokenstorage-cli-support) | ✓ |
| 15.4.2 | [Dependency Audit and Cleanup](#step-1542--dependency-audit-and-cleanup) | ✓ |
| 15.4.3 | [Symfony Invoke-Style Command Refactor](#step-1543--symfony-invoke-style-command-refactor) | ✓ |
| 15.4.4 | [ClientConfig Architectural Cleanup](#step-1544--clientconfig-architectural-cleanup) | ✓ |
| 15.5 | [Box API Coverage Alignment](#step-155--box-api-coverage-alignment) | ✓ |
| 15.6 | [API Fixture Realism and Contract Alignment](#step-156--api-fixture-realism-and-contract-alignment) | ✓ |
| 16 | [Webhook Verification and Evaluation](#step-16--webhook-verification-and-evaluation) | ✓ |
| 19 | [Chunked Upload + PSR-14 Events](#slice-19--chunked-upload--psr-14-events) | ✓ |
| 20 | [Human Code Review & Cleanup Feedback](#slice-20--human-code-review--cleanup-feedback) | Not Started |
| 20.5 | [Enum Wiring & Hydrator Audit](#slice-205--enum-wiring--hydrator-audit) | Not Started |
| 21 | [Docblock Quality & Legacy Tag Cleanup](#slice-21--docblock-quality--legacy-tag-cleanup) | Not Started |
| 22 | [License & Rebrand Preparation](#slice-22--license--rebrand-preparation) | Not Started |
| 17 | [v1 Release Readiness](#step-17--v1-release-readiness) | Not Started |
| 18 | [Documentation Cleanup and Organization](#step-18--documentation-cleanup-and-organization) | Not Started |

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

## Step 13 — Auth Lifecycle/Auth Provider Extraction [✓]

### Purpose
Move auth lifecycle responsibilities out of `Client` into a dedicated provider/boundary to prepare for JWT/S2S work.

### Status Table

| Slice | Title | Status |
| :--- | :--- | :--- |
| 13.0 | Auth Lifecycle/Auth Provider Extraction Discovery | ✓ |
| 13.1 | Roadmap Step Naming and Documentation Drift Cleanup | ✓ |
| 13.2 | Guzzle Default Transport Cleanup | ✓ |
| 13.3 | [Connection Interface Modernization (Step 13.3)](#step-133--connection-interface-modernization-step-133) | ✓ |
| 13.4 | [Authenticated Request Boundary Cleanup](#step-134--authenticated-request-boundary-cleanup) | ✓ |
| 13.5 | [AuthProvider Extraction (OAuth2) (Step 13.5)](#step-135--authprovider-extraction-oauth2-step-135) | ✓ |
| 13.6 | [Client Facade and Legacy Surface Review](#step-136--client-facade-and-legacy-surface-review) | ✓ |
- Final audit of `Client` surface and auth-adjacent APIs.
- Performed Semantic Naming and Human-Readable API Clarity Review.
- Collapsed `Service::$connection` and `Service::$authorizedConnection` into a single `connection` property.
- Removed legacy auth shims: `ConnectionInterface::getAuthorizationHeader()`, `ServiceInterface::getAuthorizedConnection()`, `ServiceInterface::getConnectionHeaders()`.
- Removed legacy auth shims: `Client::getAuthorizationHeader()`, `Client::setTokenData()`, `Client::setConnectionAuthHeader()`, `Client::auth()`, `Client::buildAuthQuery()`.
- Verified token storage boundaries remain intact.
- Auth Lifecycle/Auth Provider Extraction (Step 13) is complete.
- Legacy service `authorizedConnection` and token-lifecycle methods were removed, not merely deprecated.
- OAuth2 dynamic `state` support was verified and tested.
- Updated tests to cover connection state collapse and facade changes.
- **Guzzle Default Transport**: Make Guzzle the default and only bundled transport path.
- **Connection Boundary**: Cleaned up `ConnectionInterface` to remove transport-specific (curl) leakage.

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
- **Complete** ✓

#### Slice Status

| Slice | Title | Status |
| :--- | :--- | :--- |
| 15.1 | Dependency and Core JWT Support | ✓ |
| 15.2 | JwtProvider Implementation | ✓ |
| 15.3 | Factory and Client Integration | ✓ |
| 15.4 | CLI Support and Env Var Alignment | ✓ |
| 15.4.1 | FilesystemTokenStorage CLI Support | ✓ |
| 15.4.2 | Dependency Audit and Cleanup | ✓ |

#### Purpose
Implement JWT/S2S authentication based on the feasibility study.

#### Key Decisions Made
- **Separate credential env vars**: `BOX_OAUTH_*` for OAuth2, `BOX_JWT_*` for JWT. No shared `BOX_CLIENT_ID` — avoids cross-contamination between Box app registrations.
- **Private key file read**: `EnvConfigProvider` reads the PEM file at `BOX_JWT_PRIVATE_KEY_PATH` and returns content. `JwtAuthConfig::$privateKey` is always PEM, never a path. Keeps `JwtAssertionGeneratorInterface` extension point clean.
- **Transport flag removed from CLI**: `--transport` option removed from `AbstractBoxCommand`. `ConnectionInterface::setTransportName/getTransportName` retained for programmatic consumer use.
- **CLI storage**: `--storage-type memory` removed; `--storage-type` flag kept with only `pdo` valid in Slice 15.4. `FilesystemTokenStorage` and `--storage-type filesystem` (plus `--storage-path`) added in Slice 15.4.1 (PDO requires a database; memory is process-scoped and useless across CLI invocations).
- **OAuth2 method rename**: `ConfigProviderInterface`, `EnvConfigProvider`, and `ClientConfig` getters renamed from `getClientId()` to `getOAuth2ClientId()` etc. for symmetry with `getJwtClientId()`. All callers updated in Slice 15.4.
- **Symfony invoke-style commands**: All CLI commands to be refactored to `#[AsCommand]` + `__invoke()` style (Symfony 7.4+ preferred). Added as Slice 15.4.3.

#### Scope
- Implement JWT signing and token exchange.
- Integrate with `AuthProvider` / `Connection` / `Client` flows.
- **JWT Configuration**: Separate `BOX_JWT_*` env vars; `JwtAuthConfig` DTO.
- **Service Account Support**: Enterprise and App User auth modes via `JwtProvider`.
- **CLI**: `box:jwt:token` command; `BOX_AUTH_MODE` detection; env var alignment.
- **Security**: Redact `private_key`, `private_key_passphrase`, `assertion` in CLI output.
- **Tests**: All tests use placeholder fixtures only.

#### Acceptance Criteria
- Working JWT/S2S authentication flow.
- Unit tests cover signing, response handling, and configuration.
- CLI supports JWT token exchange.

---

## Step 15.4.1 — FilesystemTokenStorage CLI Support

#### Status
- **Required for v1** (revised from original "excluded" decision in Step 12 audit)
- Implementation has **NOT** started.

#### Purpose
Implement `FilesystemTokenStorage` as a lightweight, database-free persistent token storage
option. PDO is heavyweight for local development; in-memory storage is process-scoped and useless
for CLI use across multiple commands. A JSON file on disk is the right middle ground.

#### Scope
- `src/Storage/FilesystemTokenStorage.php` — implements `TokenStorageInterface`.
  Constructor accepts a file path. Tokens stored as a JSON map keyed by `TokenStorageContext`
  identifier (or a default key when no context is provided). Operations: load, save, remove.
- `AbstractBoxCommand` — add `--storage-path` option alongside `--use-storage`. When
  `--use-storage` is provided and `--storage-path` is set, use `FilesystemTokenStorage`;
  otherwise use PDO (requires `BOX_STORAGE_PDO_*` env vars).
- Env var: `BOX_STORAGE_FILE_PATH` as fallback when `--storage-path` is not passed.
- Tests for `FilesystemTokenStorage` and CLI option wiring.

#### Non-Goals
- Do not implement locking or concurrent-write safety (single-user CLI use case).
- Do not implement encryption at rest.

#### Acceptance Criteria
- `FilesystemTokenStorage` implements `TokenStorageInterface` and passes all storage contract tests.
- CLI `--storage-path` option selects filesystem storage.
- `composer review` passes.

---

## Step 15.4.2 — Dependency Audit and Cleanup

#### Status
- **Required for v1**
- Implementation has **NOT** started.

#### Purpose
Audit `composer.json` dependencies for correctness and remove anything no longer needed after
the Step 13 transport cleanup and Step 7 foundation work.

#### Scope
1. **`ext-curl`**: `CurlTransport` was removed in Step 13.2. Grep `src/` for `curl_` function
   calls. If none remain, remove `ext-curl` from `require`.
2. **`symfony/http-foundation`**: Step 7 established that `BoxResponse` must not inherit from
   `HttpFoundation\Response`. Grep `src/` for `use Symfony\Component\HttpFoundation`. If no
   usages remain, remove from `require`.
3. **PHP constraint**: Confirm `>=8.4` is correct. Verify no source code uses features
   deprecated or removed in PHP 8.5.
4. **Symfony constraint format**: Confirm all `symfony/*` packages use `^7.4|^8` consistently.
5. **`ext-fileinfo`**: Verify it is still actively called in `src/`.

#### Acceptance Criteria
- `composer.json` requires only packages and extensions that are actually used.
- PHP and Symfony version constraints reflect actual support intent.
- `composer review` passes.

---

## Step 15.4.3 — Symfony Invoke-Style Command Refactor

#### Status
- **Required for v1**
- Implementation has **NOT** started.

#### Purpose
Update all CLI commands to use the Symfony 7.4+ preferred invokable command style:
`#[AsCommand]` attribute on the class and `__invoke()` instead of `execute()`. This was not
found in docs (possibly remembered from another project) but is the correct v1 goal given the
Symfony `^7.4|^8` dependency.

#### Scope
- Add `#[AsCommand(name: 'box:...', description: '...')]` attribute to each command class.
- Replace `configure()` + `execute()` with `__invoke(InputInterface $input, OutputInterface $output): int`.
- Update `AbstractBoxCommand` if it requires changes to support the new style in subclasses.
- Update all command tests to work with the refactored style.

#### Non-Goals
- Do not introduce a DI container or service locator. Commands are still wired manually in
  `bin/box-sdk`.

#### Acceptance Criteria
- All commands use `#[AsCommand]` attribute and `__invoke()`.
- `bin/box-sdk` still wires them manually (no framework container).
- `composer review` passes.

---

## Step 15.4.4 — ClientConfig Architectural Cleanup

#### Status
- **Required for v1**
- **Complete** ✓

#### Purpose
`ClientConfig` currently implements `ConfigProviderInterface`, which is architecturally wrong:
a DTO should not implement a service interface. This forces `ClientConfig` to stub out methods
it doesn't own (upload paths, PDO DSN, JWT credentials, etc.) and creates misleading empty-string
returns where callers would expect exceptions. Discovered during Slice 15.4 review.

#### Scope
- Remove `implements ConfigProviderInterface` from `ClientConfig`. Make it a plain DTO.
- Remove stub methods that only exist to satisfy the interface:
  `getRefreshToken()`, `getAccessToken()`, `getUploadFilePath()`, `getUploadFolderId()`,
  `getStoragePdoDsn()`, `getStoragePdoUser()`, `getStoragePdoPassword()`,
  `getAuthMode()`, `getJwtClientId()`, `getJwtClientSecret()`, `getJwtEnterpriseId()`,
  `getJwtPublicKeyId()`, `getJwtPrivateKey()`, `getJwtPrivateKeyPassphrase()`.
- Remove legacy mobile-API fields: `getDeviceId()`, `getDeviceName()`, `setDeviceId()`,
  `setDeviceName()`.
- Resolve alias inconsistency: remove `getAuthorizationCode()` (keep `getOAuth2AuthCode()`).
- Evaluate the magic array-hydration constructor: replace with explicit named parameters or
  keep with strict key validation — decision required.
- Update `Client` and any other callers that currently type-hint `ConfigProviderInterface`
  where they actually only need `ClientConfig`. Narrow type hints accordingly.
- Update all affected tests.

#### Non-Goals
- Do not change `EnvConfigProvider` in this slice (it already correctly implements the interface).
- Do not merge `ClientConfig` and `EnvConfigProvider`.

#### Acceptance Criteria
- `ClientConfig` is a pure OAuth2 DTO with no interface stubs.
- No caller passes `ClientConfig` where `ConfigProviderInterface` is required.
- `composer review` passes.

---

## Step 15.5 — Box API Coverage Alignment

#### Status
- **Required for v1 release**
- Implementation has **NOT** started.

#### Purpose
Audit the current SDK against current Box API documentation to ensure core resources provide high value per resource. This is about depth of support for core resources, not full API parity.

#### Scope
1. **Audit**:
    - Compare current `FileService`, `FolderService`, `UserService`, `GroupService`, `CollaborationService`, and `EventService` against current Box API docs.
    - Identify missing common/basic CRUD operations or important fields.
    - **Service Base Modernization**: Perform a modernization review of the `Service` base class. Address obsolete state (`clientId`, `clientSecret`, `deviceId`, `deviceName`), legacy response return-mode helpers, broad base-service request helpers (`queryBox`, etc.), `refreshConnection()` residue, and hydration/response handling mixed into the base service. Verify if services are focused API-operation classes with clear typed returns and if tests preserve obsolete behavior.
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

## Slice 19 — Chunked Upload + PSR-14 Events

### Status
- **Complete** ✓

### Summary
Added chunked file upload (low-level session API on `FileService` + `chunkedUpload()` orchestrator on `Client`) and PSR-14 event infrastructure across the full SDK surface. All 9 gates complete:

1. PSR-14 infrastructure ✓
2. FileStream additions ✓
3. DTOs ✓
4. FileService low-level API ✓
5. Orchestrator ✓
6. Client facade ✓
7. Tests ✓
8. Documentation ✓
9. Additional PSR-14 events (token lifecycle, `FileUploaded`, `RateLimitHit`, `JwtTokenGenerated`) ✓

Also included: typed return refactor (`array` → typed objects) for 7 service methods; `PagedResult<T>` and `GroupMembership` resources; `api-coverage.md` user doc; migration guide Section 9.

---

## Slice 20 — Human Code Review & Cleanup Feedback

### Status
- **Not Started**

### Purpose
Apply cleanup items identified during human code review of Slice 19 output. No new features — correctness, typing, and style alignment only.

### Scope

#### Typed Constants
Audit `src/` for untyped class constants and add PHP 8.3+ type declarations where possible. Example: `public const string ENDPOINT = '...'`.

#### Type Coverage Audit
Review `src/` for remaining `mixed`, untyped properties, untyped return types, and untyped parameters. Tighten where the actual type is known and not intentionally `mixed`.

#### Property Hooks
Audit DTO and value-object classes (`src/Dto/`, `src/Resource/`, `src/Connection/Token/`) for get/set method pairs that qualify for replacement with PHP 8.4 property hooks. Apply where all conditions hold:
1. Class is a DTO or value object (data-only; no interface method contracts declaring getters/setters)
2. Property is part of the public API (accessed as `$obj->prop`, not `$obj->getProp()`)
3. Hook logic is lightweight — normalization, type coercion, or a simple guard; no service calls, no side-effects beyond the property itself
4. No fluent setter chain (`return $this`) needed

Do not apply hooks where the class implements an interface with getter/setter method contracts, where setters have side-effects, or where complexity warrants a named method.

#### BoxClientFactory Namespace Move *(may be its own slice)*
Move `Box\Service\BoxClientFactory` → `Box\Factory\BoxClientFactory`. Rename `createClient()` → `createOAuth2Client()`. Update `BoxClientFactoryInterface` in tandem. Update all callers, tests, and migration guide.

### Acceptance Criteria
- All touched constants have type declarations
- No unintentional `mixed` in `src/` (documented exceptions acceptable)
- Qualifying DTOs/VOs use property hooks
- `composer review` green
- Migration guide updated if any public API changes

---

## Slice 20.5 — Enum Wiring & Hydrator Audit

### Status
- **Not Started**

### Purpose
Wire the existing orphaned enum classes to their resource setters, create the missing `CollaborationStatus` enum, and verify that the Hydrator handles backed enum properties correctly so API-hydrated objects don't silently break.

> **Note**: `FolderSyncState` is **out of scope**. Box API docs confirm `sync_state` is a legacy field from the discontinued Box Sync client (not Box Drive). It is still accepted by the API but has no meaningful effect in modern integrations. `Folder::classArray` docblock documents this; no enum is warranted.

### Background
Four enums exist in `src/Enum/`. Only `UserStatus` is wired to a resource (`User::setStatus(?UserStatus)`). The other three are orphaned:

| Enum | Should wire to |
|---|---|
| `CollaborationRole` | `Collaboration::setRole` |
| `SharedLinkAccess` | `SharedLink` (property + setter/getter) |
| `BoxItemType` | Evaluate — may be documentation-only or used in service layer |

One fixed-set string-validation setter needs an enum created:

| Location | Valid values | New enum |
|---|---|---|
| `Collaboration::setStatus` | `accepted`, `pending`, `rejected` | `CollaborationStatus` |

`Folder::classArray`'s `$syncState` parameter is a legacy field from the discontinued Box Sync client — no enum warranted; the docblock already documents this.

### Pre-flight

**Step 1 — Hydrator audit (required before wiring any enum)**

Confirm how the Hydrator handles backed enum properties. `User::setStatus(?UserStatus)` is already wired — test whether the Hydrator correctly calls `UserStatus::from($value)` or fails on a raw string from JSON. If it fails, fix the Hydrator first; all subsequent wiring depends on it.

```
grep -n "BackedEnum\|from\|tryFrom\|enum" src/Mapper/Hydrator.php
```

Run `tests/Model/Mapper/HydratorTest.php` with a `User` having a `status` field set to a valid string to verify behavior.

**Step 2 — Wire orphaned enums**

After confirming the Hydrator:
1. Wire `Collaboration::setRole(?CollaborationRole)` — remove `?string`, update docblock, update callers.
2. Wire `SharedLink` with `SharedLinkAccess` — evaluate property type and setter/getter.
3. Evaluate `BoxItemType` — determine if it needs wiring or remains a utility enum.

**Step 3 — Create missing enum**

```php
// src/Enum/CollaborationStatus.php
enum CollaborationStatus: string
{
    case Accepted = 'accepted';
    case Pending  = 'pending';
    case Rejected = 'rejected';
}
```

Wire `Collaboration::setStatus(?CollaborationStatus)` — replace string `in_array` validation.

**Step 4 — PathCollection DTO**

Box API returns `path_collection` on both `File` and `Folder` responses as:
```json
{ "total_count": 2, "entries": [ { mini-folder }, { mini-folder } ] }
```

Create `src/Dto/PathCollection.php`:
```php
class PathCollection
{
    /** @param Folder[] $entries */
    public function __construct(
        public readonly int $totalCount,
        public readonly array $entries,
    ) {}
}
```

- Update `File::setPathCollection` to accept `PathCollection|null` and remove the `// Post-v1:` comment.
- Update `File::getPathCollection` return type accordingly.
- Update `Folder` if it also has a `path_collection` field.
- Verify Hydrator handles nested hydration correctly.

**Step 5 — SharedLink array narrowing**

After confirming the Hydrator correctly hydrates `SharedLink` from array (part of Step 1), narrow `File::setSharedLink` to `?SharedLink` and remove the `// Post-v1:` comment.

**Step 6 — Tests & migration**

- Update any tests passing raw strings to enum-typed setters.
- Add migration guide entry: callers passing `string` role/status to `Collaboration` setters must switch to the enum.
- `composer review` must pass.

### Acceptance Criteria
- `Hydrator` correctly hydrates backed enum properties from API strings (`BackedEnum::tryFrom` or equivalent)
- `CollaborationRole`, `CollaborationStatus`, `SharedLinkAccess` wired to their resource setters
- `CollaborationStatus` enum created
- `BoxItemType` evaluated — decision recorded
- No raw string `in_array` validation remains in resource setters (excluding `Folder::classArray` `$syncState` — legacy field, no enum warranted)
- `PathCollection` DTO created; `File::setPathCollection` narrowed
- `File::setSharedLink` narrowed to `?SharedLink` (pending Hydrator confirmation)
- `composer review` green
- Migration guide updated

---

## Slice 21 — Docblock Quality & Legacy Tag Cleanup

### Status
- **Not Started**

### Purpose
Address remaining docblock quality issues and legacy annotation patterns not covered by Slice 20's type/throws audit. No new features — correctness and consistency only.

### Scope

#### `@inheritdoc` Correctness
Audit all `@inheritDoc` / `{@inheritdoc}` uses in `src/`. Ensure each is on a method that actually overrides or implements a parent/interface method. Remove or replace where wrong or awkward.

Pre-flight:
```
grep -rn "@inheritdoc\|{@inheritDoc}" src/ --include="*.php"
```

#### `@package` / `@subpackage` Removal
Remove all `@package` and `@subpackage` tags from `src/` and `tests/`.

**Decision**: Do not replace with `@psalm-package`/`@psalm-subpackage`. PSR-4 namespaces serve this purpose. `@psalm-package` is a Psalm visibility modifier, not a grouping tag. We use PHPStan, not Psalm.

Pre-flight:
```
grep -rln "@package\|@subpackage" src/ tests/ --include="*.php"
```

#### `ConnectionInterface` / `EntrySource` Architectural Review
Re-examine `ConnectionInterface` and `EntrySource` with current eyes. Inline deprecation/removal notes were added during earlier refactors. Determine for each symbol whether it is still v1-sound, should be removed now, or should be deferred with a documented rationale.

#### `json_encode` / `json_decode` Hardening
Audit all `json_encode` and `json_decode` calls in `src/`. Add `JSON_THROW_ON_ERROR` to every call. Catch `JsonException` at appropriate boundaries and translate to domain exceptions where needed.

Pre-flight:
```
grep -rn "json_encode\|json_decode" src/ --include="*.php"
```

#### Legacy Survivor Audit
Scan `src/` for code or comments that represent pre-v1 legacy paths that survived earlier purges. Specific known items:

- **`Connection::post` `$nameValuePair` / array `$params`**: Both `Connection.php` and `ConnectionInterface.php` carry "will be deprecated in the future" warnings. For a v1 release "future" is now — decide: remove the array-params and `$nameValuePair` paths, or document as intentionally supported and remove the "deprecated" language.
- **`FileService` / `FolderService` `method_exists($sharedLink, 'toArray')` fallback**: `FileService.php:145` and `FolderService.php:124` contain a guard labeled "Fallback for legacy models that might not be fully hydrated to DTOs yet." `CreateSharedLinkRequest` has `toArray()`, so the branch is live, but the comment implies dead models. Confirm whether the fallback is still needed; remove the comment and tighten the type if not.

Pre-flight:
```
grep -rn "deprecated\|legacy\|nameValuePair\|method_exists" src/ --include="*.php"
```

### Acceptance Criteria
- No `@package`/`@subpackage` tags remain in `src/` or `tests/`
- `@inheritdoc` usage is correct and consistent
- `ConnectionInterface`/`EntrySource` decision recorded
- All `json_encode`/`json_decode` calls pass `JSON_THROW_ON_ERROR`
- `nameValuePair`/array-params decision made and implemented
- Legacy `method_exists` fallback in `FileService`/`FolderService` resolved
- No "will be deprecated in the future" language remains in a v1 release
- `composer review` green

---

## Slice 22 — License & Rebrand Preparation

### Status
- **Not Started**

### Purpose
Transition the license from MIT to Apache-2.0, clean up per-file license headers, add the relicense note to user-facing docs, create the Packagist transition guide, and add the remote URL update task to the pre-release checklist.

### Scope

#### LICENSE File
Replace `LICENSE` content with the full Apache 2.0 license text.

#### `composer.json`
Change `"license": "MIT"` → `"license": "Apache-2.0"`.

#### File-Level License Header Removal
**Decision**: Remove per-file copyright/license blocks entirely. Apache 2.0 does not require per-file notices; the root `LICENSE` file satisfies the license. Do not add `@license Apache-2.0` tags as a replacement.

Pre-flight:
```
grep -rln "MIT License\|MIT license\|@license" src/ tests/ --include="*.php"
```

#### README & CHANGELOG Note
Add the following to the v1.0.0 section of both `README.md` and `CHANGELOG.md`:

> Note: Starting with v1.0.0, this SDK has been rebranded and transitioned from the MIT License to the Apache 2.0 License to provide better patent and trademark protections.

#### Remote URL Update Task
Add a checklist item in `docs/planning/release-task-lists.md` to update the remote repository URL to the new branded repo (performed by the user at rename time — not performed in this slice).

#### Packagist Transition Guide
Create `docs/planning/packagist-rebrand-guide.md` documenting the full Packagist abandonment/redirect process for the rebrand. See `docs/ai/next-session-plan.md` § Slice 22 for full content.

### Acceptance Criteria
- `LICENSE` contains Apache 2.0 text
- `composer.json` `"license"` is `"Apache-2.0"`
- All per-file MIT license blocks removed from `src/` and `tests/`
- README and CHANGELOG include the v1 relicense note
- `docs/planning/release-task-lists.md` has a remote URL update item
- `docs/planning/packagist-rebrand-guide.md` created
- `composer review` green

---

## Step 17 — v1 Release Readiness

### Purpose
Final polish and validation before tagging v1.0.0.

### Scope

#### Code Gate
- **Service Base Modernization Gate**: Confirm the `Service` base class deferred helpers (`queryBox`, `putIntoBox`, `getFromBox`, `sendUpdateToBox`) and legacy constants are either cleaned up or explicitly documented as deferred to v2. No new cleanup required — just verify the decision is recorded.
- Full validation (`composer review` must pass 100%).

#### Documentation Gate
- **`docs/README.md`**: Fix status drift — mark Steps 10–16 complete, Step 17 in progress.
- **`docs/migration/upgrading-0.11-to-1.0.md`**: Extend with sections for token storage (PDO/filesystem/in-memory), JWT/S2S configuration, and webhook verification.
- **`docs/user/programmatic-usage.md`**: Add JWT/S2S programmatic usage examples (enterprise token, app user token).
- **`docs/user/cli-test-harness.md`**: Document `--storage-type filesystem` / `--storage-path` / `BOX_STORAGE_FILE_PATH`; JWT CLI commands.
- **CHANGELOG.md**: Write v1.0.0 changelog covering all Steps 7–17.

#### Release Metadata Gate
- `composer.json`: bump `version` to `1.0.0`, confirm `description`, `keywords`, PHP `>=8.4` constraint, Symfony `^7.4|^8` constraint.
- Security scan: grep `src/`, `tests/`, `docs/` for any accidentally committed real credentials, keys, DSNs, or tokens.

### Acceptance Criteria
- `composer review` passes 100%.
- Migration guide covers token storage, JWT/S2S, and webhook sections.
- CHANGELOG.md written.
- `composer.json` at `1.0.0`.
- No credential leaks found.
- No known release blockers.

---

## Step 18 — Documentation Cleanup and Organization

### Purpose
v1.0.0 housekeeping — part of the v1 release, not deferred. Reduce noise in the `docs/` directory so the next maintenance phase starts with a clean, navigable structure. This step does not change any code.

### Scope

#### Archive Completed Step Trackers
Move to `docs/archive/steps/` (or delete after confirming no forward references):
- `docs/audits/08-service-layer-hardening-audit.md`
- `docs/audits/10-resource-namespace-interface-audit.md`
- `docs/audits/11-factory-service-boundary-audit.md`
- `docs/audits/11-v1-service-coverage-auth-boundary-audit.md`
- `docs/audits/12-token-storage-completion-audit.md`
- `docs/audits/13-auth-lifecycle-provider-extraction-audit.md`
- `docs/audits/14-jwt-s2s-feasibility-audit.md`
- `docs/planning/07-foundation-refinement.md`
- `docs/planning/08-service-layer-hardening.md`
- `docs/planning/09-legacy-architecture-removal.md`
- `docs/planning/10-resource-namespace-interface-rationalization.md`

#### Retire Superseded Files
Delete or archive (confirmed superseded by better documents):
- `docs/audits/box-api-endpoint-coverage.md` — superseded by `docs/audits/15.5-api-coverage-matrix.md`
- `docs/planning/v1/api-coverage-audit.md` — same
- `docs/planning/v1/interface-and-model-audit.md` — pre-implementation; implementation complete
- `docs/audits/model-typing-decisions.md` — v0.11 era; all decisions implemented
- `docs/audits/model-signature-audit.md` — v0.11 era; all recommendations implemented
- `docs/planning/v1/implementation-checklist.md` — superseded by `v1-release-roadmap.md`
- `docs/prompts/generic-phpstorm-ai-chat-prompt-delivery-format.md` — outdated tooling

#### Archive AI Workflow Prompts
Move to `docs/archive/prompts/` (Claude Code CLI memory supersedes):
- `docs/prompts/ai-workflow/` (entire directory)
- `docs/prompts/changelog-prompt.md`

#### Fix Remaining Status Drift
- `docs/planning/release-task-lists.md` — mark completed v1 tasks; remove or archive v0.11 section
- `docs/planning/v1/overview.md` — mark as historical
- `docs/ai/current-task-summary.md` — update or retire after v1 tag

#### Navigation Improvements
- Update `docs/README.md` to reflect final v1 state: completed steps, current canonical docs, archived references
- Update `docs/planning/README.md` canonical source map to reflect what was archived

### Non-Goals
- Do not change any source code.
- Do not delete audit docs that are still referenced by active planning documents.
- Do not restructure `docs/user/` or `docs/migration/` (covered in Step 17).

### Acceptance Criteria
- `docs/` root and `docs/planning/` contain only current, forward-looking files.
- Archived files are in `docs/archive/` with a clear index.
- `docs/README.md` accurately represents the current doc structure.
- No broken internal links introduced.

---

## Release Blockers
- Step 10 Resource Namespace and Interface Rationalization complete. ✓
- Step 11 Factory Modernization and Service Boundaries complete. ✓
- Step 12 Token Storage Completion and Integration complete. ✓
- Step 13 Auth Lifecycle/Auth Provider Extraction complete. ✓
- Step 14 JWT/S2S feasibility complete. ✓
- Step 15 JWT/S2S implementation complete. ✓
- Step 15.5 API Coverage Alignment complete. ✓
- Step 15.6 API Fixture Realism complete enough for release confidence. ✓
- Step 16 Webhook Verification decision and implementation complete. ✓
- Step 17 v1 Release Readiness complete. ✓
- Step 18 Documentation Cleanup and Organization complete. ✓
- `composer review` passes. ✓
- No known credential leaks. ✓
- Migration docs / changelog accurate. ✓

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
- **Content-MD5 integrity header on file upload**: Box API supports an optional `content-md5` header on `POST /files/content` that Box verifies against the uploaded content. Deferred because computing the MD5 before/during upload adds non-trivial effort for an optional feature. Tracked in `Connection::postFile` via `// Post-v1:` comment.
- **Multi-file upload convenience method**: No native batch upload endpoint exists in the Box API — multiple files require repeated single-file calls. A PHP loop wrapper on `Client` is trivial but not a v1 gap. Tracked in `Client` via `// Post-v1:` comment.
- Advanced auto-pagination.
- Full endpoint parity (all Box APIs).
- Framework-specific bundles.
- **Symfony `#[Argument]`/`#[Option]` parameter attributes on `__invoke()`**: Symfony Console's
  native invokable command support (`InvokableCommand`) processes these attributes, but only when
  (a) no subclass overrides `execute()`, and (b) the command definition has no pre-registered
  arguments or options. Both conditions are blocked by our architecture: `AbstractBoxCommand`
  overrides `execute()` as a bridge, and its `configure()` registers shared options. Enabling
  this would require either a DI container (which resolves commands without the bridge) or a
  significant restructure of the base class. Revisit if a DI container is added post-v1.
- **Docblock consistency and polish pass**: v1 acceptance bar is accuracy — `@throws` tags present and correct, param/return types matching actual types. A full consistency pass (uniform style, complete `@param` descriptions on all public methods, `@return` descriptions, prose summaries) is deferred to a post-v1 maintenance cycle.
