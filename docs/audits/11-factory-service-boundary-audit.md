# Step 11 — Factory, Construction, Hydration, and Service-Boundary Audit

This audit document inventories the current state of factories, resource construction, hydration boundaries, and service boundaries as part of the v1.0 modernization.

## 1. Factory Inventory

| Class/Interface | Type | Implementation Count | Consumers | Behavior | Risk | Proposed Step 11 Action |
|---|---|---|---|---|---|---|
| `BoxClientFactory` | Infrastructure | 1 | External | Creates `Client` instance | Low | Retain; simplify constructor dependency injection. |
| `CollaborationFactory` | Resource | 1 | `Client` | Creates `Collaboration`; accepts `$options`; uses constructor hydration | Medium | Removed interface (one-class mirror); move hydration to factory in later slice. |
| `ConnectionFactory[Interface]` | Infrastructure | 1 | `Client` | Creates `ConnectionInterface`; implements `ConnectionFactoryInterface` | Medium | Retained modern `Box\Factory\ConnectionFactory`. Legacy `Box\Connection\ConnectionFactory` removed. |
| `FileFactory` | Resource | 1 | `Client` | Creates `File`; accepts `$options`; uses constructor hydration | Medium | Removed interface (one-class mirror); move hydration to factory in later slice. |
| `FolderFactory` | Resource | 1 | `Client` | Creates `Folder`; accepts `$options`; uses constructor hydration | Medium | Removed interface (one-class mirror); move hydration to factory in later slice. |
| `GroupFactory` | Resource | 1 | `Client` | Creates `Group`; accepts `$options`; uses constructor hydration | Medium | Removed interface (one-class mirror); move hydration to factory in later slice. |
| `TokenFactory[Interface]` | Infrastructure | 1 | `Client` | Creates `TokenInterface` | Medium | Retained interface. |
| `UserFactory` | Resource | 1 | `Client` | Creates `User`; accepts `$options`; `User` is modern (passive) | Low | Removed interface (one-class mirror). |

**Findings:**
- Most resource factories are "one-class mirror" interfaces that mostly wrap `new Resource($options)`.
- return types are inconsistent (some use interfaces, some use concrete classes).
- `User` resource is already modernized, but its factory still accepts `$options` and (presumably) hydrates.

## 2. AbstractFactory Audit

| Class | Usage | Behavior Provided | Status | Proposed Step 11 Action |
|---|---|---|---|---|
| `AbstractFactory` | N/A | Removed. | Legacy | Removed. `ConnectionFactory` is a simple concrete class. |

**Findings:**
- `AbstractFactory` is only used by `ConnectionFactory`.
- It represents a legacy architecture that is no longer needed.

## 3. Resource Constructor Self-Hydration Audit

| Class | Constructor Signature | Internal Hydration? | Consumers | Proposed Step 11 Direction |
|---|---|---|---|---|
| `Collaboration` | `__construct(?array $options)` | Yes (`Hydrator`) | `CollaborationFactory`, Tests | Remove constructor hydration; move to factory/service. |
| `File` | `__construct(?array $options)` | Yes (`Hydrator`) | `FileFactory`, `FileService`, Tests | Remove constructor hydration; move to factory/service. |
| `Folder` | `__construct(?array $options)` | Yes (`Hydrator`) | `FolderFactory`, `Client`, Tests | Remove constructor hydration; move to factory/service. |
| `Group` | `__construct(?array $options)` | Yes (`Hydrator`) | `GroupFactory`, `Client`, Tests | Remove constructor hydration; move to factory/service. |
| `User` | No constructor | No | `UserFactory`, `UserService` | Already passive. Retain. |

**Findings:**
- Most primary resources still self-hydrate in the constructor.
- This creates a tight coupling between resources and the `Hydrator`.
- Direction for v1: Resources should be passive.

## 4. Hydration Boundary Audit

Current hydration entry points:
- `Box\Mapper\Hydrator::hydrate()`: The core engine.
- `Box\Service\Service::hydrate()`: Wrapper around `Hydrator`.
- `Box\Resource\*::__construct()`: Directly instantiates `Hydrator`.
- `Box\Client::parseResponse()`: Uses `Hydrator` via `Service`.

**Risks:**
- Moving hydration out of constructors will break any code doing `new File(['id' => 123])`.
- Existing tests rely heavily on this behavior.

**Proposed Boundary:**
- `Hydrator` remains the engine.
- `Service` classes should own hydration for API responses.
- `Factories` should own hydration for user-provided arrays.
- `Resources` should be passive data objects.

## 5. Client/Service-Boundary Audit

| Method | Category | Current Responsibility | Proposed Step 11 Action |
|---|---|---|---|
| `getNew*` | Factory | Wraps factories | Keep as facade, ensure it uses modernized factories. |
| `getFolder*`, `addFolder`, etc. | Service Op | API calls, orchestration | Move logic to `FolderService`; `Client` delegates. |
| `getGroupMembershipList` | Service Op | API call | Move to `GroupService`. |
| `uploadFileToBox` | Service Op | API call | Move to `FileService`. |
| `getAccessToken`, `refreshToken` | Auth | Token management | Keep in `Client` (or move to an Auth service if needed later). |
| `query`, `search` | Generic | Low-level API access | Keep as protected/low-level helpers. |

**Findings:**
- `Client.php` is over 1200 lines and contains significant business logic.
- Many methods directly construct URIs and call `getConnection()`.

## 6. Resource URI-Construction Audit

| Resource | Method | Dependency | Proposed Step 11 Action |
|---|---|---|---|
| `Folder` | `getBoxFolderItemsUri` | `FolderService::ENDPOINT` | Move to `FolderService`. |
| `Group` | `getMembershipListUri` | `GroupService::ENDPOINT` | Move to `GroupService`. |

**Findings:**
- Resources are aware of API endpoints via service constants.
- This violates the passive resource principle.

## 7. Endpoint Constant Ownership

- Verified: `FolderService`, `FileService`, `GroupService`, `CollaborationService` all own their respective `ENDPOINT` constants.
- Finding: `Folder` resource still has `getBoxFolderItemsUri` method.

## 8. Step 11 Implementation Slicing Proposal

1. **Slice 11.1: Factory Interface Decision Pass**
    - Goal: Rationalize factory interfaces; decide removal vs retention.
    - Scope: `src/Factory/*`.
    - Outcome: One-class mirror resource factory interfaces removed. Infrastructure interfaces retained.
    - Status: Completed.

2. **Slice 11.2: AbstractFactory Removal and ConnectionFactory Modernization**
    - Goal: Remove `AbstractFactory` and modernize `ConnectionFactory`.
    - Scope: `src/Factory/AbstractFactory.php`, `src/Connection/ConnectionFactory.php`, `src/Exception/FactoryException.php`.
    - Outcome: `AbstractFactory` removed; `ConnectionFactory` modernized.
    - Status: Completed.

3. **Slice 11.3: Resource Passive State and Hydration Cleanup**
    - Goal: Remove constructor hydration from primary resources.
    - Scope: `File`, `Folder`, `Group`, `Collaboration`.
    - Acceptance: Constructors are empty or removed; `Hydrator` not called in resources.

4. **Slice 11.4: Factory Hydration Support**
    - Goal: Ensure factories can still handle array options.
    - Scope: `src/Factory/*`.
    - Acceptance: Factories handle the hydration previously done in constructors.

5. **Slice 11.5: Resource URI Helper Relocation**
    - Goal: Move URI construction logic from resources to services.
    - Scope: `Folder`, `Group`, `FolderService`, `GroupService`.
    - Acceptance: Resources have no knowledge of endpoints.

6. **Slice 11.6: Client Service Delegation (Phase 1: Folders)**
    - Goal: Move folder-related orchestration from `Client` to `FolderService`.
    - Scope: `Client`, `FolderService`.
    - Acceptance: `Client` methods call `FolderService`.

7. **Slice 11.7: Client Service Delegation (Phase 2: Others)**
    - Goal: Move remaining resource orchestration (Files, Groups, etc.) to services.
    - Scope: `Client`, `FileService`, `GroupService`, `CollaborationService`.
    - Acceptance: `Client` is a thin facade.

8. **Slice 11.8: Documentation and Migration Cleanup**
    - Goal: Update docs to reflect new construction patterns.
    - Scope: `docs/migration/*`, `README.md`.

## 9. Migration/Public API Considerations

- **Breaking Change:** `new Resource($options)` will no longer hydrate. Users must use factories or setters.
- **Breaking Change:** Factory interfaces may change return types (e.g., returning concrete types if interfaces were one-class mirrors).
- **Breaking Change:** Moved methods from `Client` to services (though facade methods will remain in `Client` where practical).
