### Summary
- Implemented `JwtProvider` and `JwtProviderInterface` for JWT-based authentication (Slice 15.2).
- Centralized common OAuth2/JWT endpoint constants in `AuthProviderInterface`.

### Changes
- `src/Auth/AuthProviderInterface.php`: Added `TOKEN_URI` and `REVOKE_URI` constants.
- `src/Auth/OAuth2Provider.php`: Removed redundant `TOKEN_URI` and `REVOKE_URI` constants.
- `src/Auth/Jwt/JwtProviderInterface.php`: Created interface extending `AuthProviderInterface` with enterprise and app user exchange methods.
- `src/Auth/Jwt/JwtProvider.php`: Implemented JWT authentication logic, including state management for re-assertion (refresh).
- `tests/Auth/Jwt/JwtProviderTest.php`: Added comprehensive unit tests for `JwtProvider`.

### Verification
- Run `composer test`: 272 tests passed (including 9 new tests for `JwtProvider`).
- Run `composer analyse`: No errors.
- Run `composer cs:check`: No style violations.

### Notes
- JWT "refresh" is implemented as a full re-assertion using the last used subject ID and type.
- Constants are resolved via `self::` or class names, maintaining backward compatibility for `OAuth2Provider` constants through interface inheritance.
- Detailed summary written to `docs/ai/current-task-summary.md`.
