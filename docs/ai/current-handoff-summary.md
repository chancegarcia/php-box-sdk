# AI Handoff Summary

- **Timestamp**: 2026-05-15 23:40 (America/Indiana)
- **Project**: `chancegarcia/box-api-v2-sdk` (PHP 8.4+)

## Status
- **Roadmap Position**: Slice 20 items 1–4, 8 complete. Items 5, 6, 7 remain.
- **Test baseline**: 368 tests, 992 assertions
- **v1 remaining**: Slice 20 items 5–7 → Slice 21 (docblock/legacy tags) → Slice 22 (license/rebrand prep) → Step 17 (release readiness) → Step 18 (doc cleanup) → package/repo rename (user-driven)

## Next Action

**Slice 20 remaining** (read `docs/ai/next-session-plan.md` for full scope):
1. ~~`?->` operator conversions~~ ✓
2. ~~PHPDoc annotation spacing standardized~~ ✓
3. ~~`@throws` audit~~ ✓
4. ~~Typed constants audit~~ ✓
5. Type coverage audit (tighten `mixed`, untyped properties/params/returns; includes BoxException, Client, Event specifics)
6. Property hooks on qualifying DTOs/value objects
7. BoxClientFactory namespace move (`Box\Service` → `Box\Factory`) + `createClient()` → `createOAuth2Client()` rename
8. ~~v1 `@todo` audit~~ ✓

Do not prompt about package/repo rename.

---

## Completed This Session (2026-05-15) — Late additions

### Legacy survivor cleanup
- `Folder::classArray()` removed — zero callers; only purpose was building a `sync_state` payload for the discontinued Box Sync client
- `Folder::setSyncState()` / `getSyncState()` / `$syncState` property removed — same rationale
- `AdminEvent::mapBoxToClass()` removed — `@deprecated`, zero callers
- Orphaned `use Box\Exception\BoxException` import removed from `Folder.php`
- Orphaned `use stdClass` import removed from `AdminEvent.php`
- Slice 21 expanded with **Legacy Survivor Audit** scope item covering `Connection::post` `$nameValuePair`/array-params "deprecated in future" language and `FileService`/`FolderService` `method_exists($sharedLink, 'toArray')` legacy fallback
- Roadmap `docs/planning/v1-release-roadmap.md` Slice 20.5 updated: `FolderSyncState` removed from scope with rationale documented
- Test baseline confirmed unchanged: **368 tests, 992 assertions**

---

## Completed This Session (2026-05-15)

### Pre-release task list
- **`docs/planning/release-task-lists.md`** — Added `v1.0.0 Pre-Release Checklist` section (separate from v1 refactor steps): copyright date audit, GitHub repo rename, composer.json name/URL update, internal doc update, Packagist submission.

### Gate 9 — Additional PSR-14 Events (complete)

**New event classes:**

| Class | Namespace | Payload |
|---|---|---|
| `TokenExchanged` | `Box\Event\Auth` | `TokenInterface $token` |
| `TokenRefreshed` | `Box\Event\Auth` | `TokenInterface $token` |
| `TokenRevoked` | `Box\Event\Auth` | `TokenInterface $token` |
| `TokenLoadedFromStorage` | `Box\Event\Auth` | `TokenInterface $token` |
| `TokenSavedToStorage` | `Box\Event\Auth` | `TokenInterface $token` |
| `JwtTokenGenerated` | `Box\Event\Auth` | `TokenInterface $token` |
| `FileUploaded` | `Box\Event\File` | `File $file` |
| `RateLimitHit` | `Box\Event\Http` | `int $retryAfter` |

**Wiring:**
- `Client.php` — fires `TokenExchanged`, `TokenRefreshed`, `TokenRevoked`, `TokenLoadedFromStorage`, `TokenSavedToStorage`; propagates dispatcher to `Connection` and `JwtProvider` in `setEventDispatcher()`, `setConnection()`, and `setAuthProvider()`
- `FileService::uploadFile()` — fires `FileUploaded` after hydrating the result
- `Connection::request()` — fires `RateLimitHit` before throwing `RateLimitException` on 429; optional `setEventDispatcher()` added to `Connection` (not to `ConnectionInterface`)
- `JwtProvider::exchangeAssertion()` — fires `JwtTokenGenerated` after `tokenFactory->createToken()`; optional `setEventDispatcher()` added to `JwtProvider`

**Also fixed:** `Client::uploadFileToBox()` return type corrected from `array` to `File` (mismatch introduced in the previous session's typed return refactor); dispatcher now propagated to FileService in `uploadFileToBox()` matching the pattern in `chunkedUpload()`.

**Tests added (26 new assertions):**
- `tests/Event/EventConstructionTest.php` — 9 construction tests for all 8 new event classes
- `tests/Service/File/FileUploadedEventTest.php` — 3 tests for FileUploaded dispatch
- `tests/Connection/RateLimitHitEventTest.php` — 4 tests for RateLimitHit dispatch
- `tests/Auth/Jwt/JwtTokenGeneratedEventTest.php` — 3 tests for JwtTokenGenerated dispatch
- `tests/Client/TokenLifecycleEventTest.php` — 6 tests for token lifecycle events

**Docs:** Added §14 Events Reference to `docs/user/programmatic-usage.md` — full table of all dispatched events, payloads, and fire conditions; includes v1.1 note for auto-retry/auto-refresh hooks.

---

## Slice 19 — Chunked Upload + PSR-14 Events [COMPLETE ✓]

All 9 gates:
1. PSR-14 infrastructure ✅
2. FileStream additions ✅
3. DTOs ✅
4. FileService low-level API ✅
5. Orchestrator ✅
6. Client facade ✅
7. Tests ✅
8. Documentation ✅
9. Additional PSR-14 events ✅

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
- PSR-14: Optional `EventDispatcherInterface` injection on `Client` — same pattern as PSR-3 logger. Dispatcher propagated to `Connection` and `JwtProvider` via `instanceof` checks (not interface-enforced — pragmatic for v1).
- Chunked upload: low-level session API public on `FileService`; orchestrator on `FileService` + `Client` facade.
- Chunked upload part SHA1: `base64_encode(sha1($chunk, true))` — raw binary flag required.
- Chunked upload whole-file SHA1: incremental via `hash_init/update/final`.
- Auto-retry / auto-token-refresh: **not implemented**; deferred to v1.1. `RateLimitHit` and `TokenRefreshed` events are the hook points.
- `ArrayConfigProvider`: good idea, deferred to v1.1.
- `SearchService::search` returns raw `array` — intentional; Box returns heterogeneous entries.
- `PagedResult<T>` (`Box\Dto\PagedResult`) and `GroupMembership` (`Box\Resource\GroupMembership`) added in previous session.

---

## Deferred (Post-Slice-19)
- `llms.txt` — deferred to v1.1 or v1.2.
- **PHPDoc quality audit + PHPStan level bump** — post-v1.
- **`BoxClientFactory` namespace move** — currently `Box\Service\BoxClientFactory`; belongs in `Box\Factory`. Breaking change. Own slice after Slice 19.
- **`createOAuth2Client()` rename** — rename `BoxClientFactory::createClient()` to `createOAuth2Client()`. Same slice as namespace move. `BoxClientFactoryInterface` must update in tandem.
