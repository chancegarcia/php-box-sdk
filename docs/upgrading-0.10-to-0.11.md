# Upgrading from 0.10.x to 0.11.0

## Overview
The 0.11.0 release is a **major transitional and modernization update**. It introduces significant breaking changes aimed at improving type safety, performance, and developer experience by leveraging modern PHP features and streamlining the SDK structure.

Key themes of this release:
- **PHP Modernization**: Support for PHP 8.4+.
- **Namespace Simplification**: Flattened namespace structure for easier imports.
- **Fluent API Deprecation**: Transition away from fluent-style setters to standard non-fluent methods.
- **Improved Tooling**: Introduction of a new CLI test harness.
- **Architecture Refinement**: Pluggable HTTP transports and enhanced logging.

## Recommended Upgrade Steps
1. **Verify Environment**: Ensure your server/development environment is running **PHP 8.4** or higher.
2. **Update Composer**: Update your `composer.json` to require version `^0.11`.
3. **Refactor Namespaces**: Global search and replace for the new namespace structure.
4. **Update Model Usage**: Identify and split any chained setter calls on models.
5. **Run Tests**: Execute your test suite to catch any missed namespace or behavioral changes.
6. **Integrate Logging (Optional)**: Configure the new Monolog integration if you need detailed API logs.

## Composer Upgrade
Update your `composer.json` or run:
```bash
composer require chancegarcia/box-api-v2-sdk:^0.11
```

## Deprecations and Behavior Changes

### PHP 8.4 Requirement
Versions of PHP older than 8.4 are no longer supported. The SDK now uses PHP 8.4 property hooks, typed properties, and other modern features.

### Namespace Flattening
The namespace structure has been simplified to provide cleaner top-level `Box\...` access for public SDK resources.

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

The old `Box\Model\...` class paths remain available in 0.11.0 as deprecated aliases, but new code should use the top-level `Box\...` namespaces. These aliases are planned for removal in v1.

You will need to update your `use` statements. For example:

```php
// Before
use Box\Model\Folder\Folder;

// After
use Box\Folder\Folder;
```

### HTTP Transport Layer
The HTTP execution has been decoupled from the client logic. 
- `BoxResponse` has been rewritten to provide better access to headers and status information.
- New `TransportInterface` with `GuzzleTransport` and `CurlTransport` (default) 
implementations.

### Configuration
`EnvConfigProvider` is now available for environment-variable based configuration. Use the new `.env.dist` template as a reference for `BOX_CLIENT_ID` and `BOX_CLIENT_SECRET`.

## Setter Chaining Migration
In 0.11.0, model setter methods are deprecated as fluent-style APIs. Many setters now explicitly return `void`. You should avoid chaining setter calls and instead call them as standalone statements.

**Before:**
```php
$file->setId('123')->setName('document.pdf')->setDescription('Project specs');
```

**After:**
```php
$file->setId('123');
$file->setName('document.pdf');
$file->setDescription('Project specs');
```

## Search Patterns
You can use the following patterns to find code that needs attention:

### Find Fluent Setters
Search for multiple setter calls on the same line:
```bash
grep -r "->set.*->set" src/
```

### Find Old Namespaces
Search for the old `Model\Client` namespace:
```bash
grep -r "Box\\\\Model\\\\Client" .
```

## New CLI Tool
A new CLI tool is available at `bin/box-sdk`. It helps with:
- OAuth2 flows (`auth:url`, `auth:exchange-code`, `auth:refresh-token`)
- Testing API interactions
- File uploads

## Testing After Upgrade
After upgrading, run PHPUnit to ensure everything is working as expected:
```bash
vendor/bin/phpunit
```

## Troubleshooting
- **Fatal Error: Class not found**: Check your namespace imports. `Box\Model\Client\Client` is now `Box\Client`.
- **TypeError on setter calls**: If you were relying on setters returning the object instance, your code will now fail as they return `void`. Split the chains.
- **PHP Version Error**: Ensure you are running PHP 8.4+.

## Compatibility Notes
- **PHP**: >= 8.4
- **Symfony**: ^7.4 or ^8.0
- **Guzzle**: ^7.0
- **Monolog**: ^3.0
