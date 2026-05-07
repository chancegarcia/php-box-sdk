# V1.0 Architecture Rules

These rules define the required structure for the Box PHP SDK V1.0 refactor.

## 1. Namespace and File Structure

- **Box\Resource**: Concrete API resource objects (e.g., `File`, `Folder`, `User`).
- **Box\Dto**: Request, response, and value DTOs (e.g., `FileUploadRequest`, `PaginationParams`).
- **Box\Enum**: PHP 8.1+ Enums for fixed API values (e.g., `BoxItemType`, `CollaborationRole`).
- **Box\Service**: Focused services for API operations (e.g., `FileService`, `UserService`).
- **Box\Http**: Raw HTTP concerns, clients, and response handling.
- **Box\Contract**: True extension contracts (Interfaces) for user-replaceable services.
- **Box\Trait**: Reusable logic traits.
- **Box\Base**: Base classes that provide common functionality but are not god-abstractions.

## 2. Interface Rules

- **Allowed**: For real extension boundaries (HTTP transport, hydration, token storage, factories, etc.).
- **Forbidden**: Mirroring a single concrete resource model's getters/setters/constants.
- **Removal**: All interfaces under `src/Model` or resource namespaces that simply duplicate the model's public API must be removed.

## 3. Resource Model Rules

- **Location**: `Box\Resource`.
- **Logic**: Resources must NOT build URLs, perform HTTP calls, call services, or own hydration logic.
- **Endpoints**: Endpoint URI constants must NOT live in resource models or model interfaces.
- **Types**:
    - IDs must be `string` typed.
    - Dates must use `DateTimeImmutable`.
    - Nested resources must be object-only (no raw arrays).
- **Setters**: Must NOT be fluent. Return `void`.
- **Collections**: Use Doctrine Collections.

## 4. Service Rules

- **Location**: `Box\Service`.
- **Responsibility**: services are the primary way to interact with the Box API.
- **Input**: Use scalar IDs, request DTOs, enums, or value objects.
- **Output**: Return concrete resource classes or DTOs.
- **Facade**: The `Client` class should be a lightweight facade over these focused services.

## 5. DTO Rules

- **Location**: `Box\Dto`.
- **Logic**: Minimal logic, primarily data containers.
- **Setters**: Return `void`. No fluent chains.

## 6. General Rules

- **PHP Version**: PHP 8.4+.
- **Strict Typing**: Mandatory for parameters, return values, and properties.
- **Hydration**: Centralized in `Box\Mapper` or `Box\Http\Hydrator`, not in the models themselves.
