# Changelog

## v0.11.0

### Summary
- **Functional Transition Release**: Serves as a bridge between legacy v0.10.x and the upcoming v1.0 architecture.
- **Modernized for PHP 8.4**: Now requires PHP 8.4+ to leverage modern language features.
- **Advanced Model Mapping**: Introduced a recursive `Hydrator` service for complex API response mapping.
- **Namespace Simplification**: Flattened structure for easier imports (e.g., `Box\Client` instead of `Box\Model\Client\Client`).
- **Standardized Collections**: Migrated to Doctrine Collections for improved consistency and power.
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
