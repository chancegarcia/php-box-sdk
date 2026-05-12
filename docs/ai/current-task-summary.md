### Summary
- Implemented the functional PDO-backed token storage backend for Step 12.
- The storage layer provides passive, context-aware token persistence conforming to the v1 contract.
- Added comprehensive SQLite-based unit tests for all storage operations.

### Changes
- **src/Storage/Token/Pdo/TokenStorage.php**: Fully implemented `storeToken`, `updateToken`, `retrieveToken`, `removeToken`, and `clear` using parameterized PDO queries.
- **src/Storage/Token/Pdo/TokenStorageInterface.php**: Refined the interface to align with the v1 contract, removing legacy methods and adding proper type hints.
- **src/Service/Service.php**: Updated `refreshConnection` to ensure a default `TokenStorageContext` is used if none is set, preventing crashes during token updates.
- **tests/Storage/Pdo/TokenStorageTest.php**: Added new SQLite-based tests covering isolation, replacement, removal, and nullable context fields.
- **phpstan-baseline.neon**: Cleaned up the baseline for `TokenStorage` after removing deprecated fluent setters.
- **tests/Service/ServiceErrorTest.php**: Updated mocks to account for the new `TokenStorageContext` usage.

### Verification
- Ran `composer test tests/Storage/Pdo/TokenStorageTest.php`: Passed (9 tests, 20 assertions).
- Ran `composer review`: All checks (Lint, CS, PHPStan, PHPUnit) passed (251 tests total).
- Verified one-active-token-per-context behavior via tests.
- Verified isolation between different user/enterprise/client contexts.
- Verified that no token secrets are exposed in exceptions or logs.

### Notes
- The PDO implementation uses a manual `DELETE` then `INSERT` pattern within a transaction to maintain portability across SQL dialects while enforcing the "one active token per context" rule.
- Default `TokenStorageContext` is now automatically instantiated in `Service` if storage is active but no context is provided, ensuring backward compatibility for default use cases.

# Reviewer Notes

## Actionable Follow-ups
- **Database-specific token storage variants**: Do not plan separate MySQL/PostgreSQL/SQLite token storage implementations by default. Keep the portable PDO backend as the v1 implementation and rely on `TokenStorageInterface` for custom backend needs. Revisit only if concrete portability, locking, concurrency, or schema issues emerge from real usage.
