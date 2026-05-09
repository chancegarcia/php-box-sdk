# Service Layer Hardening Audit - Slice 1

## 1. Service Inventory

| Class | Interface | Domain | Pattern | Return Types | Status |
|---|---|---|---|---|---|
| `Box\Service\Service` | `ServiceInterface` | Base | Legacy/Transitional | `decoded`, `original`, `flat` | High Debt |
| `Box\Service\UserService` | None | User | Transitional (`Hydrator`) | `User` resource | Modernizing |
| `Box\Service\File\FileService` | `FileServiceInterface` | File | Mixed (Legacy/DTO) | `mixed` (via base) | Inconsistent |
| `Box\Service\Event\UserEventService` | `UserEventServiceInterface` | Event | Legacy (`mapBoxToClass`) | `mixed` (via base) | High Debt |
| `Box\Service\BoxClientFactory` | None | Client | Modern | `Client` | Clean |
| `Box\Service\EnvConfigProvider` | None | Config | Modern | `string`, `null` | Clean |
| `Box\Service\ConsoleOutputFormatter`| None | CLI | Modern | `void` | Clean |
| `Box\Service\DefaultJsonFormatter` | None | Format | Modern | `string` | Clean |

### `Service` (Base) Methods Audit

| Method | Role | v1 Status | Notes |
|---|---|---|---|
| `getFromBox` | Read | Transitional | Returns `mixed` based on type. Should be stabilized. |
| `sendUpdateToBox` | Write | Transitional | Depends on `ModelInterface` for response hydration. |
| `queryBox` | Read (Raw) | Transitional | Low-level wrapper. |
| `putIntoBox` | Write (Raw) | Transitional | Low-level wrapper. |
| `handleBoxResponse` | Utility | Transitional | Centralized decoding logic. |
| `handleResponseContent` | Utility | Legacy | Handles 'decoded' vs 'flat' logic. |
| `refreshToken` | Auth | Transitional | Should be in Auth/Connection layer, not Service. |
| `getLastResult` | State | Legacy | Encourages stateful service usage. Remove in v1. |

## 2. Legacy Architecture Removal Inventory

| Candidate | Usage | v1 Replacement | Risk |
|---|---|---|---|
| `Box\Model\BaseModel` | Extended by `Service` | Composition / Clean Base | High |
| `Box\Model\BaseModelTrait` | Used in `BaseModel` | `Hydrator` / `ModelMapper` | High |
| `Box\Model\Model` | Legacy DTO base | `Box\Dto\...` classes | Medium |
| `Box\Model\ModelTrait` | Legacy serialization | `toArray()` / `toBoxArray()` on DTOs | Medium |
| `Box\Model\BoxModel` | Legacy resource base | `Box\Resource\...` classes | Low |
| `mapBoxToClass` | `UserEventService`, `Model` | `Hydrator::hydrate()` | Medium |
| `classArray` / `toBoxArray` | `ModelTrait`, `FileService` | `Hydrator::extract()` or DTO methods | Medium |
| `getFromBox` (type param) | All services | Typed methods / stabilized return | High |

## 3. Hydration/Mapper Boundary Audit

- **Current State**: 
    - `UserService` uses `Hydrator` directly on results of `getFromBox`.
    - `FileService` uses `Hydrator` to normalize input arrays to DTOs, then uses `sendUpdateToBox`.
    - `UserEventService` passes a collection to `mapBoxToClass`.
- **Desired v1 Pattern**: Services should return typed resources/DTOs or collections. Hydration should be internal to the service or delegated to a dedicated Hydrator/Mapper service, but NOT via legacy traits on the resource themselves.
- **Migration candidates**: `UserEventService` is the primary candidate for full hydration migration.

## 4. Representative Service Candidates

- **Read Candidate**: `Box\Service\UserService::getUser` (Low Risk)
    - Already partially modernized.
- **Write Candidate**: `Box\Service\File\FileService::createSharedLink` (Medium Risk)
    - Inconsistent usage of DTOs and legacy models. Good candidate to standardize.
- **Compatibility Candidate**: `Box\Service\Event\UserEventService` (High Risk)
    - Most legacy-heavy. Needs a complete overhaul of its stateful properties and mapping logic.

## 5. Test Coverage Map

| Area | Current Tests | Coverage | Gaps |
|---|---|---|---|
| `Service` Auth | `ServiceAuthTest` | Good | - |
| `Service` Response | `ServiceResponseHandlingTest` | Good | - |
| `UserService` | `UserServiceTest` | Good | - |
| `FileService` | None found | Missing | Need tests before refactor. |
| `UserEventService` | None found | Missing | High risk due to lack of tests. |

## 6. Proposed Cutover Sequence

1. **Slice 2 (Base Stabilization)**: Refine `Service` base methods. Deprecate `getLastResult` and 'flat'/'original' return types where not needed.
2. **Slice 3 (Boundary)**: Standardize how `Hydrator` is used across all 3 service types.
3. **Slice 4 (Read)**: Fully migrate `UserService` to v1 patterns (no base service state).
4. **Slice 5 (Write)**: Standardize `FileService` write patterns.
5. **Slice 6 (Compatibility)**: Overhaul `UserEventService`.
6. **Slice 8 (Removal)**: Final removal of `Box\Model\...` and legacy traits once all services are migrated.

## 7. Deferred Tracker Candidates

- **Collection Layer Overhaul**: Replacing all `EventCollection` etc. with Doctrine Collections.
- **Full PHPStan Level Increase**: Should be done after major removals to avoid chasing ghosts.
