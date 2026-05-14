### Summary
- Added CLI support for JWT token exchange and aligned environment variable names with the new prefix system.
- Simplified CLI options by removing the redundant transport option and consolidating token storage to use PDO exclusively.
- Improved security by adding redaction for JWT-sensitive fields in console output.

### Changes
- **src/Contract/ConfigProviderInterface.php**: Added `getAuthMode()` and JWT-specific getters.
- **src/Service/EnvConfigProvider.php**: Renamed OAuth2 environment variables to `BOX_OAUTH_*` prefix and implemented new JWT getters.
- **src/Service/BoxClientFactory.php**: Implemented `createClientForCurrentMode()` to automatically handle OAuth2 vs JWT client creation.
- **src/Command/AbstractBoxCommand.php**: Removed `--transport` and `--storage-type` options; simplified `applyStorageOption()` to use PDO when storage is enabled.
- **src/Command/JwtTokenCommand.php**: New command `box:jwt:token` for JWT assertions exchange.
- **src/Service/ConsoleOutputFormatter.php**: Added masking for `assertion` and `jwt_assertion`, and full redaction for `private_key` fields.
- **.env.dist / .env**: Updated with new prefixed keys and JWT placeholders.
- **src/ClientConfig.php**: Implemented new interface methods to maintain compatibility.

### Verification
- Ran `composer test`: 279 tests, 739 assertions passed (including new `JwtTokenCommandTest` and updated `EnvConfigProviderTest`).
- Ran `composer analyse`: No errors.
- Ran `composer cs:check`: All files follow PSR-12 and project style rules.
- Verified `.env` file contains no real credentials.

### Notes
- The `--storage-type` option was removed because in-memory storage is not persistent across CLI runs; PDO is now the default and only persistent option for CLI.
- The `--transport` option was removed as it was vestigial for the current CLI surface.
- `ClientConfig` still implements `ConfigProviderInterface` as a temporary bridge; `getOAuth2RefreshToken()` and `getOAuth2AccessToken()` are stubs returning null. Cleanup tracked in Slice 15.4.4.
- `BoxClientFactory::createClient()` does not yet load pre-existing access/refresh tokens from env into a `TokenInterface`. Commands currently handle this at the command level with raw strings. Both gaps tracked in Slice 15.4.4 alongside the ClientConfig architectural cleanup.
