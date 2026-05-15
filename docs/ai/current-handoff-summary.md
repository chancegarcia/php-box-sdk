# AI Handoff Summary

- **Timestamp**: 2026-05-15 05:10 (America/Indiana)
- **Project**: `chancegarcia/box-api-v2-sdk` (PHP 8.4+)

## Status
- **Roadmap Position**: Slice 19 Gates 1–8 complete. Gate 9 is next.
- **Test baseline**: 342 tests, 958 assertions
- **v1 remaining**: Slice 19 Gate 9 (additional PSR-14 events) → BoxClientFactory namespace + rename slice → package/repo rename (user-driven)

## Next Action

**Gate 9** — additional PSR-14 events (token lifecycle, FileUploaded, RateLimitHit, JwtTokenGenerated).

Read `docs/ai/next-session-plan.md` for full Gate 9 scope.

**Before executing**, verify two things in source:
1. Where the 429 boundary lives (probably `Service::handleBoxResponse` — grep for `429` or `RateLimitException`)
2. How `JwtProvider` will receive the dispatcher — does it already see the `Client`'s dispatcher, or does it need injection?

**Tooling note**: run phpcbf via `composer cs:fix`, not `./vendor/bin/phpcbf` directly — the composer shortcut is pre-approved in `.claude/settings.local.json`.

Do not prompt about package/repo rename.

---

## Completed This Session (2026-05-15)

### Pre-Gate-9 Documentation
- **`docs/user/api-coverage.md`** — Created; lists every supported Box API endpoint per service class with Method, HTTP, Box Endpoint, Notes columns. Includes chunked upload low-level session API sub-section. Search entry notes why raw array return is deferred (heterogeneous results, no discriminated union).
- **`docs/README.md`** — Added link to `api-coverage.md` under User Documentation.

### Typed Return Refactor (raw `array` → typed objects across 7 service methods)

New types:
- **`src/Dto/PagedResult.php`** — `@template T of object` readonly DTO; properties: `entries` (`T[]`), `totalCount`, `offset`, `limit`.
- **`src/Resource/GroupMembership.php`** — new resource; properties: `type`, `id`, `user`, `group`, `role`, `createdAt`, `modifiedAt`.
- **`Service::hydratePagedResult(array $data, string $entryClass): PagedResult`** — protected helper on base `Service`; used by all 5 collection methods.

Methods updated (return type before → after):

| Method | Before | After |
|---|---|---|
| `FileService::uploadFile()` | `array` | `File` |
| `FolderService::updateFolder()` | `array` | `Folder` |
| `UserService::listUsers()` | `array` | `PagedResult<User>` |
| `GroupService::listGroups()` | `array` | `PagedResult<Group>` |
| `GroupService::addGroupMember()` | `array` | `GroupMembership` |
| `GroupService::getGroupMembershipList()` | `array` | `PagedResult<GroupMembership>` |
| `CollaborationService::getFolderCollaborations()` | `array` | `PagedResult<Collaboration>` |

`FolderService::getFolderCollaborations()` **removed** — was a duplicate; `CollaborationService` is canonical. `FolderServiceInterface` updated to match.

`Client.php` updated: `updateBoxFolder()` → `Folder`; `getFolderCollaborations()` → `PagedResult<Collaboration>`.

### Migration Guide Updated
- **`docs/migration/upgrading-0.11-to-1.0.md`** — Added Section 9 (Service Method Return Type Changes) with full table, `PagedResult<T>` and `GroupMembership` introductions, `getFolderCollaborations` removal note, and Client facade updates. "What Stayed the Same" corrected to distinguish single-resource reads (unchanged) from collection/mutating methods (updated).

### Legacy Regression Tests Deleted
- `tests/Resource/LegacyRemovalTest.php` — deleted (tested that old `Box\Model\*` classes don't exist)
- `tests/Resource/UserMigrationTest.php` — deleted (tested that old `Box\User\*` classes don't exist)
- `testServiceDoesNotDependOnLegacyUserModel` removed from `UserServiceTest`

Tests that cover **current** behavior retained and updated to assert typed returns.

Two tests with "legacy" in their name were verified to test current valid behavior and are intentionally kept:
- `BoxResponseTest::testConstructFromLegacyInputs` — tests `BoxResponse` constructed from raw content+header strings (supported constructor path)
- `FileServiceTest::testCreateSharedLinkWithLegacySharedLink` — tests passing a `SharedLink` object to `createSharedLink()` (valid current type in the signature)

---

## Slice 19 — Chunked Upload + PSR-14 Events

9-gate plan in `docs/ai/next-session-plan.md`. Gates in order:
1. PSR-14 infrastructure ✅
2. FileStream additions ✅
3. DTOs ✅
4. FileService low-level API ✅
5. Orchestrator ✅
6. Client facade ✅
7. Tests ✅
8. Documentation ✅
9. Additional PSR-14 events — token lifecycle, FileUploaded, RateLimitHit, JwtTokenGenerated

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
- PSR-14: Optional `EventDispatcherInterface` injection on `Client` — same pattern as PSR-3 logger.
- Chunked upload: low-level session API public on `FileService`; orchestrator on `FileService` + `Client` facade.
- Chunked upload part SHA1: `base64_encode(sha1($chunk, true))` — raw binary flag required.
- Chunked upload whole-file SHA1: incremental via `hash_init/update/final`.
- Auto-retry / auto-token-refresh: **not implemented**; deferred to v1.1. Only `RateLimitException` exists.
- `ArrayConfigProvider`: good idea, deferred to v1.1 (confirmed this session).
- `SearchService::search` returns raw `array` — intentional; Box returns heterogeneous entries (files, folders, web links) that cannot be strongly typed; deferred to future minor release.
- `PagedResult<T>` (`Box\Dto\PagedResult`) and `GroupMembership` (`Box\Resource\GroupMembership`) are new in this session.

---

## Deferred (Post-Slice-19)
- `llms.txt` — deferred to v1.1 or v1.2. Do not add to Gate 8.
- **PHPDoc quality audit + PHPStan level bump** — audit `T[]` annotations, upgrade to `list<T>` or `array<K, V>`; bump PHPStan level after. Full codebase review required. Post-v1.
- **`BoxClientFactory` namespace move** — currently `Box\Service\BoxClientFactory`; belongs in `Box\Factory`. Breaking change. Slot as own slice after Slice 19.
- **`createOAuth2Client()` rename** — rename `BoxClientFactory::createClient()` to `createOAuth2Client()`. Same slice as namespace move. `BoxClientFactoryInterface` must update in tandem.
