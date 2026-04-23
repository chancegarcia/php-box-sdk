# Changelog

## [v 0.11.0]

### Summary
- **Modernized Dependency Stack:** Updated requirements to PHP 8.4+ and transitioned to Symfony 7.4/8.0 components.
- **New Command-Line Tool:** Introduced `bin/box-sdk`, a Symfony-based CLI for interactive API exploration and manual testing.
- **Improved Logging:** Integrated a robust logging system based on PSR-3 and Monolog, with automatic propagation to internal models.
- **Refactored Architecture:** Streamlined the internal directory structure and namespace mapping for better PSR-4 compliance and library integration.
- **Enhanced Documentation:** Added comprehensive guides for programmatic usage and CLI operations.

### Developer Details
- **Namespace Realignment:** The `Box\` namespace now maps directly to `src/` (previously `src/Box/`). This is a structural change affecting all class imports.
- **CLI Harness:** New commands for OAuth2 workflows (`auth:url`, `auth:exchange-code`, `auth:refresh-token`) and file operations (`file:upload`).
- **Logging Integration:**
    - Added `LoggerFactory` and `ConfigNormalizer` for centralized log management.
    - SDK-wide support for `LoggerAwareInterface`.
    - Internal models now automatically inherit loggers from the main `Client`.
- **API Client:** Introduced `Box\Client` as a high-level facade for interacting with Box API services.
- **Environment Configuration:** Support for `.env` files and `EnvConfigProvider` to manage credentials and settings for the CLI harness.
- **Service Layer:** Added `BoxClientFactory`, `BoxResponseParser`, and various service-level interfaces to decouple transport from business logic.

### Breaking Changes
- **PHP Version:** Minimum required version is now **8.4**. Integrations running on older PHP versions will fail to install.
- **Namespace & Pathing:** The directory shift from `src/Box/` to `src/` requires updating all `use` statements or autoloader configurations.
- **Dependency Upgrades:** Significant bumps to `psr/log` (v3) and `symfony/*` (v7.4/v8) may conflict with host applications using older versions of these libraries.

### Migration Notes
- **Update Autoloading:** Ensure your `composer.json` or custom autoloader reflects that `Box\` classes are now located directly under `src/`.
- **Configuration:** Use the new `.env.dist` template to configure `BOX_CLIENT_ID` and `BOX_CLIENT_SECRET` for use with the CLI or `EnvConfigProvider`.
- **Logger Injection:** If you use a custom logger, inject it directly into the `Client` via `setLogger()`. It will be automatically propagated to internal components.
- **Dependency Management:** Review your project's dependency tree for potential conflicts with Symfony 7.4/8.0 or PSR-3 v3.
