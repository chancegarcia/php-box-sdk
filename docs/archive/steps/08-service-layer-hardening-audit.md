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

Legacy architecture removal is a critical v1 requirement. To ensure stability, the execution of the removal is moved to a dedicated tracker/plan.

1. **Slice 2-7 (Implementation)**: Harden services and establish patterns. (Completed ✓)
2. **Slice 8 (Cutover Planning)**: Perform focused audit and partitioning for removal. (Current ✓)
3. **Dedicated Legacy Removal (Step 9)**: Execute removal sub-slices defined in `docs/planning/09-legacy-architecture-removal.md`.

## 7. Service Layer Hardening Tracker Decision

The legacy cutover (actual removal) is too broad for a single slice within the current tracker. It is recommended to treat **Slice 8** as the planning/handoff point and initiate a dedicated **Legacy Architecture Removal** initiative (Step 9).

## 8. Deferred Tracker Candidates

- **Collection Layer Overhaul**: Replacing all `EventCollection` etc. with Doctrine Collections.
- **Full PHPStan Level Increase**: Should be done after major removals to avoid chasing ghosts.
