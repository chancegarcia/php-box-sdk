# Legacy Architecture Removal Plan

Roadmap reference: v1 Step 9 (Sequenced from Step 8)

## Purpose
This document outlines the sequenced plan for removing legacy pre-v1 / v0.x architecture and APIs from the SDK to achieve the "clean target architecture" of v1.0.

## 1. Legacy Inventory by Category

### Base Model Architecture
- **Files**: `src/Model/BaseModel.php`, `src/Model/BaseModelInterface.php`, `src/Model/BaseModelTrait.php`, `src/Model/Model.php`, `src/Model/ModelInterface.php`, `src/Model/ModelTrait.php`, `src/Model/BoxModel.php`, `src/Model/BoxModelInterface.php`
- **Usages**: Extended/Implemented by nearly all legacy resource models and services.
- **v1 Replacement**: 
    - Resources: `Box\Resource\...` (e.g., `Box\Resource\User`)
    - Services: `Box\Service\Service` (hardened base)
    - DTOs: `Box\Dto\...`
- **Risk**: High (Core foundation)
- **Dependency**: Must migrate all services and resources first.

### Service Base Patterns & Stateful APIs
- **Files**: `src/Service/Service.php`
- **Impacted Methods**: `getLastResult`, `getDefaultReturnType`, `refreshToken` (move to Auth), `refreshTokenIfExpired`
- **Usages**: Consumers checking `getLastResult()` after service calls.
- **v1 Replacement**: Typed return values from service methods; Auth handled by `Connection`.
- **Risk**: Medium
- **Dependency**: Update `Client` and CLI commands.

### Legacy Hydration & Mapping Flows
- **Symbols**: `mapBoxToClass`, `classArray`, `toBoxArray`, `buildQuery` (on models)
- **Files**: `src/Mapper/ModelMapper.php` (legacy methods), `src/Model/ModelTrait.php`
- **v1 Replacement**: `Box\Mapper\Hydrator::hydrate()`, `Hydrator::extract()` (or DTO `toArray()`)
- **Risk**: Medium
- **Dependency**: Migration of `UserEventService` and `Folder::classArray`.

### Custom Collection & Event Layers
- **Files**: `src/Event/Collection/*`, `src/Event/User/*`, `src/Event/Admin/*`
- **v1 Replacement**: Doctrine Collections, `Box\Resource\Event`.
- **Risk**: Medium/High
- **Dependency**: Full overhaul of `UserEventService`.

### Compatibility Aliases
- **Files**: `src/User/User.php` (deprecated), etc.
- **v1 Replacement**: Flattened namespace equivalents (e.g., `Box\Resource\User`).
- **Risk**: Low (Documentation/Migration impact)

## 2. Dependency Graph / Removal Order

1.  **Stage 1: Service Migration (HOTSPOTS)**
    - Migrate `UserEventService` (removes dependency on `mapBoxToClass` and legacy collection).
    - Migrate remaining services in `src/Service/` to hardened patterns.
2.  **Stage 2: Resource/DTO Cutover**
    - Finalize all `Box\Resource\...` classes.
    - Update all services to return `Box\Resource` instead of legacy `Box\Model`.
3.  **Stage 3: Infrastructure Removal**
    - Remove `mapBoxToClass` usage in `Client` and services.
    - Remove `ModelTrait` and `BaseModelTrait`.
    - Remove `BaseModel` and `Model` classes.
    - Remove `Box\Model` namespace entirely.
4.  **Stage 4: Cleanup & Validation**
    - Remove compatibility aliases.
    - Remove PHPStan baseline entries for legacy code.
    - Update all tests to use v1 symbols.

## 3. Removal Sub-slices

### Slice 9.1: UserEventService Overhaul
- **Goal**: Modernize `UserEventService` and remove its reliance on `mapBoxToClass`.
- **Scope**: `UserEventService`, `EventCollection`, `Event`.
- **Validation**: `composer test` with focus on event hydration.
- **Risk**: High.

### Slice 9.2: Legacy Model Interface & Trait Removal
- **Goal**: Remove `ModelInterface`, `ModelTrait`, `BaseModelInterface`, `BaseModelTrait`.
- **Scope**: `src/Model/*.php` (except base classes).
- **Dependency**: All resources must be migrated to `Box\Resource`.

### Slice 9.3: Base Architecture Cutover
- **Goal**: Remove `BaseModel`, `Model`, `BoxModel`.
- **Scope**: `src/Model/` (directory removal).
- **Validation**: `composer review`.

### Slice 9.4: Compatibility Alias & Docs Cleanup
- **Goal**: Remove legacy aliases and update migration guides.
- **Scope**: `src/User/User.php`, `docs/migration/`.

## 4. Validation Requirements
- `composer test`: No regressions in service behavior.
- `composer analyse`: Level 0 pass (at minimum) for all v1 code.
- `composer review`: Full project check.
- **Reflection Check**: Verify `Box\Resource\User` does not inherit from `BaseModel`.
