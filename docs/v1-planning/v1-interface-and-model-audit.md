# V1.0 Interface and Model Audit

This document audits the existing interfaces and models in `src/Model` and core resource namespaces against V1.0 architecture rules.

## 1. Full Interface Inventory

Audit of every interface under `src`.

| Interface | File Path | Current Purpose | Known Implementations | V1.0 Classification | Rationale | Likely Migration Step |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `ConfigProviderInterface` | `src/Contract/ConfigProviderInterface.php` | Provides configuration settings. | None in src | **Keep** | Infrastructure boundary for configuration. | None (already in `Box\Contract`). |
| `BoxClientFactoryInterface` | `src/Contract/BoxClientFactoryInterface.php` | Factory for creating Box clients. | `BoxClientFactory` | **Keep** | Infrastructure boundary for client creation. | Move to `Box\Contract` (already there). |
| `JsonFormatterInterface` | `src/Contract/JsonFormatterInterface.php` | Interface for JSON formatting. | None in src | **Keep** | Infrastructure boundary for serialization. | None. |
| `LoggerAwareInterface` | `src/Logger/LoggerAwareInterface.php` | Interface for logger awareness. | Many classes | **Replace** | Standard PSR-3 `LoggerAwareInterface` or standard trait. | Replace with `Psr\Log\LoggerAwareInterface`. |
| `TokenInterface` | `src/Connection/Token/TokenInterface.php` | Represents an OAuth2 token. | `Token` | **Keep** | Infrastructure boundary for auth tokens. | Move to `Box\Connection\Token`. |
| `ConnectionInterface` | `src/Connection/ConnectionInterface.php` | Core transport/connection logic. | `Connection` | **Keep** | Infrastructure boundary for HTTP communication. | Refactor to remove resource-model dependencies. |
| `AuthenticationResponseInterface` | `src/Connection/Response/AuthenticationResponseInterface.php` | Auth response wrapper. | `AuthenticationResponse` | **Keep** | Infrastructure/DTO boundary. | Move to `Box\Connection\Response`. |
| `BaseTokenStorageInterface` | `src/Storage/Token/BaseTokenStorageInterface.php` | Base token storage. | `Pdo\TokenStorage` | **Keep** | Infrastructure boundary for token persistence. | Move to `Box\Storage\Token`. |
| `TokenStorageInterface` | `src/Storage/Token/Pdo/TokenStorageInterface.php` | PDO-specific token storage. | `Pdo\TokenStorage` | **Keep** | Infrastructure boundary for PDO storage. | None. |
| `TransportInterface` | `src/Http/Transport/TransportInterface.php` | HTTP transport abstraction. | `GuzzleTransport` | **Keep** | Infrastructure boundary for HTTP client. | None. |
| `BoxResponseInterface` | `src/Http/Response/BoxResponseInterface.php` | Response wrapper. | `BoxResponse` | **Keep** | Infrastructure boundary for responses. | None. |
| `ResponseHeaderInterface` | `src/Http/Response/Header/ResponseHeaderInterface.php` | HTTP header wrapper. | `ResponseHeader` | **Keep** | Infrastructure boundary. | None. |
| `StatusLineInterface` | `src/Http/Response/Header/StatusLineInterface.php` | HTTP status line wrapper. | `StatusLine` | **Keep** | Infrastructure boundary. | None. |
| `PermissionsInterface` | `src/Item/SharedLink/Permissions/PermissionsInterface.php` | Shared link permissions. | `Permissions` | **Replace** | Resource/DTO abstraction. | Replace with concrete `Box\Resource\Permissions` or `Box\Dto\Permissions`. |
| `BaseModelInterface` | `src/Model/BaseModelInterface.php` | Base interface for models. | Many classes | **Remove** | God-abstraction part. | Remove after migrating children. |
| `BoxModelInterface` | `src/Model/BoxModelInterface.php` | Interface for Box models. | Many classes | **Remove** | God-abstraction part. | Remove after migrating children. |
| `ModelInterface` | `src/Model/ModelInterface.php` | Core model interface. | Many classes | **Remove** | God-abstraction part. | Remove after migrating children. |
| `ServiceInterface` | `src/Service/ServiceInterface.php` | Base service interface. | `Service` | **Keep/Replace** | Service boundary. | Refactor to remove `BaseModelInterface` dependency. |
| `FileServiceInterface` | `src/Service/File/FileServiceInterface.php` | File operations. | `FileService` | **Keep** | Service boundary. | Refactor signatures to use concrete classes. |
| `UserEventServiceInterface` | `src/Service/Event/UserEventServiceInterface.php` | Event operations. | `UserEventService` | **Keep** | Service boundary. | Refactor signatures. |
| `FileInterface` | `src/File/FileInterface.php` | File resource mirror. | `File` | **Remove** | Mirrors concrete model; contains URI constants. | Replace usages with `Box\Resource\File`. |
| `FolderInterface` | `src/Folder/FolderInterface.php` | Folder resource mirror. | `Folder` | **Remove** | Mirrors concrete model; contains URI constants. | Replace usages with `Box\Resource\Folder`. |
| `UserInterface` | `src/User/UserInterface.php` | User resource mirror. | `User` | **Remove** | Mirrors concrete model; contains URI constants. | Replace usages with `Box\Resource\User`. |
| `GroupInterface` | `src/Group/GroupInterface.php` | Group resource mirror. | `Group` | **Remove** | Mirrors concrete model; contains URI constants. | Replace usages with `Box\Resource\Group`. |
| `CollaborationInterface` | `src/Collaboration/CollaborationInterface.php` | Collaboration mirror. | `Collaboration` | **Remove** | Mirrors concrete model; contains URI constants. | Replace usages with `Box\Resource\Collaboration`. |
| `EventInterface` | `src/Event/EventInterface.php` | Event mirror. | `Event` | **Remove** | Mirrors concrete model. | Replace usages with `Box\Resource\Event`. |
| `EventCollectionInterface` | `src/Event/Collection/EventCollectionInterface.php` | Event collection mirror. | `EventCollection` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\EventCollection` (or DTO). |
| `SharedLinkInterface` | `src/Item/SharedLink/SharedLinkInterface.php` | Shared link mirror. | `SharedLink` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\SharedLink` or `Box\Dto\SharedLink`. |
| `EntryInterface` | `src/Event/Collection/Entry/EntryInterface.php` | Event entry mirror. | `Entry` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\EventEntry`. |
| `AdminEntryInterface` | `src/Event/Collection/Entry/AdminEntryInterface.php` | Admin entry mirror. | `AdminEntry` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\AdminEventEntry`. |
| `UserEntryInterface` | `src/Event/Collection/Entry/UserEntryInterface.php` | User entry mirror. | `UserEntry` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\UserEventEntry`. |
| `SourceInterface` | `src/Event/Collection/Entry/Source/SourceInterface.php` | Event source mirror. | `Source` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\EventSource`. |
| `AdminEventInterface` | `src/Event/Admin/AdminEventInterface.php` | Admin event mirror. | `AdminEvent` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\AdminEvent`. |
| `UserEventInterface` | `src/Event/User/UserEventInterface.php` | User event mirror. | `UserEvent` | **Remove** | Mirrors concrete model. | Replace with `Box\Resource\UserEvent`. |
| `FileFactoryInterface` | `src/Factory/FileFactoryInterface.php` | Factory interface. | `FileFactory` | **Uncertain** | Factory for resource creation. | Keep if used for mockability, else remove. |
| `UserFactoryInterface` | `src/Factory/UserFactoryInterface.php` | Factory interface. | `UserFactory` | **Uncertain** | Factory for resource creation. | Keep if used for mockability, else remove. |
| `ConnectionFactoryInterface` | `src/Factory/ConnectionFactoryInterface.php` | Factory interface. | `ConnectionFactory` | **Keep** | Infrastructure boundary. | None. |

## 2. Resource Interface Usage Impact

| Removed Interface | Replacement | Files Affected | Service Methods | Tests Affected |
| :--- | :--- | :--- | :--- | :--- |
| `FileInterface` | `Box\Resource\File` | `FileService`, `File`, `FileFactory` | `createSharedLink`, `createNewFile` | `FileServiceTest`, `FileTest` |
| `FolderInterface` | `Box\Resource\Folder` | `FolderService`, `Folder`, `FolderFactory` | `createFolder` (implied) | `FolderTest` |
| `UserInterface` | `Box\Resource\User` | `UserService`, `User`, `UserFactory` | `getCurrentUser`, `updateUser` | `UserTest` |
| `GroupInterface` | `Box\Resource\Group` | `GroupService`, `Group`, `GroupFactory` | `getGroups`, `getGroupMemberships` | `GroupTest` |
| `CollaborationInterface` | `Box\Resource\Collaboration` | `CollaborationService`, `Collaboration` | `getCollaborations` | `CollaborationTest` |

**Impact Notes:**
- **Endpoint Constants**: All `URI` constants (e.g., `FileInterface::URI`) must move to the corresponding `Box\Service` classes (e.g., `FileService::ENDPOINT`).
- **URL Building**: Methods like `GroupInterface::getMembershipListUri()` must move to `GroupService`.
- **Request DTOs**: Create/Update operations should move from passing resource objects to passing `Create[Resource]Request` DTOs.
- **Enums**: Fields like `role`, `status`, `type` should be migrated to PHP Enums (e.g., `Box\Enum\CollaborationRole`).

## 3. src/Model Dependency Audit

| Class/Trait/Interface | Direct Dependencies | Dependents | Responsibilities | V1.0 Recommendation |
| :--- | :--- | :--- | :--- | :--- |
| `BaseModel` | `ModelMapper`, `BaseModelTrait` | `Model`, `Service` | Dynamic properties via `__call`, hydration helper. | **Remove**. Replace with explicit properties in resources. |
| `Model` | `BaseModel`, `ModelTrait` | All resource models | Base for API resources, participates in hydration. | **Remove**. Resources should be passive. |
| `BoxModel` | `Model`, `BoxModelInterface` | Core resources | Common Box fields (id, name, created_at, etc). | **Remove**. Use traits or small base class in `Box\Base`. |
| `ModelTrait` | `ModelMapper`, `BoxLoggerTrait` | `Model` | `toBoxArray`, `buildQuery`. | **Remove/Split**. Move `buildQuery` to HTTP/Service; `toBoxArray` to Hydrator. |
| `BaseModelTrait` | `ModelMapper`, `LoggerAwareTrait` | `BaseModel` | `mapBoxToClass`, `toBoxVar`. | **Remove**. Move hydration logic to `Box\Mapper\Hydrator`. |
| `BoxModelTrait` | N/A | `BoxModel` | Box-specific field logic. | **Remove**. Split into focused resource properties. |

**Audit Findings:**
- **Magic Behavior**: `BaseModel` uses `__call` for dynamic properties. This must be eliminated for type safety.
- **Hydration**: Models currently "know" how to hydrate themselves via traits. V1.0 must move this to `Box\Mapper\Hydrator`.
- **API Behavior**: Models currently own knowledge of Box field naming (`toBoxVar`). This should be centralized in the Mapper.

## 4. API Behavior Audit

| Resource/Method | Logic Type | Current Location | V1.0 Destination |
| :--- | :--- | :--- | :--- |
| `FileInterface::URI` | Endpoint URI | `FileInterface` | `FileService::ENDPOINT` |
| `FolderInterface::URI` | Endpoint URI | `FolderInterface` | `FolderService::ENDPOINT` |
| `GroupInterface::URI` | Endpoint URI | `GroupInterface` | `GroupService::ENDPOINT` |
| `GroupInterface::getMembershipListUri()` | URL Building | `GroupInterface` | `GroupService::getMembershipListUri()` |
| `Folder::getBoxFolderItemsUri()` | URL Building | `Folder` | `FolderService::getFolderItemsUri()` |
| `ModelTrait::buildQuery()` | Request Building | `ModelTrait` | `Box\Http\RequestFactory` or `Service` |
| `File::getExtension()` | Resource Logic | `File` | **Keep** in `Box\Resource\File` (non-API behavior). |

## 5. Hydration and Mapping Audit

- **Current Flow**: `Service` calls `Model->mapBoxToClass()`, which uses `ModelMapper::mapBoxToClass()`, which uses `Hydrator`. Models are active participants.
- **Target V1.0 Flow**: `Service` receives raw response, calls `Hydrator->hydrate(Resource::class, $data)`. Resources are passive DTOs/objects.
- **Key Changes**:
    - Remove `mapBoxToClass` from models and traits.
    - Resources should only have typed properties and standard getters/setters (returning `void`).
    - `Hydrator` should use reflection (already does) but without requiring model-level methods.
    - Remove `ModelMapper` dependency from models.

## 6. API Reference Alignment

See `docs/v1-planning/v1-api-coverage-audit.md` for detailed alignment.

## 7. Proposed Migration Plan

### Step 1: Base Layer & Infrastructure
- **Goal**: Establish the new namespace skeleton and infrastructure.
- **Scope**: `Box\Resource`, `Box\Dto`, `Box\Enum`, `Box\Service`, `Box\Http`.
- **Files**: Create directories, migrate `Transport`, `Connection`, `Hydrator`.
- **Risk**: Low.

### Step 2: Core Enums
- **Goal**: Define fixed API value sets.
- **Scope**: `BoxItemType`, `CollaborationRole`, `UserStatus`, etc.
- **Risk**: Low.

### Step 3: Users Resource (Safest Starting Point)
- **Goal**: Migrate `Box\User\User` to `Box\Resource\User`.
- **Scope**: Remove `UserInterface`, move constants to `UserService`, update hydration.
- **Risk**: Low.

### Step 4: Groups & Memberships
- **Goal**: Migrate `Box\Group` resources.
- **Scope**: Fold in `GroupMembership` as a first-class resource.
- **Risk**: Moderate (relationships with Users).

### Step 5: Files & Folders (High Complexity)
- **Goal**: Migrate core Box items.
- **Scope**: Fold in `FileVersion`, `PathCollection` DTO, `Permissions` DTO.
  - `PathCollection` should be a focused DTO. It may use a Doctrine Collection internally only if that adds value for path entries; otherwise, a typed array is acceptable. 
- **Risk**: High (widely used).

### Step 6: Collaborations
- **Goal**: Migrate `Box\Collaboration`.
- **Scope**: Link Users/Groups to Files/Folders.
- **Risk**: Moderate.

### Step 7: Final Cleanup
- **Goal**: Eliminate `src/Model` and legacy interfaces.
- **Risk**: Low.

## 8. Resolved Design Decisions

The following design decisions have been resolved as follows:

1.  **Generic Resource Interface**: **Do not keep initially**. Use concrete resource classes instead of recreating `ModelInterface` or `IdentifiableInterface`.
2.  **SharedLink**: **Model as DTO/value object**. Shared links are usually nested under files/folders and do not need a dedicated service initially.
3.  **Collection DTOs**: **Use specific response DTOs wrapping Doctrine Collections**. Avoid one generic public collection DTO. Include pagination metadata per response type.
4.  **Metadata**: **Typed DTO envelope with flexible custom values**. Model Box-defined metadata structures while allowing dynamic template values as `array<string, mixed>`.
    - *Note: Namespace skeleton and implementation checklist established.*
5.  **Factory Interfaces**: **Remove per-resource factory interfaces**. They add little user value; hydrator/mapper is the correct construction boundary.
6.  **Raw API Payloads**: **Optional debug capture only**. Disabled by default; prefer a separate debug payload store (e.g., WeakMap-backed) over storing raw arrays on every resource. Raw payloads must not be logged, serialized, or treated as the primary SDK API.
7.  **Doctrine Collections**: **Use selectively**. Useful for list response entries; not required for all arrays, metadata values, or small value-object lists.