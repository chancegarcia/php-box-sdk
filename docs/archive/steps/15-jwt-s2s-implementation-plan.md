## Step 14 Process Follow-Up + JWT/S2S Implementation (Step 15)

You are working on the `chancegarcia/box-api-v2-sdk` (PHP 8.4+) repository on branch `release-v1.0.0`.

This prompt covers two sequential phases. Complete Phase A and commit it before beginning Phase B. Do not begin Phase B without completing Phase A.

---

### Process Rules

- Use `Step Title (Step N)` naming in all docs and references.
- Reconcile roadmap, audit, task-summary, and handoff before closing each phase.
- Treat pre-v1 tests skeptically — classify tests before modifying (behavior contract, characterization, legacy shim, implementation-coupled, or stale).
- All validation via Composer scripts: `composer test`, `composer analyse`, `composer cs:check`, `composer lint`.
- No real credentials, PEM private keys, passphrases, or enterprise IDs in any test, fixture, or committed file.
- A step or slice is not approved to begin until the human reviewer explicitly confirms. AI assistants must not begin the next step automatically based on completion docs alone.

---

### Resolved Design Decisions

These decisions were made during human review and must not be re-opened during implementation. If an implementation obstacle requires revisiting one, stop and document the blocker rather than substituting a different approach.

- `Client::getState/setState/getRedirectUri/setRedirectUri` — remove in Slice 15.3. Vestigial after OAuth2Provider extraction. Verify no callers outside CLI or tests before removing.
- App User tokens are the primary JWT v1 use case. Enterprise tokens are explicit opt-in via `--enterprise` CLI flag.
- `JwtAssertionGeneratorInterface` is an explicit extension point. `JwtProvider` accepts the interface. The default implementation uses `ext-openssl` only.
- OAuth2 env vars are renamed to the `BOX_OAUTH_*` prefix. JWT env vars use `BOX_JWT_*`. Auth mode is selected via `BOX_AUTH_MODE`. This is a breaking change requiring a migration note.
- `JwtProvider::refreshToken()` re-asserts using stored `$lastSubjectId`/`$lastSubjectType`. It must never call `$token->getRefreshToken()`.
- `Client::refreshToken()` must be audited for any guard on non-null refresh token. If such a guard exists, add an `instanceof JwtProviderInterface` bypass.
- CLI groupings: `box:auth:*` = OAuth2 only; `box:jwt:token` = JWT only; `box:resource:*` = factory-determined by `BOX_AUTH_MODE`.

---

## Phase A — Step 14 Process Follow-Up

Complete all Phase A tasks, then commit. Do not begin Phase B until Phase A is committed.

### A.0 Startup Verification

1. Confirm the working tree has only Step 14 uncommitted documentation changes (no source changes).
2. Confirm the latest source commit is the Step 13 completion commit.
3. Confirm `docs/audits/14-jwt-s2s-feasibility-audit.md` exists.
4. Run `composer review` — confirm all checks pass.

### A.1 Fix `docs/ai/current-task-summary.md`

The task summary was not correctly updated at Step 14 close. Update it to accurately reflect Step 14 outcomes:

- Step 13 escape fix: `AuthUrlCommand::buildAuthQuery()` replaced with `buildAuthorizationUrl()`.
- Feasibility decisions: native `ext-openssl`, `JwtAuthConfig` DTO, `JwtProvider`, CLI plan.
- Validation: 254 tests, PHPStan Level 0, PSR-12 all passed.
- Next step status: **Pending Approval** — not automatically approved.

### A.2 Add Step Transition Approval Rule to `docs/prompts/ai-workflow/single-repository-workflow.md`

Add an explicit rule: completing a step and updating documentation does not constitute approval to begin the next step. Human reviewer must explicitly confirm before any next step begins. AI assistants must surface this requirement at each step close.

### A.3 Update `docs/prompts/ai-workflow/handoff-summary-template.md`

Add a `Next Step Status` field with allowed values: `Pending Approval | Approved | Blocked`. Set it to `Pending Approval` when any step closes. Only update to `Approved` after explicit reviewer confirmation.

### A.4 Update `docs/ai/current-handoff-summary.md`

Add the `Next Step Status` field. Set it to `Approved` (reviewer has now confirmed). Document the reviewer concerns that were raised and resolved during Step 14 review.

### A.5 Commit Phase A

Commit all Phase A documentation changes with a message clearly scoped to Step 14 documentation correction and process guardrail additions.

---

## Phase B — JWT/S2S Implementation (Step 15)

### B.0 Startup Verification

1. Confirm Phase A is committed.
2. Confirm `Next Step Status: Approved` in `docs/ai/current-handoff-summary.md`.
3. Confirm Step 15 is `Not Started` in `docs/planning/v1-release-roadmap.md`.
4. Run `composer review` — confirm baseline passes.

---

## Slice 15.1 — Dependency and Core JWT Support

### composer.json

Add `"ext-openssl": "*"` to the `require` section.

### `src/Auth/Jwt/JwtAuthConfig.php`

Readonly-style DTO with a typed constructor. Fields:

- `string $clientId` — JWT app client ID (from `BOX_JWT_CLIENT_ID`)
- `string $clientSecret` — JWT app client secret (from `BOX_JWT_CLIENT_SECRET`)
- `string $enterpriseId`
- `string $publicKeyId` — the `kid` claim value
- `string $privateKey` — PEM content loaded from a file (never a file path)
- `?string $privateKeyPassphrase`
- `string $jwtAlgorithm = 'RS256'`

Validate in constructor: throw `BoxException` if `clientId`, `enterpriseId`, `publicKeyId`, or `privateKey` are empty strings. Provide no `toArray()` or serialization method that exposes `privateKey` or `privateKeyPassphrase`.

### `src/Auth/Jwt/JwtAssertionGeneratorInterface.php`

Single-method interface — a user-replaceable extension point:

    interface JwtAssertionGeneratorInterface
    {
        public function generate(JwtAuthConfig $config, string $subjectId, string $subjectType): string;
    }

### `src/Auth/Jwt/JwtAssertionGenerator.php`

Default implementation of `JwtAssertionGeneratorInterface`. Uses `ext-openssl` only.

Implementation steps:

1. Validate `$subjectType` is `'enterprise'` or `'user'` — throw `BoxException` otherwise.
2. Build JWT header JSON: `{"alg":"RS256","typ":"JWT","kid":"<publicKeyId>"}`.
3. Build JWT payload JSON with claims: `iss` (clientId), `sub` (subjectId), `box_sub_type` (subjectType), `aud` (`https://api.box.com/oauth2/token`), `jti` (`bin2hex(random_bytes(16))`), `exp` (`time() + 60`).
4. Base64Url-encode each part (private static helper: replace `+` with `-`, `/` with `_`, strip `=` padding).
5. Load private key via `openssl_pkey_get_private($config->privateKey, $config->privateKeyPassphrase ?? '')` — throw `BoxException` on failure.
6. Sign `"<encoded-header>.<encoded-payload>"` with `openssl_sign()` using `OPENSSL_ALGO_SHA256`.
7. Return `"<encoded-header>.<encoded-payload>.<base64url-encoded-signature>"`.

### Tests

`tests/Auth/Jwt/JwtAuthConfigTest.php`:
- Empty `clientId`, `enterpriseId`, `publicKeyId`, or `privateKey` each throw `BoxException`.
- A valid config constructs without error.

`tests/Auth/Jwt/JwtAssertionGeneratorTest.php`:
- Generate a test key pair inline: `openssl_pkey_new(['private_key_bits'=>2048,'private_key_type'=>OPENSSL_KEYTYPE_RSA])` — 2048-bit is acceptable for tests only.
- Assert the returned string has exactly three dot-separated segments.
- Assert the decoded header contains the expected `alg`, `typ`, and `kid`.
- Assert the decoded payload contains all required claims.
- Assert the signature verifies with `openssl_verify()` using the corresponding public key.
- An invalid subject type throws `BoxException`.
- A bad private key throws `BoxException`.

### Validation After Slice 15.1

`composer test && composer analyse && composer cs:check`

---

## Slice 15.2 — JwtProvider Implementation

### `src/Auth/Jwt/JwtProviderInterface.php`

Extends `AuthProviderInterface` and adds:

    public function exchangeForEnterpriseToken(): TokenInterface;
    public function exchangeForAppUserToken(string $userId): TokenInterface;

### `src/Auth/Jwt/JwtProvider.php`

Implements `JwtProviderInterface`.

Constructor parameters:
- `ConnectionInterface $connection`
- `TokenFactoryInterface $tokenFactory`
- `JwtAuthConfig $config`
- `JwtAssertionGeneratorInterface $assertionGenerator`

Internal state: `?string $lastSubjectId = null` and `?string $lastSubjectType = null`.

Method implementations:

`buildAuthorizationUrl()`: throws `BoxException('JWT authentication does not support browser-based authorization flows.')`.

`exchangeAuthorizationCode()`: throws `BoxException('JWT authentication does not use authorization codes.')`.

`refreshToken(TokenInterface $token, array $options = [])`: calls `exchangeAssertion()` using `$this->lastSubjectId ?? $this->config->enterpriseId` and `$this->lastSubjectType ?? 'enterprise'`. Must NOT call `$token->getRefreshToken()`.

`revokeToken(TokenInterface $token)`: POST to `OAuth2Provider::REVOKE_URI` with `client_id`, `client_secret`, `token` (access token value). Reuse the constant — do not duplicate the URI string.

`exchangeForEnterpriseToken()`: calls `exchangeAssertion($this->config->enterpriseId, 'enterprise')`, stores last state, returns token.

`exchangeForAppUserToken(string $userId)`: calls `exchangeAssertion($userId, 'user')`, stores last state, returns token.

Private `exchangeAssertion(string $subjectId, string $subjectType): TokenInterface`:
- Generates JWT via `$this->assertionGenerator->generate($this->config, $subjectId, $subjectType)`.
- POSTs to `OAuth2Provider::TOKEN_URI` with `grant_type` = `urn:ietf:params:oauth:grant-type:jwt-bearer`, `assertion`, `client_id`, `client_secret`.
- Validates response is an array — throw `BoxException` if not.
- Returns `$this->tokenFactory->createToken($data)`.

### Tests

`tests/Auth/Jwt/JwtProviderTest.php`:
- Mock `ConnectionInterface` — assert POST params include correct `grant_type` and `assertion` key.
- `buildAuthorizationUrl()` throws `BoxException`.
- `exchangeAuthorizationCode()` throws `BoxException`.
- `exchangeForEnterpriseToken()` generates assertion with `box_sub_type` = `enterprise`.
- `exchangeForAppUserToken('user-abc')` generates assertion with `box_sub_type` = `user`.
- `refreshToken()` after enterprise exchange re-uses enterprise subject state.
- `refreshToken()` after app user exchange re-uses app user subject state.
- `refreshToken()` with no prior exchange defaults to enterprise state.
- `revokeToken()` POSTs to revoke URI with access token value.

### Validation After Slice 15.2

`composer test && composer analyse && composer cs:check`

---

## Slice 15.3 — Factory and Client Integration

### `src/Auth/Jwt/JwtConfigProviderInterface.php`

    interface JwtConfigProviderInterface
    {
        public function getAuthMode(): ?string;
        public function getJwtClientId(): string;
        public function getJwtClientSecret(): string;
        public function getJwtEnterpriseId(): string;
        public function getJwtPublicKeyId(): string;
        public function getJwtPrivateKeyPath(): string;
        public function getJwtPrivateKeyPassphrase(): ?string;
    }

### Environment Variable Rename — `BOX_OAUTH_*` prefix (breaking change)

| Old variable | New variable |
|---|---|
| `BOX_CLIENT_ID` | `BOX_OAUTH_CLIENT_ID` |
| `BOX_CLIENT_SECRET` | `BOX_OAUTH_CLIENT_SECRET` |
| `BOX_REDIRECT_URI` | `BOX_OAUTH_REDIRECT_URI` |
| `BOX_STATE` | `BOX_OAUTH_STATE` |
| `BOX_AUTH_CODE` | `BOX_OAUTH_AUTH_CODE` |
| `BOX_ACCESS_TOKEN` | `BOX_OAUTH_ACCESS_TOKEN` |
| `BOX_REFRESH_TOKEN` | `BOX_OAUTH_REFRESH_TOKEN` |
| (new) | `BOX_AUTH_MODE` |

Non-auth vars (`BOX_UPLOAD_*`, `BOX_STORAGE_*`) keep their existing names.

Update `EnvConfigProvider`, `.env.dist`, all commands, and all tests that reference old env var names.

### Update `src/Service/EnvConfigProvider.php`

- Implement `JwtConfigProviderInterface`.
- Update all OAuth2 getters to read from `BOX_OAUTH_*` vars. Public method names remain unchanged — only the env var keys change.
- Add `getAuthMode()` reading `BOX_AUTH_MODE` (nullable).
- Add all `getJwt*()` methods reading `BOX_JWT_*` vars.
- `getJwtClientId()` and `getJwtClientSecret()` are required — throw `RuntimeException` if empty.

### Update `src/Service/BoxClientFactory.php`

In `createClient()`:

    if ($this->configProvider instanceof JwtConfigProviderInterface
        && $this->configProvider->getAuthMode() === 'jwt') {
        return $this->createJwtClient();
    }
    return $this->createOAuth2Client();

Extract existing OAuth2 logic into private `createOAuth2Client(): Client`.

New private `createJwtClient(): Client`:
- Load PEM file via `file_get_contents()` — throw `BoxException` if not readable.
- Build `JwtAuthConfig` from provider values and loaded PEM.
- Instantiate `JwtAssertionGenerator`.
- Build `JwtProvider` with a new `Connection`, `TokenFactory`, config, and generator.
- Create `Client`, inject via `$client->setAuthProvider($jwtProvider)`.
- Apply logger if set.

### Update `src/Client.php`

Remove vestigial OAuth2-specific state (verify no callers first):
- `Client::getState()` and `setState()`
- `Client::getRedirectUri()` and `setRedirectUri()`

Audit `Client::refreshToken()`: if it guards on non-null refresh token before delegating, add:

    if (!($this->getAuthProvider() instanceof JwtProviderInterface) && null === $token->getRefreshToken()) {
        // existing guard
    }

Add JWT facade methods:

    public function exchangeForEnterpriseToken(): TokenInterface
    {
        $provider = $this->getAuthProvider();
        if (!$provider instanceof JwtProviderInterface) {
            throw new BoxException('Current auth provider does not support JWT token exchange.');
        }
        return $provider->exchangeForEnterpriseToken();
    }

    public function exchangeForAppUserToken(string $userId): TokenInterface
    {
        $provider = $this->getAuthProvider();
        if (!$provider instanceof JwtProviderInterface) {
            throw new BoxException('Current auth provider does not support JWT token exchange.');
        }
        return $provider->exchangeForAppUserToken($userId);
    }

### Tests

Update `tests/Service/EnvConfigProviderTest.php` for `BOX_OAUTH_*` rename and new JWT methods.
Update `tests/Service/BoxClientFactoryTest.php` for JWT mode detection.
Update `tests/ClientTest.php` for JWT facade methods and wrong-provider throws.

### Validation After Slice 15.3

`composer test && composer analyse && composer cs:check`

---

## Slice 15.4 — CLI Support and Redaction

### New `src/Command/JwtTokenCommand.php`

Command name: `box:jwt:token`. Extends `AbstractBoxCommand`.

Options:
- `--user-id` (`VALUE_REQUIRED`, optional): App User token for this Box user ID
- `--enterprise` (`VALUE_NONE`, optional): enterprise service account token

Behavior:
- Both flags together: error — mutually exclusive.
- Neither flag: error with clear explanation of both options.
- `--user-id`: call `$client->exchangeForAppUserToken($userId)`.
- `--enterprise`: call `$client->exchangeForEnterpriseToken()`.

Output: token type and expiry. Access token masked. Never emit private key, passphrase, or raw JWT assertion.

### Update `.env.dist`

    # Auth Mode: 'jwt' for JWT/S2S auth, leave unset or 'oauth2' for OAuth2
    BOX_AUTH_MODE=

    # OAuth2 Credentials
    BOX_OAUTH_CLIENT_ID=
    BOX_OAUTH_CLIENT_SECRET=
    BOX_OAUTH_REDIRECT_URI=https://localhost/callback/oauth2/box
    BOX_OAUTH_STATE=
    BOX_OAUTH_AUTH_CODE=
    BOX_OAUTH_ACCESS_TOKEN=
    BOX_OAUTH_REFRESH_TOKEN=

    # JWT / S2S Credentials
    BOX_JWT_CLIENT_ID=
    BOX_JWT_CLIENT_SECRET=
    BOX_JWT_ENTERPRISE_ID=
    BOX_JWT_PUBLIC_KEY_ID=
    BOX_JWT_PRIVATE_KEY_PATH=
    BOX_JWT_PRIVATE_KEY_PASSPHRASE=

    # Uploads
    BOX_UPLOAD_FILE_PATH=
    BOX_UPLOAD_FOLDER_ID=0

    # Token Storage (optional)
    BOX_STORAGE_PDO_DSN=
    BOX_STORAGE_PDO_USER=
    BOX_STORAGE_PDO_PASS=

### Update `src/Service/ConsoleOutputFormatter.php`

Extend sensitive field masking list: `private_key`, `private_key_passphrase`, `jwt_assertion`, `passphrase`. Use the existing `formatMasked()` mechanism only.

### Register `JwtTokenCommand`

Find the CLI entry point (likely `bin/box`) and register `JwtTokenCommand` alongside existing commands.

### Tests

`tests/Command/JwtTokenCommandTest.php`:
- `--user-id` invokes app user exchange.
- `--enterprise` invokes enterprise exchange.
- Both flags together: error.
- Neither flag: descriptive error.
- JSON output does not contain unmasked private key, passphrase, or assertion.

Update any existing command tests referencing old `BOX_CLIENT_ID` / `BOX_CLIENT_SECRET` names.

### Validation After Slice 15.4

`composer test && composer analyse && composer cs:check && composer lint`

---

## Final Step 15 Close-Out

1. Run `composer review` — all checks must pass.
2. Update `docs/planning/v1-release-roadmap.md`: mark Step 15 and all sub-slices complete; set strategic status to **Box API Coverage Alignment (Step 15.1)**.
3. Update `docs/ai/current-handoff-summary.md`: Step 15 outcomes, `Next Step Status: Pending Approval`.
4. Update `docs/ai/current-task-summary.md`.
5. Create `docs/audits/15-jwt-s2s-implementation-audit.md`: deviations from Step 14 plan, security checklist, breaking change note for `BOX_OAUTH_*` rename, test coverage summary, deferrals.
6. Add migration note to upgrade documentation covering the `BOX_OAUTH_*` env var rename.
7. Commit all source and documentation changes.
8. Confirm no real credentials, PEM key content, or enterprise IDs appear in any committed file.

---

## Existing Utilities to Reuse

- `OAuth2Provider::TOKEN_URI` and `OAuth2Provider::REVOKE_URI` — reuse in `JwtProvider`, do not duplicate URI strings.
- `TokenFactoryInterface` / `TokenFactory` — same path as OAuth2, no changes needed.
- `AbstractBoxCommand::applyStorageOption()` — `JwtTokenCommand` inherits this.
- `ConsoleOutputFormatter::formatMasked()` — extend masking list only.

## Acceptance Criteria

- Phase A committed before Phase B begins.
- Phase B slices implemented and validated in order.
- `JwtProvider` accepts `JwtAssertionGeneratorInterface`.
- `JwtProvider::refreshToken()` never calls `$token->getRefreshToken()`.
- `BOX_OAUTH_*` rename complete across all files; migration note written.
- `Client::getState/setState/getRedirectUri/setRedirectUri` removed.
- JWT CLI command requires explicit `--user-id` or `--enterprise`.
- JWT-sensitive fields masked in all CLI and log output.
- `composer review` passes 100%.
- No real credentials or PEM content in any committed file.
- All docs reconciled and committed.