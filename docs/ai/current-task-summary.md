### Summary
- Completed the Slice 12.6 follow-up by centralizing CLI storage configuration within the SDK's config provider abstraction.
- Removed direct `$_ENV` and `$_SERVER` access from command classes to improve maintainability and testability.

### Changes
- Updated `ConfigProviderInterface` to include `getStoragePdoDsn()`, `getStoragePdoUser()`, and `getStoragePdoPassword()`.
- Implemented these new methods in `EnvConfigProvider` to handle environment-based PDO settings.
- Refactored `AbstractBoxCommand` to use `ConfigProviderInterface` for resolving PDO storage settings, ensuring CLI options retain precedence.
- Updated constructors of all child commands (`AuthExchangeCommand`, `AuthRefreshCommand`, `AuthUrlCommand`, `FileUploadCommand`) to inject the required `ConfigProviderInterface` into the base class.
- Enhanced `tests/Command/AuthStorageIntegrationTest.php` with new test cases for PDO configuration resolution and precedence.

### Verification
- Ran `composer test tests/Command` (22 tests, all passing, including new PDO integration tests).
- Ran `composer test tests/ClientStorageIntegrationTest.php tests/Storage` (28 tests, all passing).
- Validated codebase with `composer lint`, `composer cs:check`, and `composer analyse` (all clean).

### Notes
- Services remain completely storage-independent.
- Token storage implementations remain passive persistence layers.
- Slice 12.7 (Final Review) is the next scheduled task.
