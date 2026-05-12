### Summary
- Finalized AuthProvider extraction for OAuth2 by removing reflection-based mutation and cleaning up the `Connection` credential surface.
- Refined the auth boundary interfaces to support future JWT/S2S authentication.
- Normalized `ClientConfig` internal state to improve type safety and remove legacy getter-time normalization.

### Changes
- Refined `AuthProviderInterface` to be strategy-neutral by moving OAuth2-specific methods to `OAuth2ProviderInterface`.
- Replaced reflection-based `AuthProvider` updates in `Client` with explicit interface-based configuration checks (`instanceof OAuth2ProviderInterface`).
- Updated `ClientConfig` to store `clientId` and `clientSecret` as non-nullable strings (default `''`), normalizing `null` on write.
- Updated `BoxClientFactory` to map `ConfigProviderInterface` to `ClientConfig`, resolving a type mismatch and narrowing the `Client` configuration boundary.
- Updated `OAuth2Provider` to implement `OAuth2ProviderInterface`.
- Removed OAuth2-specific credentials (`clientId`, `clientSecret`, `redirectUri`) from `ConnectionInterface` and `Connection` in previous pass (Step 13.5 follow-up).
- Documented follow-ups for **Client Facade and Legacy Surface Review (Step 13.6)** and **v1 Release Readiness (Step 17)** regarding semantic masking and service connection state.

### Verification
- Full validation via `composer review` (linting, 262 tests passed, PSR-12 formatting, and PHPStan analysis).
- Added `Box\Tests\Service\BoxClientFactoryTest` to ensure correct configuration mapping and type safety.
- Added `Box\Tests\Auth\OAuth2ProviderTest` (previous pass) and updated `ClientTest` to ensure clean configuration propagation.
- Verified that existing integration tests still pass with the new boundary.

### Notes
- A detailed summary was written to `docs/ai/current-task-summary.md`.
- **Client Facade and Legacy Surface Review (Step 13.6)** is next.
- Step 13.6 will audit `Connection::getAuthorizationHeader()`, service connection state, and perform a broad semantic naming review.
- Broad getter-time normalization cleanup is deferred to the Step 17 modernization gate.
