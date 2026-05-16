# Changelog

## v1.0.0

### Summary
- Relicensed from MIT to Apache 2.0.
- Introduced JWT/Server-to-Server (S2S) authentication via `JwtProvider` and `JwtAuthConfig`, supporting enterprise and app-user token exchange without OAuth2 redirects.
- Added formal token storage with PDO (`TokenStorage`), Filesystem (`FilesystemTokenStorage`), and in-memory (`TokenStorageContainer`) backends behind a unified `TokenStorageInterface`.
- Added `WebhookVerifier` for HMAC-SHA256 signature verification of incoming Box webhook payloads, with configurable replay-window protection and primary/secondary key rotation support.
- Completed resource namespace rationalization: all resource classes now live under `Box\Resource`; one-class mirror interfaces removed.
- Completed legacy architecture removal: `Box\Model` base classes, traits, and v0.11 shims have been fully removed.
- Hardened the service layer with `BoxClientFactory`, `ClientServiceRegistry`, and the `AuthenticatedServiceInterface` boundary.
- Normalized HTTP transport to Guzzle only; removed `--transport` CLI option.
- Replaced `BoxLoggerTrait` with `BoxApiErrorTrait` (unified error-throw-and-log implementation).
- Modernized all auth providers: `OAuth2Provider` and `JwtProvider` both implement `AuthProviderInterface`.
- Updated CLI storage options: `--storage-type filesystem` (default) or `--storage-type pdo`; added `box:jwt:token` command for JWT flows.

### Developer Details

- **JWT / S2S Authentication**:
    - Added `Box\Auth\Jwt\JwtProvider` implementing `AuthProviderInterface` with `exchangeForEnterpriseToken()` and `exchangeForAppUserToken(string $userId)`.
    - Added `Box\Auth\Jwt\JwtAuthConfig` DTO for JWT credentials; `$privateKey` is always PEM content, never a path.
    - Added `Box\Auth\Jwt\JwtAssertionGenerator` for building signed JWT assertions.
    - `EnvConfigProvider` reads `BOX_JWT_*` variables; `BOX_AUTH_MODE=jwt` switches `BoxClientFactory::createClientForCurrentMode()` to the JWT path.
    - `BoxClientFactory::createJwtClient(JwtAuthConfig $config)` builds a fully wired JWT client.

- **Token Storage**:
    - Added `Box\Storage\Token\TokenStorageInterface` as the common contract.
    - `FilesystemTokenStorage`: persists tokens to a JSON file. Path from `--storage-path` or `BOX_STORAGE_FILE_PATH`.
    - `Pdo\TokenStorage`: persists tokens to a database. DSN/user/pass from `--pdo-dsn`/`--pdo-user`/`--pdo-pass` or `BOX_STORAGE_PDO_*` env vars.
    - `TokenStorageContainer`: in-memory storage; one active token per context; supports lifecycle edge cases (empty store, safe idempotent remove, context isolation).
    - CLI default storage type changed from `memory` to `filesystem`.

- **Webhook Verification**:
    - Added `Box\Webhook\WebhookVerifier` implementing `WebhookVerifierInterface`.
    - Signing formula: `base64(HMAC-SHA256(body + delivery_timestamp, key))`.
    - Enforces a 10-minute replay window. Supports both primary and secondary signing keys for rotation.
    - Webhook CRUD management deferred to post-v1.

- **Service Layer Hardening**:
    - Added `Box\Factory\BoxClientFactory` as the canonical client construction entry point.
    - Added `Box\Service\ClientServiceRegistry` and `ClientServiceRegistryInterface` for typed service access.
    - Added `AuthenticatedServiceInterface`; `Client` throws `RuntimeException` if an authenticated service is accessed without a token.
    - `Service::handleBoxResponse()` replaces legacy `queryBox`/`handleResponseContent`; all services use this unified response path.

- **Legacy Removal**:
    - Removed `Box\Model` base architecture (`BaseModel`, `Model`, `BoxModel`, traits, interfaces).
    - Removed one-class mirror factory interfaces (`FileFactoryInterface`, `FolderFactoryInterface`, `UserFactoryInterface`, `GroupFactoryInterface`, `CollaborationFactoryInterface`).
    - Removed one-class mirror resource interfaces (`FileInterface`, `FolderInterface`, etc.).
    - Removed `queryBox`, `putIntoBox`, `getFromBox`, `sendUpdateToBox`, `handleResponseContent`, `validateReturnType`, `allowedReturnTypes` from `ServiceInterface`/`Service`.
    - Removed `authorizedConnection` (collapsed into `connection`).
    - Removed `BoxLoggerTrait`; replaced by `BoxApiErrorTrait`.
    - Removed `--transport` CLI option; Guzzle is the only transport.

- **Resource and Namespace**:
    - All resources now in `Box\Resource` (`File`, `Folder`, `Group`, `Collaboration`, `Event`, `AdminEvent`, `UserEvent`, `SharedLink`).
    - Endpoint constants moved from resource interfaces to their respective service classes (`FileService::ENDPOINT`, etc.).
    - Resources are passive state objects; hydration is handled exclusively by `Hydrator` and factories.

- **Event Service**:
    - `UserEventService::getEvents()` now returns `Box\Dto\Event\EventResponse` (Doctrine Collection of `Event` objects, `nextStreamPosition` as `string`).
    - Added `EventResponseMapper` for mapping raw API arrays to `EventResponse`.

- **Code Quality**:
    - `BoxApiErrorTrait::error()` return type corrected to `never` (always throws).
    - Yoda conditionals applied consistently across auth and webhook classes.
    - PHPStan level-0 clean; 372 tests, 1002 assertions.

### Breaking Changes
- **Namespace**: All resources moved to `Box\Resource`. Update all imports.
- **Interfaces removed**: Mirror resource interfaces, mirror factory interfaces, and legacy model interfaces are gone. Use concrete classes.
- **Legacy methods removed**: `queryBox`, `putIntoBox`, `getFromBox`, `sendUpdateToBox`, `handleResponseContent`, `mapBoxToClass`, `toBoxArray`, `buildQuery`, `getLastResult()`, `getDefaultReturnType()`, `setDefaultReturnType()`.
- **Legacy classes removed**: `Box\Model\BaseModel`, `Box\Model\Model`, `Box\Model\BoxModel`, and all associated traits.
- **CLI**: `--transport` option removed. `--storage-type memory` removed; use `filesystem` or `pdo`.
- **`UserEventService::getEvents()`**: Now returns `EventResponse` DTO instead of array.
- **`BoxApiErrorTrait::error()`**: Return type is now `never`. Any code checking the return value will break (there is no return value — it always throws).

### Migration Notes
See [Upgrading from 0.11 to 1.0](docs/migration/upgrading-0.11-to-1.0.md) for full migration details.

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
