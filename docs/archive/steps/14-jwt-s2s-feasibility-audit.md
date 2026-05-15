# Step 14 Audit — JWT/S2S Feasibility and Dependency Review

## 1. Box JWT/S2S Auth Flow Summary

Box JWT (JSON Web Token) and S2S (Server-to-Server) authentication allow an application to authenticate directly with Box without user interaction (no browser redirect).

### Key Characteristics:
- **Flow**: Application generates a JWT assertion -> Signs it with a private key -> Sends it to Box token endpoint -> Receives an access token.
- **Grant Type**: `urn:ietf:params:oauth:grant-type:jwt-bearer`
- **Signing Algorithm**: `RS256` (RSASSA-PKCS1-v1_5 with SHA-256) using a 4096-bit RSA key.
- **Required Claims**:
    - `iss`: Client ID
    - `sub`: Enterprise ID (for enterprise tokens) or User ID (for app user tokens)
    - `box_sub_type`: `enterprise` or `user`
    - `aud`: `https://api.box.com/oauth2/token`
    - `jti`: A unique identifier for the token (prevent replay attacks)
    - `exp`: Expiration time (typically 60 seconds from generation)
- **Token Endpoint**: `https://api.box.com/oauth2/token`
- **Revocation**: Supported via standard OAuth2 revocation endpoint.
- **Refresh**: JWT tokens themselves don't "refresh" via a refresh token; instead, the application simply generates a new JWT assertion and exchanges it for a new access token when the old one expires.

---

## 2. Crypto Dependency Review

### 2.1 Native PHP `ext-openssl`
- **Capability**: PHP 8.4 provides full support for RSA signing via `openssl_sign()`.
- **Requirement**: `ext-openssl` must be added to `composer.json`.
- **Manual Construction**: Building the JWT assertion manually involves:
    1. Base64Url encoding the header: `{"alg":"RS256","typ":"JWT","kid":"..."}`
    2. Base64Url encoding the payload (claims).
    3. Concatenating them with a dot.
    4. Signing the result with `openssl_sign()`.
    5. Base64Url encoding the signature.
    6. Concatenating all three parts.

### 2.2 Third-Party Libraries
- **Options**: `lcobucci/jwt` or `firebase/php-jwt`.
- **Pros**: Handles edge cases, well-tested, easier API.
- **Cons**: Adds dependencies, may have version conflicts.

### 2.3 Decision: Manual Construction with `ext-openssl`
**Rationale**: 
- The project aims for a lean SDK boundary. 
- JWT assertion for Box is highly specific but technically simple (fixed algorithm `RS256`).
- Native `openssl` functions are efficient and already common in PHP environments.
- Avoiding a heavy dependency like `lcobucci/jwt` keeps the SDK lightweight.
- **Security**: Manual construction of a simple JWT is low-risk if Base64Url encoding and standard `openssl_sign` are used correctly.

---

## 3. Config / DTO Boundary Design

### 3.1 Separate JWT Config DTO
Instead of bloating `ClientConfig`, a dedicated `JwtAuthConfig` or similar should be introduced for JWT-specific fields.

### 3.2 Config Mode Discriminator
`ClientConfig` should ideally remain agnostic, but `BoxClientFactory` needs to know which mode to use. We will introduce an `auth_mode` (or similar) in the config provider.

### 3.3 JWT Configuration Fields:
- `clientId` (Shared)
- `clientSecret` (Shared - optional for JWT but often provided in Box App Config)
- `enterpriseId` (JWT-specific)
- `publicKeyId` (JWT-specific / `kid`)
- `privateKey` (JWT-specific / PEM content)
- `privateKeyPassphrase` (JWT-specific)

---

## 4. AuthProvider Integration Plan

### 4.1 `JwtProvider`
- Will implement `AuthProviderInterface`.
- `buildAuthorizationUrl()`: Will throw `BoxException` (JWT is not for browser flows).
- `exchangeAuthorizationCode()`: Will throw `BoxException`.
- `refreshToken()`: Will perform a new JWT exchange.
- `revokeToken()`: Standard revocation.

### 4.2 `Client` and `BoxClientFactory` Changes
- `Client` currently hard-codes `OAuth2Provider` in some logic or assumes it. It should be fully injected.
- `BoxClientFactory` will be updated to detect JWT config and instantiate `JwtProvider`.

---

## 5. Token Storage Implications

- `TokenStorageContext` already supports `enterpriseId`, which is perfect for JWT service account tokens.
- `TokenInterface` implementations are sufficient.
- **Auto-refresh**: `JwtProvider::refreshToken()` will handle generating a new assertion. The `Client` already calls `refreshToken()` when it detects expiry.

---

## 6. CLI / Config-Provider Plan

### 6.1 New CLI Commands
- `box:jwt:token`: Exchange assertion for an enterprise token.
- `box:jwt:token --user-id=...`: Exchange assertion for an App User token.

### 6.2 `.env` Credential Separation
We will use clear grouping in `.env.dist`:
```
# JWT / S2S Credentials
BOX_JWT_ENTERPRISE_ID=
BOX_JWT_PUBLIC_KEY_ID=
BOX_JWT_PRIVATE_KEY_PATH=
BOX_JWT_PRIVATE_KEY_PASSPHRASE=
```
`BOX_CLIENT_ID` and `BOX_CLIENT_SECRET` will be reused.

### 6.3 Private Key Handling
- `EnvConfigProvider` will read `BOX_JWT_PRIVATE_KEY_PATH`.
- `JwtProvider` (or a helper) will load the file content.
- Key material must never be logged.

---

## 7. Security Redaction Requirements

### Redaction Checklist for Step 15:
- [ ] `privateKey`: FULL REDACTION or "---PRIVATE KEY---" placeholder.
- [ ] `privateKeyPassphrase`: FULL REDACTION.
- [ ] `jwtAssertion`: Masked (like tokens).
- [ ] Ensure `ConsoleOutputFormatter` includes these new fields.

---

## 8. Implementation Slice Plan for Step 15

### 15.1 — Dependency and Core JWT Support
- Add `ext-openssl` to `composer.json`.
- Implement `JwtAuthConfig` DTO.
- Implement `JwtAssertionGenerator` (internal helper for manual JWT construction).

### 15.2 — JwtProvider Implementation
- Implement `JwtProvider` class.
- Implement `JwtProviderInterface` (if needed for type safety).
- Unit tests with mocked OpenSSL/Responses.

### 15.3 — Factory and Client Integration
- Update `BoxClientFactory` to support JWT mode detection.
- Update `Client` to gracefully handle non-OAuth2 providers.
- Integration tests for JWT client construction.

### 15.4 — CLI Support
- Add JWT-specific commands.
- Update `.env.dist`.
- Update `ConsoleOutputFormatter` for JWT field redaction.

---

## 9. Outstanding Questions
- **Passphrase-protected keys**: Supported via `openssl_pkey_get_private`.
- **App User tokens**: The assertion payload changes `sub` and `box_sub_type`. `JwtProvider` should support this via options or context.
- **Auto-refresh**: Should `JwtProvider` cache the token internally or rely on `Client` + `TokenStorage`? (Prefer `Client` + `TokenStorage` for consistency).
