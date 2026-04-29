# Changelog

## [v 0.11.0]

### Summary
- **Modernized PHP Requirement**: The SDK now requires PHP 8.4 or higher, leveraging the latest language features for better performance and type safety.
- **Streamlined Namespace Structure**: Simplified the project structure and namespaces (e.g., `Box\Client` instead of `Box\Model\Client\Client`), making the SDK more intuitive to use.
- **New CLI Test Harness**: Introduced a powerful command-line tool (`bin/box-sdk`) for testing API interactions, managing authentication, and uploading files without writing code.
- **Enhanced Logging and Observability**: Integrated Monolog support, allowing developers to easily plug in and configure detailed logging for all API interactions.
- **Pluggable HTTP Transports**: Added support for both Guzzle and native Curl transports, giving developers more control over how HTTP requests are handled.

### Developer Details
- **Namespace Flattening**: The `Box\` namespace now maps directly to `src/`. This structural change affects all class imports.
- **Authentication Improvements**: Added `auth:url`, `auth:exchange-code`, and `auth:refresh-token` commands to the new CLI tool to facilitate OAuth2 flows.
- **HTTP Layer**:
    - Introduced `TransportInterface` to decouple HTTP execution from client logic.
    - Added `GuzzleTransport` and `CurlTransport` implementations.
    - `BoxResponse` has been rewritten to provide better access to headers and status information.
- **Configuration**:
    - Introduced `EnvConfigProvider` for environment-variable based configuration.
    - Added a central `config/monolog.php` for logging configuration.
- **Service Layer**: Added `BoxClientFactory` and various service-level interfaces to decouple transport from business logic.

### Breaking Changes
- **PHP 8.4 Required**: Versions of PHP older than 8.4 are no longer supported.
- **Class Renames/Moves**: Almost every class has been moved or renamed due to the namespace reorganization. The primary entry point is now `Box\Client`.
- **Dependency Updates**: Updated major versions of Symfony components (v7/v8) and PSR log (v3).

### Migration Notes
- **Update PHP Version**: Ensure your environment is running PHP 8.4+.
- **Update Namespaces**: Update all imports from `Box\Model\Client\Client` to `Box\Client`. Other models and services have similarly moved from `Box\Box\...` to `Box\...`.
- **Update Autoloading**: If you have custom autoloading, reflect that `Box\` classes are now located directly under `src/`.
- **Configuration**: Use the new `.env.dist` template to configure `BOX_CLIENT_ID` and `BOX_CLIENT_SECRET` for use with the CLI or `EnvConfigProvider`.
