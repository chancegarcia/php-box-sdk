# Auth Lifecycle/Auth Provider Extraction (Step 13) Audit

## 1. Startup Verification

- **Working tree**: Clean.
- **Latest commit**: `a5d3328a1603bb312482eb7b273104f5dd98840d` (Roadmap reconciliation).
- **Step 12 Status**: COMPLETED. Token storage is passive, context-aware, and decoupled from services.
- **Step 13 Status**: IN PROGRESS.

> **Note on Audit Context**: This section (Section 1) reflects the initial startup snapshot for **Auth Lifecycle/Auth Provider Extraction (Step 13)**. For current slice status, refer to Section 9.

## 2. Step Naming / Documentation Drift Notes

- Standardized all references to `Step Title (Step N)` in this audit and future planning.
- Identified that `docs/planning/v1-release-roadmap.md` needs a minor pass to ensure all step titles match the newest naming convention.
- A dedicated **Roadmap Step Naming and Documentation Drift Cleanup** pass is recommended before Step 13.1 implementation.

## 3. Current Auth Lifecycle Inventory

### 3.1. Authorization URL / Kickoff
- **Current**: `Client::buildAuthQuery()` and `Client::auth()` (which triggers a `header('Location: ...')` redirect).
- **Smell**: `Client` directly handles URL building and browser redirect.
- **Target**: `AuthProvider` builds the URL; `Client` provides a facade; CLI/App handles redirect/output.

### 3.2. Authorization-Code Exchange
- **Current**: `Client::exchangeAuthorizationCodeForToken()` and `Client::getAccessToken()`.
- **Smell**: `Client` orchestrates the HTTP call to `/oauth2/token` and maps the response.
- **Target**: `AuthProvider` performs the exchange and returns a `TokenInterface`.

### 3.3. Refresh-Token Flow
- **Current**: `Client::refreshToken()`.
- **Smell**: `Client` implements the refresh logic and manually updates its own state.
- **Target**: `AuthProvider` performs refresh; `Client` coordinates state update and optional storage.

### 3.4. Revoke / Destroy Flow
- **Current**: `Client::destroyToken()`.
- **Smell**: `Client` calls `/oauth2/revoke`.
- **Target**: `AuthProvider` handles revocation.

## 4. Auth Header / Connection Auth Legacy-Smell Inventory

### 4.1. Header Construction
- **Current**: `Client::getAuthorizationHeader()` returns `"Authorization: Bearer ..."` or `"Authorization: Bearer <token>"`.
- **Current**: `Connection::getAuthorizationHeader()` returns the bearer string.
- **Smell**: Construction is scattered. `Service` also has a `getAuthorizationHeader` method.

### 4.2. Connection Pushing
- **Current**: `Client::setConnectionAuthHeader(ConnectionInterface $connection, ...)` manually pushes headers into curl options: `$connection->setCurlOpts(['CURLOPT_HTTPHEADER' => $headers])`.
- **Smell**: Hard coupling to `setCurlOpts` and `CURLOPT_HTTPHEADER`.
- **Target**: `Connection` should handle its own auth application (e.g., via a `BearerTokenMiddleware` or internal request preparation).

## 5. Curl Transport / Transport Boundary Inventory

### 5.1. Curl-Specific methods in `ConnectionInterface`
- `initCurl()`
- `initCurlOpts()`
- `initAdditionalCurlOpts()`
- `getCurlData()`
- `createCurlFile()`
- `setCurlOpts()`
- `getCurlOpts()`

### 5.2. Transport Selection
- **Default**: `Connection::$transportName` defaults to `self::TRANSPORT_CURL`.
- **File Upload**: `Connection::postFile` has significant curl-specific branching for `CURLFile`.

### 5.3. Removal Impact
- **ext-curl**: Still likely needed as a dependency for Guzzle (usually), but the SDK should no longer use `curl_*` functions directly in its core path.
- **Guzzle Default**: Transitioning to Guzzle as the default/only bundled transport simplifies the connection boundary significantly.

## 6. Client God-Object / Facade Surface Inventory

Remaining `Client` responsibilities:

| Responsibility | Classification | Step 13 Action |
| :--- | :--- | :--- |
| Auth URL Building | Auth Lifecycle | Move to Provider |
| Auth Code Exchange | Auth Lifecycle | Move to Provider |
| Token Refresh | Auth Lifecycle | Move to Provider |
| Token Revocation | Auth Lifecycle | Move to Provider |
| Token Storage Coordination | Auth/Session Facade | **RETAIN** (Composition point) |
| Token State Management | Auth/Session Facade | **RETAIN** (composition root) |
| Service Configuration | Service Delegation | **RETAIN** (Facade) |
| Resource Factory Convenience | Factory Convenience | **RETAIN** (Ergonomics) |
| Response Parsing Helper | Parsing/Hydration | Evaluate for Removal |
| Connection/Transport Setup | Composition root | **RETAIN** |
| Curl Opt mutation | Legacy Shim | **REMOVE** |

## 7. Boundary Analysis

### 7.1. `AuthProvider`
- Owns all `/oauth2/*` interactions.
- Stateless (does not store tokens, only returns them).
- Supports different auth modes (OAuth2, future JWT).

### 7.2. `Client`
- High-level SDK facade.
- Composition root for `AuthProvider`, `Connection`, `TokenStorage`.
- Owns the "current session" (active token).

### 7.3. `Connection`
- Owns request execution.
- Receives a token from `Client` or `AuthProvider`.
- Applies auth headers automatically.
- No longer exposes curl-specific internals.

### 7.4. `TransportInterface` / `GuzzleTransport`
- `GuzzleTransport` becomes the default.
- `TransportInterface` remains as the custom seam.
- `CurlTransport` is removed.

### 7.5. Services
- Receive an authenticated `Connection`.
- No knowledge of storage or auth lifecycle.

## 8. Compatibility and Planned v1 Removal Candidates

- **BREAKING**: `ConnectionInterface` removal of curl methods.
- **BREAKING**: Removal of `CurlTransport` and `Connection::TRANSPORT_CURL`.
- **DEPRECATE/REMOVE**: `Client::getAuthorizationHeader()`, `Client::setConnectionAuthHeader()`.
- **BREAKING**: `Client::auth()` might change signature or behavior (no direct redirect).

## 9. Final Recommended Step 13 Slice Plan

### 13.0 — Auth Lifecycle/Auth Provider Extraction Discovery (COMPLETED)
- Inventory of auth and transport coupling.
- Persisted in `docs/audits/13-auth-lifecycle-provider-extraction-audit.md`.

### 13.1 — Roadmap Step Naming and Documentation Drift Cleanup (COMPLETED)
- Standardize step references to `Step Title (Step N)`.
- Fix drift in roadmap and audits.

### 13.2 — Guzzle Default Transport Cleanup (COMPLETED)
- Default to `TRANSPORT_GUZZLE`.
- Remove curl transport and selection path.
- Update tests to rely on Guzzle.

### 13.3 — Connection Interface Modernization (Curl Removal) (NEXT)
- Remove curl-specific methods from `ConnectionInterface`.
- Flatten `Connection` implementation.
- Update file upload to use Guzzle-native multipart.

### 13.4 — Authenticated Request Boundary Cleanup
- Centralize bearer token application in `Connection`.
- Remove manual header pushing from `Client` and `Service`.

### 13.5 — AuthProvider Extraction (OAuth2)
- Implement `AuthProviderInterface` and `OAuth2Provider`.
- Move URL building, exchange, refresh, and revoke.
- Update `Client` to delegate.

### 13.6 — Client Facade and Legacy Surface Review
- Final audit of `Client` surface.
- Remove remaining auth-header helpers.
- Ensure **v1 Release Readiness (Step 17)** gate is ready.

## 10. v1 Release Readiness (Step 17) Modernization Gate

Add to Step 17 checklist:
- [ ] No `curl_` or `CURLOPT_` usage in core SDK or `Connection`.
- [ ] No `curl`-specific public SDK methods remain in `ConnectionInterface` or `Client`.
- [ ] `AuthProvider` owns all auth lifecycle mechanics (exchange, refresh, revoke).
- [ ] `Client` does not manually build or push auth headers to `Connection`.
- [ ] `ConnectionInterface` is clean of transport-specific methods.
- [ ] All auth-related services are storage-independent and lifecycle-independent.
- [ ] `Client` is a facade/composition root, not a god object.
- [ ] Token storage remains strictly passive.
- [ ] Migration docs cover all planned v1 breaking removals (especially curl-specific paths).
- [ ] `composer review` passes.

## 11. Risks and Open Questions

- **Upload Behavior**: Ensure Guzzle multipart handling is fully compatible with Box's expectations (filename, mime-type).
- **Client compatibility**: Many users might call `exchangeAuthorizationCodeForToken` directly. Keeping it as a facade is critical.

## 12. Validation Strategy

- `composer review` at each slice.
- Specific check on `FileUploadCommand` and `PostFile` behavior.
- Ensure no regression in Token Storage integration from Step 12.
