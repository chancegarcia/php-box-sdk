# Upgrading from 0.10.x to 0.11.0

## Overview
The 0.11.0 release is a **functional transition release** that bridges the legacy v0.10.x architecture with the future v1.0 design. It introduces significant modernization while maintaining reasonable backward compatibility where practical.

Key themes of this release:
- **PHP Modernization**: Support for PHP 8.4+.
- **Recursive Hydration Layer**: Standalone `Hydrator` for nested API response mapping.
- **Doctrine Collections Integration**: Replaces custom `ArrayCollection`.
- **Typed DTOs**: Improved type safety for complex request/response payloads.
- **Namespace Simplification**: Flattened namespace structure for easier imports.
- **Fluent API Deprecation**: Transition away from fluent-style setters to standard non-fluent methods.
- **Improved Tooling**: Introduction of a new CLI test harness.
- **Architecture Refinement**: Pluggable HTTP transports and enhanced logging.
- **Transition Layer**: Support for both legacy (array) and future (object) shapes for nested Box resources.

## Recommended Upgrade Steps
1. **Verify Environment**: Ensure your server/development environment is running **PHP 8.4** or higher.
2. **Update Composer**: Update your `composer.json` to require version `^0.11` and `doctrine/collections: ^2.2`.
3. **Refactor Namespaces**: Update imports to the new flattened structure (e.g., `Box\Client` instead of `Box\Model\Client\Client`).
4. **Update Collections**: Migrate from `Box\Collection\ArrayCollection` to `Doctrine\Common\Collections\ArrayCollection`.
5. **Update Model Usage**: Identify and split any chained setter calls on models as they now return `void`.
6. **Run Tests**: Execute your test suite (`composer test`) to catch any missed namespace or behavioral changes.
7. **Integrate Logging (Optional)**: Configure a PSR-3 logger if you need detailed API logs.

## Composer Upgrade
Update your `composer.json` or run:
~~~~bash
composer require chancegarcia/box-api-v2-sdk:^0.11 doctrine/collections:^2.2
~~~~

## Transition Layer and Compatibility
v0.11.0 is designed to be a bridge. While many classes have moved and types have been tightened, several features are kept to ease the transition:

- **Nested Field Support**: Many model properties (e.g., `createdBy`, `parent`) now accept both the new resource objects and legacy arrays. Array usage will trigger a deprecation warning in documentation and is planned for removal in v1.0.
- **Legacy Namespaces**: Infrastructure classes like `Box\Model\ModelTrait`, `Box\Model\BoxModelInterface`, and `Box\Model\BaseModel` are preserved to support existing extensions and legacy-facing code.
- **Moved Infrastructure**: `ModelMapper` and `Hydrator` have been moved to `Box\Mapper` from `Box\Model\Mapper`. Since these are new to v0.11, no legacy aliases are provided.

## Deprecations and Behavior Changes

### PHP 8.4 Requirement
Versions of PHP older than 8.4 are no longer supported. The SDK now uses PHP 8.4 features where safe, including improved type safety and property hooks.

### Recursive Hydration
Model hydration is now handled by the standalone `Box\Mapper\Hydrator` class, which uses reflection to recursively hydrate nested objects and collections. `Box\Mapper\ModelMapper::mapBoxToClass()` is now a compatibility facade for this new service.

### Doctrine Collections
The custom `Box\Collection\ArrayCollection` has been deprecated in favor of `Doctrine\Common\Collections\ArrayCollection`. Existing collection classes like `EventCollection` now use Doctrine interfaces.

### Namespace Flattening
The namespace structure has been simplified to provide cleaner top-level `Box\...` access for public SDK resources. 

> **Important**: In v1.0, these namespaces have been further rationalized. Resources are now located in `Box\Resource` (e.g., `Box\Resource\File`) and mirror interfaces have been removed. See the [v1.0 Upgrade Guide](upgrading-0.11-to-1.0.md) for the final v1.0 structure.

| Old Namespace | New Namespace |
| --- | --- |
| `Box\Model\Client\Client` | `Box\Client` |
| `Box\Model\File\File` | `Box\File\File` |
| `Box\Model\Folder\Folder` | `Box\Folder\Folder` |
| `Box\Model\User\User` | `Box\User\User` |
| `Box\Model\Group\Group` | `Box\Group\Group` |
| `Box\Model\Collaboration\Collaboration` | `Box\Collaboration\Collaboration` |
| `Box\Model\Event\Event` | `Box\Event\Event` |
| `Box\Box\...` | `Box\...` |

The old `Box\Model\...` class paths remain available in 0.11.0 as deprecated aliases for most resources, but new code should use the top-level `Box\...` namespaces. These aliases are planned for removal in v1.

### HTTP Transport Layer
The HTTP execution has been decoupled from the client logic. 
- `BoxResponse` has been rewritten to provide better access to headers and status information.
- New `TransportInterface` with `GuzzleTransport` and `CurlTransport` (default) implementations.

### Logging and Error Handling
- **BoxLoggerTrait**: Utility methods for logging and error parsing have been moved to `Box\Trait\BoxLoggerTrait`.
- **PSR-3 Logging**: The SDK now supports any PSR-3 compliant logger.
- **Robust Exceptions:** `BoxException` now frequently includes a `BoxResponseInterface` (via `$e->getBoxResponse()`), allowing programmatic inspection of API error details.

### File Streaming Support
- **FileStream Abstraction:** Introduced `Box\Http\FileStream` for handling file uploads from resources, strings, or specific paths without requiring disk access.
- **Upload Flexibility:** Both `Client::uploadFileToBox()` and `Connection::postFile()` now accept a `FileStream` object in addition to standard file paths.

### API Enhancements
- **Parent ID Support:** `Client::uploadFileToBox()` now accepts a second parameter `$parentId` (defaulting to `0`), matching the underlying `Connection` capability.
- **Auth Aliases:** Added `exchangeAuthorizationCodeForToken()` as a clearer alternative to `getAccessToken()` for the OAuth2 code exchange step.
- **Type Flexibility:** Box IDs are now consistently documented and supported as `string|int` to avoid issues with large numeric-string IDs.

### Configuration
`EnvConfigProvider` is now available for environment-variable based configuration. Use the new `.env.dist` template as a reference for `BOX_CLIENT_ID`, `BOX_CLIENT_SECRET`, and `BOX_ACCESS_TOKEN`.

## Setter Chaining Migration
In 0.11.0, model setter methods are deprecated as fluent-style APIs. Many setters now explicitly return `void`. You should avoid chaining setter calls and instead call them as standalone statements.

**Before:**
~~~~php
$file->setId('123')->setName('document.pdf')->setDescription('Project specs');
~~~~

**After:**
~~~~php
$file->setId('123');
$file->setName('document.pdf');
$file->setDescription('Project specs');
~~~~
## New Features and Improvements

### Client Robustness and Error Handling
- **Robust Exceptions:** `BoxException` now frequently includes a `BoxResponseInterface` (via `$e->getBoxResponse()`), allowing programmatic inspection of API error details.
- **Fail-Fast Validation:** The client now performs more rigorous checks on configuration (like tokens) before attempting remote calls, throwing descriptive `BoxException` errors early.

## Search Patterns
You can use the following patterns to find code that needs attention:

### Find Fluent Setters
Search for multiple setter calls on the same line:
~~~~bash
grep -r "->set.*->set" src/
~~~~

### Find Old Namespaces
Search for the old `Model\Client` namespace:
~~~~bash
grep -r "Box\\\\Model\\\\Client" .
~~~~

## New CLI Tool
A new CLI tool is available at `bin/box-sdk`. It helps with:
- OAuth2 flows (`auth:url`, `auth:exchange-code`, `auth:refresh-token`)
- Testing API interactions
- File uploads

## Testing After Upgrade
After upgrading, run PHPUnit to ensure everything is working as expected:
~~~~bash
composer test
~~~~

## Troubleshooting
- **Fatal Error: Class not found**: Check your namespace imports. `Box\Model\Client\Client` is now `Box\Client`.
- **TypeError on setter calls**: If you were relying on setters returning the object instance, your code will now fail as they return `void`. Split the chains.
- **PHP Version Error**: Ensure you are running PHP 8.4+.

## Compatibility Notes
- **PHP**: >= 8.4
- **Symfony**: ^7.4 or ^8.0
- **Guzzle**: ^7.0
- **Monolog**: ^3.0
