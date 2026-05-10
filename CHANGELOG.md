# Changelog

## Unreleased

### Summary
- Completed the v1 legacy architecture removal by deleting the `Box\Model` base architecture and all associated legacy shims.
- Modernized the `UserEventService` to return a typed `EventResponse` DTO, improving type safety and immutability for event data.
- Standardized the service layer as stateless by removing legacy result-tracking properties and methods.
- Centralized model hydration and extraction in the `Hydrator` and `ModelMapper` components, removing legacy trait-based mapping.
- Enforced the flattened v1 namespace by removing the `Box\User\User` compatibility alias and stale non-flattened references.
- Updated documentation and migration guides with clear before/after examples for the v1 major-version cutover.

### Developer Details
- **Legacy Architecture Removal**:
    - Deleted `Box\Model\BaseModel`, `Box\Model\Model`, `Box\Model\BoxModel`, and their respective interfaces.
    - Removed `Box\Model\BaseModelTrait` and `Box\Model\ModelTrait`.
    - Removed the `Box\User\User` compatibility alias; consumers should use `Box\Resource\User`.
    - Fully cleared the `src/Model` directory of legacy infrastructure.
- **Event Service Modernization**:
    - `UserEventService::getEvents()` now returns a typed `Box\Dto\Event\EventResponse` DTO.
    - Introduced `Box\Mapper\EventResponseMapper` to decouple service logic from response parsing.
    - `EventResponse` now returns a Doctrine `Collection` of `Event` objects via `getEntries()`, using defensive copies to maintain immutability.
    - Aligned `next_stream_position` handling as a `string` to support large cursor values and set default `stream_position` to `now`.
- **Service Layer Hardening**:
    - Removed stateful properties `lastResultOriginal`, `lastResultDecoded`, and `lastResultFlat`.
    - Removed `getLastResult()`, `getDefaultReturnType()`, and `setDefaultReturnType()` from `ServiceInterface` and `Service`.
    - Standardized internal validation by making `Service::validateReturnType()` protected.
- **Mapping and Hydration**:
    - Removed legacy mapping methods `mapBoxToClass`, `toBoxVar`, `toClassVar`, `isInt`, and `removeEmpty` from mapping infrastructure.
    - Migrated remaining SDK internals to use `Hydrator::hydrate()` and native PHP alternatives.
    - Added `toArray()` to `TokenInterface` and `Token` for clean serialization.

### Breaking Changes
- **Box\Model Removal**: The entire `Box\Model` namespace and base architecture have been removed. All resources and services no longer inherit from these legacy bases.
- **UserEventService Signature**: `getEvents()` return type changed from `array|stdClass|null` to `EventResponse`. The `type` parameter and legacy collection support have been removed.
- **Stateless Services**: Services no longer track the "last result" state. Access response data directly from method return values or `ApiException` context.
- **Alias Removal**: The `Box\User\User` alias is gone. Update imports to `Box\Resource\User`.

### Migration Notes
- **Update Event Handling**: Update code calling `UserEventService::getEvents()` to handle the new `EventResponse` DTO.
    - *Before*:
      ~~~~php
      $events = $service->getEvents();
      $nextPos = $service->getLastResult()['next_stream_position'];
      ~~~~
    - *After*:
      ~~~~php
      $response = $service->getEvents();
      $events = $response->getEntries();
      $nextPos = $response->getNextStreamPosition();
      ~~~~
- **Replace mapBoxToClass**: Use the `Hydrator` for manual model hydration.
    - *Before*: `$model->mapBoxToClass($data);`
    - *After*: `(new Hydrator())->hydrate($model, $data);`
- **Namespace Updates**: Update any remaining references to legacy namespaces or aliases.
    - *Change*: `Box\User\User` -> `Box\Resource\User`
    - *Change*: `Box\Model\Model` -> (Remove usage or use `Box\Resource` equivalents)

## v0.11.3

### Summary
- Hardened the CI workflow with explicit minimal repository permissions, reducing default token scope during automated checks.
- Improved package discoverability in the project README by adding common ecosystem badges for CI status, supported PHP version, Packagist release, license, and downloads.
- Refined changelog authoring guidance to preserve full project-context instructions for maintainers preparing future release notes.

### Developer Details
- **CI security posture**:
    - Added top-level `permissions: contents: read` to `.github/workflows/ci.yml` so the workflow runs with an explicitly restricted GitHub token scope.
- **Documentation and release visibility**:
    - Added README badges for CI status, latest stable version, PHP requirement, license, and total downloads to improve quick release and compatibility checks for consumers.
- **Maintainer workflow**:
    - Updated `docs/prompts/changelog-prompt.md` to keep the project-context section in the expected location and wording, helping keep changelog generation aligned with SDK/library release-note standards.

## v0.11.2

### Summary
- Updated CI workflow behavior to keep the full cross-platform matrix for pull requests while running a leaner default matrix for non-PR runs.
- Reduced workflow complexity by replacing separate matrix and job-condition logic with a single JSON-driven matrix definition.
- Improved release and contribution maintainability by making CI execution rules easier to audit and less error-prone when adjusting OS/PHP coverage.

### Developer Details
- **CI matrix selection logic**:
    - Replaced static `matrix.os`/`matrix.php-version` plus `if` filtering with `include: ${{ fromJSON(...) }}` in `.github/workflows/ci.yml`.
    - Pull requests now explicitly run `ubuntu-latest` and `macos-15` for PHP `8.4` and `8.5`; non-PR runs keep Ubuntu-only checks for the same PHP versions.

## v0.11.1

### Summary
- Added token lifecycle helpers so SDK consumers can check expiration status and remaining token lifetime directly from the client and token objects.
- Added utility methods for common model checks: file extension extraction and folder emptiness detection.
- Improved hydration behavior for typed/private properties, reducing edge-case mapping failures during response-to-model conversion.
- Tightened type declarations across core services and token storage interfaces, improving static-analysis friendliness and integration reliability.
- Completed the short array syntax migration (`[]`) across the codebase, with corresponding coding-standard enforcement.

### Developer Details
- **Authentication and token handling**:
    - Added `TokenInterface::getReceivedAt()` and `TokenInterface::isExpired()` and implemented expiration tracking in `Token` when `expiresIn` is set.
    - Added `Client::isTokenExpired()` and `Client::getRemainingTokenLifetime()` for first-class token lifetime checks.
- **Model API additions**:
    - Added `FileInterface::getExtension()` / `File::getExtension()` for safer extension parsing (including leading-dot filename handling).
    - Added `FolderInterface::isEmpty()` / `Folder::isEmpty()` with support for array and `Countable` item collections.
- **Hydration and typing improvements**:
    - Updated hydrator assignment flow to prefer public setters and use reflection-based property assignment where appropriate.
    - Added and refined parameter/return type hints in connection, service, parser, and token-storage related components.
- **Tooling and maintainability**:
    - Added unit tests covering token expiration logic and the new file/folder helper methods.
    - Updated CI workflow coverage for broader environment checks, including PHP 8.5 and macOS jobs.

## v0.11.0

### Summary
- **Functional Transition Release**: Serves as a bridge between legacy v0.10.x and the upcoming v1.0 architecture.
- **Modernized for PHP 8.4**: Now requires PHP 8.4+ to leverage modern language features.
- **Advanced Model Mapping**: Introduced a recursive `Hydrator` service for complex API response mapping.
- **Namespace Simplification**: Flattened structure for easier imports (e.g., `Box\Client` instead of `Box\Model\Client\Client`).
- **Standardized Collections**: Migrated to Doctrine Collections for improved consistency and power.
- **Comprehensive Documentation Pass**: Consolidated all audit and planning material into long-lived documentation. Introduced [v1.0 Planning](docs/planning/v1/overview.md) to track technical goals and architectural decisions for the next major release.
- **Improved Tooling**: New CLI test harness for managing OAuth2 and API interactions without writing code.

### Developer Details
- **Architecture Refinement**:
    - Decoupled HTTP execution via `TransportInterface`, supporting Guzzle and native Curl.
    - `BoxResponse` now provides direct access to headers and status information.
    - Integrated PSR-3 logging across the SDK via `BoxLoggerTrait`.
    - New `FileStream` abstraction for memory-efficient large file uploads.
- **API & Models**:
    - Primary entry point moved to `Box\Client`.
    - Extensive type audit: standardizing on `string|int` for IDs and `DateTimeInterface` for dates.
    - Introduced `EnvConfigProvider` for environment-variable based configuration.
- **Planned v1.0 Removals**:
    - Deprecated `Box\Collection\ArrayCollection` and its interface.
    - Deprecated transition-layer array support for nested model fields (e.g., `createdBy`).
    - Deprecated legacy namespaces and aliases (e.g., `Box\Model\*`).
    - Stricter typing for IDs (`string`) and Dates (`DateTimeImmutable`) planned for v1.0.

### Breaking Changes
- **PHP Requirement**: PHP >= 8.4 is now strictly required.
- **Non-Fluent Setters**: All model setters now return `void`. Chained calls will now result in errors.
- **Namespace Reorganization**: Many classes have moved. While aliases exist in 0.11.0, they are deprecated.
- **Dependency Update**: Now requires `doctrine/collections: ^2.2`.

### Migration Notes
- **Update PHP**: Ensure your environment is running PHP 8.4 or higher.
- **Namespace Refactor**: Update imports to the flattened structure.
- **Unchain Setters**: Break any chained setter calls into individual statements.
    - *Before*:

      ~~~~php
      $folder->setName('New Name')->setParentId('0');
      ~~~~

    - *After*:

      ~~~~php
      $folder->setName('New Name');
      $folder->setParentId('0');
      ~~~~
- **Update Collections**: If you used `Box\Collection\ArrayCollection`, migrate to `Doctrine\Common\Collections\ArrayCollection`.
- **Configuration**: Use the new `.env.dist` template for environment-based configuration.
- **Comprehensive Guide**: See [Upgrading from 0.10.x to 0.11.0](docs/migration/upgrading-0.10-to-0.11.md) for a detailed checklist.
