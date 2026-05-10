# Step 10 — Resource Namespace and Interface Audit

## 1. Resource Namespace Inventory

The following resources are identified for migration to `Box\Resource`.

| Current Namespace | Class | Domain | Type | Target Namespace | Risk | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `Box\Resource` | `User` | User | Resource | `Box\Resource\User` | Low | Already migrated. |
| `Box\File` | `File` | File | Resource | `Box\Resource\File` | Medium | High usage in `FileService`. |
| `Box\Folder` | `Folder` | Folder | Resource | `Box\Resource\Folder` | Medium | High usage in `Client` and factories. |
| `Box\Collaboration` | `Collaboration` | Collaboration | Resource | `Box\Resource\Collaboration` | Low | Moved to flat resource namespace. |
| `Box\Group` | `Group` | Group | Resource | `Box\Resource\Group` | Low | Moved to flat resource namespace. |
| `Box\Event` | `Event` | Event | Resource | `Box\Resource\Event` | Low | Migrated in Step 10.5. |
| `Box\Event\Admin` | `AdminEvent` | Event | Resource | `Box\Resource\AdminEvent` | Low | Migrated in Step 10.5. |
| `Box\Event\User` | `UserEvent` | Event | Resource | `Box\Resource\UserEvent` | Low | Migrated in Step 10.5. |
| `Box\Item\SharedLink` | `SharedLink` | Shared Link | Resource | `Box\Resource\SharedLink` | Low | Migrated in Step 10.5. |
| `Box\Item\SharedLink\Permissions` | `Permissions` | Shared Link | Helper | `Box\Resource\SharedLink\Permissions` | Low | Migrated in Step 10.5. |
| `Box\Dto` | | | DTO | `Box\Dto\...` | Low | Requests/Responses already here. |
| `Box\Service` | | | Service | `Box\Service\...` | Low | Services remain here. |

## 1.1 Namespace Policy

- **Resources**: All Box domain resources (e.g., File, Folder, User) must live under `Box\Resource`. Sub-resources or domain-specific helpers (like `Permissions`) should follow this nesting.
- **DTOs**: Pure data transfer objects for requests and responses live under `Box\Dto`.
- **Services**: All business logic and API orchestration live under `Box\Service`.
- **Domain-Root Removal**: Namespaces like `Box\File`, `Box\Folder`, `Box\Group`, etc., are deprecated and will be removed in v1 after classes are moved.
- **Aliases**: No compatibility aliases will be maintained in the final v1 release. Migration guides will provide mapping.

## 2. Resource Interface Inventory

| Interface | Implementations | Mirror? | v1 Action | Migration Impact | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `FileInterface` | `File` | Yes | Remove | High | Used in `FileService`, `FileFactory`. |
| `FolderInterface` | `Folder` | Yes | Remove | High | Used in `FolderFactory`, `Client`. |
| `CollaborationInterface` | `Collaboration` | Yes | Remove | Medium | Removed in Step 10.4. |
| `GroupInterface` | `Group` | Yes | Remove | Medium | Removed in Step 10.4. |
| `SharedLinkInterface` | `SharedLink` | Yes | Remove | Low | Migrated in Step 10.5. |
| `PermissionsInterface` | `Permissions` | Yes | Remove | Low | Migrated in Step 10.5. |
| `EventInterface` | `Event` | Yes | Remove | Low | Migrated in Step 10.5. |
| `AdminEventInterface` | `AdminEvent` | Yes | Remove | Low | Migrated in Step 10.5. |
| `UserEventInterface` | `UserEvent` | Yes | Remove | Low | Migrated in Step 10.5. |
| `EventCollectionInterface` | `EventCollection` | Yes | Remove | Low | Migrated in Step 10.5. |

## 2.1 Interface Policy

- **Mirror Interfaces**: One-to-one mirror interfaces (e.g., `FileInterface` for `File`) are to be removed.
- **Contract Interfaces**: Interfaces that define multi-implementation contracts (e.g., `TokenInterface`, `ConnectionInterface`, `BoxResponseInterface`) are retained.
- **Service Interfaces**: `FileServiceInterface`, `UserServiceInterface`, etc., are retained as they define the SDK's primary service contracts.
- **Rationalization Order**: Interface removal must occur alongside or after the namespace move to ensure all type hints are updated to the new concrete resource classes.

## 3. Endpoint Constant Audit

| Constant | Current Location | Recommended v1 Location |
| :--- | :--- | :--- |
| `URI` | `FileInterface` | `FileService::ENDPOINT` |
| `UPLOAD_URI` | `FileInterface` | `FileService::UPLOAD_ENDPOINT` |
| `URI` | `FolderInterface` | `FolderService::ENDPOINT` |
| `SHARED_ITEM_URI` | `FolderInterface` | `FolderService::SHARED_ITEM_ENDPOINT` |
| `URI` | `CollaborationInterface` | `CollaborationService::ENDPOINT` (To be created) |
| `URI` | `GroupInterface` | `GroupService::ENDPOINT` |
| `MEMBERSHIP_URI` | `GroupInterface` | `GroupService::MEMBERSHIP_ENDPOINT` |
| `URI` | `AdminEventInterface` | `UserEventService::ENDPOINT` (Implicitly handled via stream_type) |
| `URI` | `UserEventInterface` | `UserEventService::ENDPOINT` |

## 3.1 Endpoint Constants Policy

- **Location**: Endpoint constants must live in Service classes, not Resource interfaces or classes.
- **Naming**: Use `ENDPOINT` or `[TYPE]_ENDPOINT` (e.g., `UPLOAD_ENDPOINT`).
- **Access**: Constants should be `public` to allow for inspection or custom transport usage.
- **Centralization**: Avoid duplicating endpoint strings across multiple services if they represent the same API endpoint.

## 4. Service/Factory Dependency Map

- **`FileService`**: Depends on `FileInterface`, `File`, `SharedLinkInterface`, `CreateSharedLinkRequest`.
- **`UserService`**: Correctly depends on `Box\Resource\User`.
- **`UserEventService`**: Depends on `UserEventInterface`, `AdminEventInterface`.
- **Factories**: `FileFactory`, `FolderFactory`, `CollaborationFactory`, `GroupFactory`, `UserFactory` all have `Interface` return types in their own interfaces.

## 5. Transitional Patterns and Notes

### 5.1 FileService Shared-Link Compatibility Bridge
`FileService::normalizeSharedLinkPayload()` uses `method_exists($sharedLink, 'toArray')` as a fallback.
- **Reason**: Supports legacy models that implement `SharedLinkInterface` but haven't been fully migrated to DTOs or the new `toArray()` implementation.
- **V1 Replacement**: Once `SharedLinkInterface` is removed and all resources/DTOs consistently implement `toArray()` (or are passed as DTOs), this fallback should be removed.
- **Removal Slice**: Step 10.5 (Shared Item and Event Rationalization) should evaluate if this can be safely removed.

### 5.2 FileServiceTest Explanatory Comments
`FileServiceTest` contains comments regarding PHPUnit mocking of `toArray()` and `SharedLinkInterface`.
- **Cleanup**: These comments and complex mocks should be simplified once `SharedLinkInterface` is removed and the resource namespace move is complete.
- **Action**: Part of Step 10.2 and 10.5 validation.

### 5.3 FileInterface Risk
`FileInterface` carries endpoint constants and is a mirror interface.
- **Status**: Targeted for removal in Step 10.2.
- **Pre-requisite**: Move constants to `FileService` and update all type hints in `Client`, `FileService`, and `FileFactory`.

## 6. Deferred Architecture Smells (For Step 11)

During Step 10, several architecture smells were identified that are non-blockers for namespace/interface rationalization but should be addressed during **Step 11 (Factory Modernization and Service Boundaries)**.

### 6.1 Resource Purity Issues
Resources should represent Box API data/state only. However, some resources currently:
- Construct API endpoint URIs internally (e.g., `Folder::getBoxFolderItemsUri`).
- Depend on Service classes or constants to build URIs.
- Import Service classes for type hints or constant access.

**Action**: Move URI construction and Service dependencies out of Resources and into dedicated Services/Mappers during Step 11.

### 6.2 Client/Service Boundary Issues
`Client.php` still contains API operation logic that belongs in dedicated services.
- **Example**: `Client` methods that perform resource-specific API calls rather than delegating to the appropriate `Service`.

**Action**: Audit `Client` and move resource API operation logic to services, leaving `Client` as a high-level facade/delegator.

### 6.3 Factory Interface Rationalization
Many resources have accompanying factory interfaces (e.g., `FileFactoryInterface`) that may be redundant if they only have one implementation and don't represent a meaningful extension point.
- **Action**: Audit all factory interfaces and remove those that are simple mirrors of concrete factories unless they are needed for dependency inversion in v1.

### 6.4 Resource Self-Hydration
Most resources currently accept `?array $options` in their constructor and call `Hydrator::hydrate()`. This blurs the line between a data object and a hydration-aware object.
- **Action**: Decide if resource constructors should be passive (no internal hydration) or if this ergonomic bridge should be retained for v1.

## 7. Recommended Migration Sequence

1. **Step 10.1**: Validate `Box\Resource\User`. Confirm it is the template for others.
2. **Step 10.2**: Migrate `Box\File\File` to `Box\Resource\File`.
    - Move constants to `FileService`.
    - Remove `FileInterface`.
    - Update `FileService`, `FileFactory`, `Client`.
3. **Step 10.3**: Migrate `Box\Folder\Folder` to `Box\Resource\Folder`.
    - Move constants to `FolderService` (needs to be created or use `Client`).
    - Remove `FolderInterface`.
    - Update `FolderFactory`, `Client`.
4. **Step 10.4**: Migrate `Group` and `Collaboration`.
    - Move to `Box\Resource`.
    - Remove interfaces.
    - Update factories.
5. **Step 10.5**: Migrate `SharedLink` and `Event`.
    - Move to `Box\Resource`.
    - Update `UserEventService`, `EventResponseMapper`.
6. **Step 10.6**: Finalize docs and baseline cleanup.
7. **Step 11**: Address deferred architecture smells (Resource purity and Service boundaries).
