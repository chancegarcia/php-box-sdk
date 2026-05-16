# AI Handoff Summary

- **Timestamp**: 2026-05-16 02:07 (America/Indiana)
- **Project**: `chancegarcia/box-api-v2-sdk` (PHP 8.4+)

## Status
- **Roadmap Position**: Slice 21 complete. Next: Slice 22.
- **Test baseline**: 372 tests, 1002 assertions
- **v1 remaining**: Slice 22 (license/rebrand prep) → Step 17 (release readiness) → Step 18 (doc cleanup) → package/repo rename (user-driven)

## Next Action

**Slice 22 — License & Rebrand Preparation** (read `docs/ai/next-session-plan.md` for full scope).

Do not prompt about package/repo rename.

---

## Completed This Session (2026-05-16)

### Slice 21 — Docblock Quality & Legacy Tag Cleanup [COMPLETE ✓]

**Item 1 — `@inheritdoc` Correctness**
- All lone `{@inheritdoc}` / `@inheritdoc` docblocks removed (entire block = just the tag): 47 instances across 10 files. Typed signatures make them redundant.
- Mixed-content docblocks: `@inheritdoc` tag removed, valuable `@throws`/`@return` tags kept.

**Item 2 — `@package` / `@subpackage` Removal**
- Removed from all 41 `src/` PHP files via perl one-liner.
- Empty/redundant class docblocks cleaned up (`/**\n */` and `/** Class X\n *\n */`).
- PHPCS auto-fixed docblock spacing issues from blank lines left behind.

**Item 3 — `ConnectionInterface` / `EntrySource` Review**
- Both confirmed v1-sound. No removals needed.
- `ConnectionInterface` docblock deprecation language removed (see Item 5).
- `EntrySource`: `mixed $synced` field retained with comment explaining Box Sync polymorphism.

**Item 4 — `json_encode` / `json_decode` Hardening**
- `JSON_THROW_ON_ERROR` added to all bare `json_encode`/`json_decode` calls in `src/`:
  - `JwtAssertionGenerator::generate()` (×2)
  - `JwtTokenCommand::__invoke()` output
  - `Service::sendUpdateAndHydrate()`
  - `FolderService::createFolder()` and `copyFolder()`
  - `BoxResponse::json()` — now throws `\JsonException` (previously returned `[]` on bad JSON)
  - `BoxResponseException` constructor — wrapped in `try/catch(\JsonException)` (can't throw from constructor)
- `BoxResponseTest::testJsonDecodingInvalidJson` updated to expect `\JsonException`.

**Item 5 — Legacy Survivor Audit**
- `$nameValuePair` parameter removed from `Connection::post()` and `ConnectionInterface::post()`.
- Array→form-encoded path in `post()` and `put()` kept (used by OAuth2 token endpoints); deprecation warnings removed.
- `Client.php` collaboration POST updated to pass `json_encode($params, JSON_THROW_ON_ERROR)` directly.
- `FileService::normalizeSharedLinkPayload()` — dead `method_exists($sharedLink, 'toArray')` fallback removed (both `SharedLink` and `CreateSharedLinkRequest` have `toArray()`).
- `FolderService::updateFolder()` — `method_exists($sharedLink, 'toArray')` kept (Folder::$sharedLink is `mixed`, guard is live).

**Item 6 — Naming Convention Audit**
- No snake_case method names or parameters found in `src/`.
- `$sStatusLine`/`$sHeader` in `StatusLine`/`ResponseHeader` are camelCase (Hungarian prefix, no underscores) — PSR-12 compliant.
- No public method renames required.

**`@throws` Chain Coverage (user-requested mid-slice)**
- Added `@throws \JsonException` to every method in the bubble chain up to catch boundaries:
  - `JwtAssertionGeneratorInterface::generate()` and implementation
  - `AuthProviderInterface::exchangeAuthorizationCode()` and `refreshToken()`
  - `JwtProviderInterface::exchangeForEnterpriseToken()` and `exchangeForAppUserToken()`
  - All `JwtProvider` and `OAuth2Provider` implementations
  - `FolderServiceInterface`/`FolderService`: `createFolder`, `updateFolder`, `createSharedLink`, `copyFolder`
  - `CollaborationServiceInterface`/`CollaborationService`: `addCollaboration`, `updateCollaboration`
  - `FileServiceInterface`/`FileService`: `updateFile`, `createSharedLink`
  - `GroupServiceInterface`: `createGroup`, `addGroupMember`
  - `Client`: `exchangeAuthorizationCodeForToken`, `refreshToken`, `addCollaboration`, `createNewBoxFolder`, `updateBoxFolder`, `createSharedLinkForFolder`, `copyBoxFolder`

**Style rules added (user-requested mid-slice)**
- `@throws` chain coverage rule added to global memory and `php-code-style-guidance.md`
- `array` generic syntax rule added (all `@param array`, `@return array` must use `list<T>` / `array<K,V>` / shape)
- `json_encode`/`json_decode` hardening rule added (always `JSON_THROW_ON_ERROR`)

**Migration guide** — Sections 14 and 15 added:
- Section 14: `$nameValuePair` parameter removed from `Connection::post()`
- Section 15: `BoxResponse::json()` now throws `\JsonException` on malformed JSON

---

## Key Architecture Decisions (Carry Forward)
- Auth providers: `OAuth2Provider` and `JwtProvider` both implement `AuthProviderInterface`.
- Env vars: `BOX_OAUTH_*` (OAuth2), `BOX_JWT_*` (JWT), `BOX_AUTH_MODE` (mode selector).
- Config provider methods: provider-prefixed — `getOAuth2ClientId()`, `getJwtClientId()`, etc.
- Private key: `EnvConfigProvider` reads PEM file; `JwtAuthConfig::$privateKey` is always PEM content.
- CLI transport: `--transport` removed; `ConnectionInterface` transport methods kept for programmatic use.
- CLI storage: `--storage-type filesystem` (default) or `--storage-type pdo`.
- JWT CLI: `box:jwt:token` (enterprise) / `box:jwt:token --user-id=<ID>` (app user).
- `BoxApiErrorTrait::error()` return type is `never` — always throws.
- Webhook signing: `Box\Webhook\WebhookVerifier`; formula `base64(HMAC-SHA256(body + timestamp, key))`; CRUD deferred.
- PSR-14: Optional `EventDispatcherInterface` injection on `Client` — propagated to `Connection` and `JwtProvider` via `instanceof` checks.
- Chunked upload: low-level session API public on `FileService`; orchestrator on `FileService` + `Client` facade.
- Auto-retry / auto-token-refresh: deferred to v1.1.
- `SearchService::search` returns raw `array` — intentional; Box returns heterogeneous entries.
- `PagedResult<T>` and `GroupMembership` in previous sessions.
- `BoxClientFactory`: now `Box\Factory\BoxClientFactory`; `createOAuth2Client()` (was `createClient()`).
- Constructors: `Token`, `Connection`, `AuthenticationResponse`, `AdminEvent` no longer self-hydrate — hydration belongs in factories.
- Enums: `CollaborationRole`, `CollaborationStatus`, `SharedLinkAccess` wired to resource setters; `BoxItemType` is utility-only.
- `PathCollection` DTO: `File` and `Folder` coerce raw API arrays in `setPathCollection`.
- `static fn` over `fn` when closure has no `$this`/`self`/`static`/`parent` reference.
- `Connection::post()` / `put()`: array params → form-encoded (for OAuth2); string params → passed as-is (for JSON bodies). No `$nameValuePair` parameter.
- `BoxResponse::json()` throws `\JsonException` on malformed JSON — no silent fallback.
- All `json_encode`/`json_decode` calls use `JSON_THROW_ON_ERROR`.
