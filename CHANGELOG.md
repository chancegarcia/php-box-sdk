# Changelog

## Unreleased

### Summary
- Hardened the service layer architecture to provide more consistent hydration, better error handling, and clearer boundaries between raw API data and typed resources.
- Introduced standardized service patterns for read and write operations, leveraging centralized hydration helpers.
- Refactored the SDK response foundation to use a thin PSR-7-backed `BoxResponse`, improving compliance and providing helpful response utilities.
- Hardened connection error handling with a refined exception taxonomy, ensuring more granular and descriptive errors (e.g., `ApiException`, `TransportException`).
- Implemented automatic redaction of sensitive data (access tokens, refresh tokens, client secrets) in logs, exceptions, and CLI output.
- Updated comprehensive public and migration documentation to reflect the hardened service layer and refined foundation.

### Developer Details
- **Service Layer Hardening**:
    - Introduced `Service::getResourceFromBox()` to fetch and hydrate typed resources.
    - Introduced `Service::sendUpdateAndHydrate()` to centralize payload submission and response hydration.
    - Added `Service::hydrate()` helper to standardize resource creation via the `Hydrator`.
    - Migrated `UserService` and `FileService::createSharedLink()` to representative hardened patterns.
    - Deprecated stateful service properties (`lastResult`, `lastResultDecoded`, `lastResultFlat`) and methods (`getLastResult`, `getDefaultReturnType`).
- **Response and Error Handling**:
    - Replaced Symfony-inherited `BoxResponse` with a PSR-7-backed implementation wrapping `Psr\Http\Message\ResponseInterface`.
    - Introduced `BoxResponseInterface` with helpers like `json()` for safe decoding and `getRetryAfter()` for rate-limit handling.
    - Normalized all transports (Guzzle, Curl) to consistently return `BoxResponseInterface`.
    - Implemented a hierarchical exception model: `BoxException` -> `BoxResponseException` -> `ApiException`.
    - Added specialized `ApiException` subclasses for common HTTP status codes (401, 403, 404, 409, 429).
    - Ensured `ApiException` preserves the original response object for programmatic inspection.
- **Security and Redaction**:
    - Added a `Redactor` utility to mask sensitive tokens and secrets.
    - Integrated redaction into `BoxException` messages and `BoxLoggerTrait` for safe logging.
    - Enabled automatic masking of tokens in CLI command output via `ConsoleOutputFormatter`.
- **Documentation**:
    - Updated `README.md`, `upgrading-0.11-to-1.0.md`, and `programmatic-usage.md` with new architectural details and examples.
    - Standardized on Composer scripts (e.g., `composer review`) as the primary validation commands in all documentation.

### Migration Notes
- **Response Wrapper**: `BoxResponse` no longer inherits from Symfony's `Response`. If your code relied on Symfony-specific response methods, update it to use the new `BoxResponseInterface` or access the underlying PSR-7 response via `getPsrResponse()`. See [Upgrading from v0.11 to v1.0](docs/migration/upgrading-0.11-to-1.0.md) for a detailed guide.
- **Exception Handling**: While `BoxException` remains the base, it is recommended to catch more specific exceptions like `ApiException` to access the response context.
    - *Before*:
      ~~~~php
      try {
          $client->getFile($id);
      } catch (\Box\Exception\BoxException $e) {
          // generic handling
      }
      ~~~~
    - *After*:
      ~~~~php
      try {
          $client->getFile($id);
      } catch (\Box\Exception\ApiException $e) {
          $errorData = $e->getResponse()->json();
          $boxCode = $errorData['code'] ?? 'unknown';
      } catch (\Box\Exception\TransportException $e) {
          // network error
      }
      ~~~~

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
