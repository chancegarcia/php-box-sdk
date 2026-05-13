### Summary
- Implemented core JWT/S2S authentication support (Slice 15.1).
- Added `ext-openssl` dependency to `composer.json`.
- Created `JwtAuthConfig` DTO for JWT configuration.
- Created `JwtAssertionGeneratorInterface` and its default implementation `JwtAssertionGenerator` using `ext-openssl`.
- Added unit tests for configuration and assertion generation.

### Changes
- `composer.json`: Added `ext-openssl: *` to `require`.
- `src/Auth/Jwt/JwtAuthConfig.php`: Created DTO with validation.
- `src/Auth/Jwt/JwtAssertionGeneratorInterface.php`: Created interface for JWT generation.
- `src/Auth/Jwt/JwtAssertionGenerator.php`: Implemented default JWT assertion generation using `openssl` functions.
- `tests/Auth/Jwt/JwtAuthConfigTest.php`: Added tests for configuration validation.
- `tests/Auth/Jwt/JwtAssertionGeneratorTest.php`: Added tests for JWT structure and signature verification.

### Verification
- Run `composer test`: All 263 tests passed (including 9 new tests).
- Run `composer analyse`: No errors found.
- Run `composer cs:check`: No style violations found.

### Notes
- Used RS256 algorithm and SHA256 for signing as required by Box API.
- Implemented a custom Base64Url encoder to avoid third-party dependencies.
- Detailed summary written to `docs/ai/current-task-summary.md`.
