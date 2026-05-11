# v1 Service Coverage and Auth Boundary Audit

## Executive Summary
This audit evaluates the progress of the `v0.11` to `v1.0` architectural transition, focusing on service coverage, remaining legacy code patterns, and the authentication boundary in `Client::configureService()`. While core resources (File, Folder, Collaboration, Group) have moved to the service-based pattern, significant "God object" behavior remains in `Client`, and the authentication boundary is currently porous and potentially confusing.

## Scope
- `src/Resource/` and `src/Service/` namespaces
- `Box\Client` delegation progress
- `Client::configureService()` exception handling
- Legacy mapping and hydration patterns

## Service and Resource Coverage

| Domain | Resource | Service | Client Facade | Tests | Modern Pattern Status | Notes |
|---|---|---|---|---|---|---|
| Folders | Folder | FolderService | Yes (Partial) | Yes | **Full** | `Client` still has many legacy folder methods. |
| Files | File | FileService | Yes (Partial) | Yes | **Full** | `Client::uploadFileToBox` still exists. |
| Collaborations | Collaboration | CollaborationService | No | Yes | **Full** | Service exists but not exposed on `Client`. |
| Groups | Group | GroupService | No | Yes | **Full** | Service exists but not exposed on `Client`. |
| Users | User | UserService | No | Yes | **Partial** | Service is thin; uses legacy `getResourceFromBox`. |
| Events (User) | Event | UserEventService | No | Yes | **Legacy** | Uses custom `EventResponseMapper` and `Dto`. |
| Events (Admin) | AdminEvent | No | No | Yes | **Legacy** | Still uses `mapBoxToClass`. |
| Shared Links | SharedLink | No | Yes | Yes | **Legacy** | Handled inside `Client` or as child of File/Folder. |

## Box API Coverage Gaps
The following major Box API areas lack dedicated Resources or Services:
- **Collections** (Missing)
- **Comments & Tasks** (Missing)
- **Metadata** (Missing)
- **Webhooks** (Missing)
- **Sign Requests** (Missing)
- **File Versions** (Missing)

## Legacy Smell Inventory

| Finding | Location | Severity | v1 Recommendation | Notes |
|---|---|---|---|---|
| `mapBoxToClass` | `AdminEvent`, `Event` | **Blocking for v1** | Remove; use `Hydrator` via `Factory`. | Pre-v1 artifact. |
| `getResourceFromBox` | `Service` base | **Should fix** | Keep but ensure it uses `Hydrator`. | Transition helper. |
| `Client` query methods | `Client::query`, `Client::search` | **Should fix** | Move to dedicated Search/General service. | `Client` should be thin. |
| Mixed types | Multiple Resources | **Acceptable** | Narrow to specific types where possible. | Ongoing task. |
| Fluent Setters | Legacy models | **Should fix** | Change to `void` returns. | Enforced in new resources. |
| Hard-coded URIs | `Client`, `UserService` | **Should fix** | Move to Service constants. | |
| Nested Ternaries | Various | **Should fix** | Replace with explicit branching. | Guideline preference. |

## Authenticated Service Boundary

### Current Behavior
`Client::configureService()` attempts to set the token on every service. If `Client::getToken()` throws a `RuntimeException` (token not set), it silently catches it:

```php
try {
    $service->setToken($this->getToken());
} catch (\RuntimeException $e) {
    // Token not set on client, skip setting it on service
}
```

### Services Requiring Authentication
Most Box API operations require an `Authorization: Bearer <token>` header. These include:
- `FolderService` (all methods)
- `FileService` (all methods)
- `UserService` (all methods)
- `CollaborationService` (all methods)
- `GroupService` (all methods)
- `UserEventService` (all methods)

### Services Not Requiring Authentication / Special Cases
- **OAuth2 Token Exchange**: The initial exchange of an auth code for a token (handled by `Client::exchangeAuthorizationCodeForToken` or a future `AuthService`).
- **Token Refresh**: Uses a refresh token, not an access token.
- **Revoke Token**: Needs client credentials, but may not need the token being revoked as a Bearer header.

### AuthenticatedServiceInterface Recommendation
It is recommended to introduce an `AuthenticatedServiceInterface` (marker interface).
1. `Client::configureService()` should check if the service implements this interface.
2. If it does, and no token is available, it should **throw** an exception rather than silently skipping.
3. This prevents "late-stage" failures where the Service tries to make a request and fails with a confusing 401 or null pointer because the connection wasn't properly authorized.

### Connection vs Authorized Connection Notes
The current `Service` base has both `setConnection` and `setAuthorizedConnection`. In most modern services, these are set to the same object.
- `connection`: The base transport.
- `authorizedConnection`: The transport with Authorization headers injected.
This split seems like a legacy holdover from when tokens were managed differently. For v1, we should aim to unify these or clearly define the separate use cases.

## Recommended Tracker Changes
- **Add Step 11.6.2**: Implement `AuthenticatedServiceInterface` and harden `Client::configureService()`.
- **Add Step 11.6.3**: Remove `mapBoxToClass` from all resources and ensure `Hydrator` coverage.
- **Add Step 11.6.4**: Modernize `UserService` and `UserEventService` to match `FolderService` patterns.

## Proposed Next Slices
1. **Slice 11.6.2**: Auth Boundary Hardening (marker interface).
2. **Slice 11.7**: Client Service Delegation (remaining existing services: File, Collaboration, Group, User).
3. **Slice 11.8**: Legacy Smell Cleanup (Removing `mapBoxToClass`).

## Validation
- This audit was performed by inspecting `src/` and `docs/`.
- No code changes were made in this slice.

## Conclusion
The SDK is approximately 60% through the architectural transition for its currently implemented features. However, the "tail" of legacy behavior in `Client` and older Resources is significant. **Step 11.7 (Client Service Delegation) should proceed**, but it should be accompanied by the recommended auth hardening and legacy cleanup to ensure v1 is truly "clean".
