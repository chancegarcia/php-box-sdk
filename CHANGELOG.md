# Changelog

## v0.11.1

### Summary
- **PHP 8.5 Support**: Added initial testing and compatibility for the upcoming PHP 8.5 release while maintaining full support for PHP 8.4.
- **Enhanced Token Lifecycle**: Added native methods to track token expiration and remaining lifetime, simplifying authentication management.
- **Model Utility Helpers**: New methods on `File` and `Folder` models make it easier to extract extensions and check for empty folders.
- **Improved Hydration**: The internal mapping engine now uses reflection for property assignment, better supporting hydrated typed properties.
- **Type Safety & Modernization**: Tightened type hints across the SDK and completed the migration to PHP short array syntax `[]`.

### Developer Details
- **Token Management**:
    - Added `receivedAt` property and `isExpired()` method to `TokenInterface`.
    - `Client` now exposes `isTokenExpired()` and `getRemainingTokenLifetime()` helpers.
- **Model Extensions**:
    - Added `FileInterface::getExtension()` to simplify file path handling.
    - Added `FolderInterface::isEmpty()` to check for the presence of items in a folder.
- **Hydrator Refactor**:
    - Switched to `ReflectionProperty::setValue()` in `Hydrator`, allowing hydration of protected/private properties without requiring public setters.
- **API & Type Cleanup**:
    - Standardized parameter and return types to specific types or `mixed` where previously omitted.
    - Updated `BaseTokenStorageInterface` with more accurate type hints for token context.
    - Enforced PHP short array syntax `[]` across the entire codebase and documentation.
- **CI/CD Improvements**:
    - Updated GitHub Actions workflow matrix to include PHP 8.5 testing on Ubuntu.

## v0.11.0

### Summary
- **Functional Transition Release**: Serves as a bridge between legacy v0.10.x and the upcoming v1.0 architecture.
- **Modernized for PHP 8.4**: Now requires PHP 8.4+ to leverage modern language features.
- **Advanced Model Mapping**: Introduced a recursive `Hydrator` service for complex API response mapping.
- **Namespace Simplification**: Flattened structure for easier imports (e.g., `Box\Client` instead of `Box\Model\Client\Client`).
- **Standardized Collections**: Migrated to Doctrine Collections for improved consistency and power.
- **Comprehensive Documentation Pass**: Consolidated all audit and planning material into long-lived documentation. Introduced [v1.0 Planning](docs/v1-planning.md) to track technical goals and architectural decisions for the next major release.
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
- **Comprehensive Guide**: See [Upgrading from 0.10.x to 0.11.0](docs/upgrading-0.10-to-0.11.md) for a detailed checklist.
