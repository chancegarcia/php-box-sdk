### Summary
- Completed **Authenticated Request Boundary Cleanup (Step 13.4)**, including reviewer follow-up.
- Centralized bearer token application in `Connection`, removing manual auth header management from `Client` and `Service`.
- Removed ambient `additionalConnectionHeaders` state from base `Service` to prevent side-effect-heavy connection mutation.

### Changes
- **Connection**: Updated `request()` to automatically apply the `Authorization: Bearer <token>` header when an access token is available. Added `getAuthorizationHeader()` to `ConnectionInterface`.
- **Client**: Removed manual calls to `setConnectionAuthHeader()` in `query()` and `addCollaboration()`; deprecated legacy auth header helper methods.
- **Service**: 
    - Removed `additionalConnectionHeaders` property and associated `get/set` methods from `Service` and `ServiceInterface`.
    - Cleaned up `getAuthorizedConnection()` to only synchronize the access token, removing all arbitrary header mutation logic.
    - Updated `getConnectionHeaders()` to return only the bearer header, as ambient additional headers are no longer supported at the service level.
- **ConnectionFactory**: Refactored `createAuthorizedConnection()` to leverage `Connection` auth capabilities.
- **Roadmap**: 
    - Documented planned removal of `Client::getAccessToken()` in **AuthProvider Extraction (OAuth2) (Step 13.5)**.
    - Added a **Semantic Naming and Human-Readable API Clarity Review** to **Client Facade and Legacy Surface Review (Step 13.6)**.

### Verification
- Ran `composer review` (lint, test, cs:check, analyse).
- All 259 tests passed (686 assertions).
- Verified that `Authorization` headers are correctly applied by the connection layer.
- Confirmed that `Service` no longer mutates connection headers from ambient state.

### Token Storage Boundary Verification
- Token storage remains passive persistence only.
- Services remain storage-independent and auth-lifecycle-independent.

### Follow-ups
- **AuthProvider Extraction (OAuth2) (Step 13.5)**: Extract OAuth2 lifecycle (exchange, refresh, revoke) into a dedicated `AuthProvider`.
- **Client Facade and Legacy Surface Review (Step 13.6)**: Perform semantic naming review and final facade cleanup.
