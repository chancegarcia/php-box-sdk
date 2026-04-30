# Changelog

## [v 0.11.0]

### Summary
- **Modernized for PHP 8.4**: Updated to leverage PHP 8.4 features like property hooks and improved type safety, requiring a minimum of PHP 8.4.
- **Simplified SDK Structure**: Flattened namespaces for a more intuitive developer experience (e.g., `Box\Client` instead of `Box\Model\Client\Client`).
- **New CLI Test Harness**: Introduced `bin/box-sdk`, a command-line tool for managing OAuth2 flows and interacting with the Box API without writing code.
- **Flexible HTTP Layer**: Added support for pluggable HTTP transports, including Guzzle and a native Curl implementation.
- **Enhanced Observability**: Integrated Monolog for standard logging across all API interactions.
- **Memory-Efficient Uploads**: Introduced a new file streaming abstraction to handle large uploads from various sources with minimal memory overhead.

### Developer Details
- **Architecture**:
    - Introduced `TransportInterface` to decouple HTTP execution from client logic.
    - Added `GuzzleTransport` and `CurlTransport` (default).
    - `BoxResponse` now provides direct access to headers, status lines, and parsed body content.
- **Client & Models**:
    - Primary entry point moved to `Box\Client`.
    - Model setters now return `void`, deprecating the previous fluent/chained API style.
    - Added `Client::exchangeAuthorizationCodeForToken()` as a descriptive alias for OAuth2 code exchange.
    - Standardized Box IDs as `string|int` for consistency across the SDK.
- **Authentication & Config**:
    - Added `EnvConfigProvider` for easy environment-variable based configuration.
    - CLI tool supports `auth:url`, `auth:exchange-code`, and `auth:refresh-token` commands.
- **Logging**: Centralized logging via `LoggerFactory` and `LoggerAwareInterface`, supporting PSR-3 compliant loggers.

### Breaking Changes
- **PHP Version**: PHP >= 8.4 is now required.
- **Namespaces**: Extensive reorganization of classes. While aliases exist for many classes in 0.11.0, they are deprecated and will be removed in v1.0.
- **Non-Fluent Setters**: All model setters (e.g., `setName()`, `setId()`) no longer return `$this`. Chained calls will now result in errors.
- **Dependency Updates**: Upgraded to Symfony 7/8 components and PSR-3 logging.

### Migration Notes
- **Update PHP**: Ensure your environment is running PHP 8.4 or higher.
- **Namespace Refactor**: Update imports to the new flattened structure. For example, change `use Box\Model\Client\Client` to `use Box\Client`.
- **Unchain Setters**: Break any chained setter calls into individual statements.
    - *Before*: 
       ```php
      $folder->setName('New Name')->setParentId('0');
       ```
    - *After*:
       ```php
      $folder->setName('New Name'); 
      $folder->setParentId('0');
       ``` 
- **Configuration**: Use the new `.env.dist` as a template for environment-based configuration if using `EnvConfigProvider` or the CLI.
- **Detailed Guide**: Refer to [Upgrading from 0.10.x to 0.11.0](docs/upgrading-0.10-to-0.11.md) guide for a comprehensive migration checklist.
