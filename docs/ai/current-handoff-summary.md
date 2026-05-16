# AI Handoff Summary

- **Timestamp**: 2026-05-16 01:15 (America/Indiana)
- **Project**: `chancegarcia/box-api-v2-sdk` (PHP 8.4+)

## Status
- **Roadmap Position**: Slice 20 complete. Slice 20.5 complete. Next: Slice 21.
- **Test baseline**: 372 tests, 1002 assertions
- **v1 remaining**: Slice 21 (docblock/legacy tags) → Slice 22 (license/rebrand prep) → Step 17 (release readiness) → Step 18 (doc cleanup) → package/repo rename (user-driven)

## Next Action

**Slice 21 — Docblock Quality & Legacy Tag Cleanup** (read `docs/ai/next-session-plan.md` for full scope):

1. `@inheritdoc` correctness audit
2. `@package` / `@subpackage` removal from `src/` and `tests/`
3. `ConnectionInterface` / `EntrySource` architectural review
4. `json_encode` / `json_decode` hardening (`JSON_THROW_ON_ERROR` everywhere in `src/`)
5. Legacy survivor audit (`Connection::post` `$nameValuePair`/array-params; `FileService`/`FolderService` `method_exists` fallback)
6. Naming convention & method accuracy audit (PSR-12 camelCase sweep + descriptive method name pass)

Do not prompt about package/repo rename.

---

## Completed This Session (2026-05-16)

### Slice 20 — Human Code Review & Cleanup Feedback [COMPLETE ✓]

**Item 5 — Type Coverage Audit**
- `BoxException` / `BoxResponseException`: constructor `$code` → `int|string`; `$boxCode` → `int|string|null`; `$status` → `int|string|null`
- `Event`: `$type`, `$eventType`, `$sessionId` → `?string`; `$eventId` → `string|int|null`
- `AdminEvent`: `$streamType` → `string`, `$limit` → `int`, date fields → `?string`
- `Service` / `ServiceInterface`: `$connection` → `?ConnectionInterface`, `$token` → `?TokenInterface`; all methods fully typed
- `Collaboration`: `$id` → `string|int|null`, date fields → `DateTimeInterface|string|null`, scalar fields → `?string`
- `Folder`: scalar fields narrowed, `$size` → `?int`, `$hasCollaborations` → `?bool`
- `SharedLink` + `Permissions`: all properties were untyped — now fully typed
- `EventCollection`: `$chunkSize`/`$nextStreamPosition` → `int|string|null`, `$entries` → `?Collection`
- Intentional `mixed` left with inline comments where Box API is genuinely polymorphic

**Item 6 — Property Hooks**: Deferred to post-v1. Rationale recorded in roadmap and next-session plan.

**Item 7 — BoxClientFactory Namespace Move**
- `Box\Service\BoxClientFactory` → `Box\Factory\BoxClientFactory`
- `Box\Contract\BoxClientFactoryInterface` → `Box\Factory\BoxClientFactoryInterface`
- `createClient()` → `createOAuth2Client()` on class and interface
- All command files, tests, and `bin/box-sdk` updated
- Migration guide section 10 added

### Constructor Hydration Cleanup (companion to type audit)
- `AdminEvent`, `Token`, `Connection`, `AuthenticationResponse` — hydration removed from constructors
- Hydration moved to: `TokenFactory`, `ConnectionFactory`, `AuthenticationResponseFactory`
- Storage classes (`FilesystemTokenStorage`, `TokenStorage` PDO) now hydrate explicitly after blank construction
- Tests updated to use factories where previously passing arrays to constructors

### `ext-pdo` added to `composer.json` require
PDO is directly instantiated and type-hinted in `src/Storage/Token/Pdo/` — hard runtime dependency, now declared.

### `static fn` style enforcement
- All arrow functions in `src/` that do not reference `$this`/`self`/`static`/`parent` converted to `static fn`
- Files touched: `CollaborationService`, `FileService` (×3), `FolderService`, `LoggerFactory`
- Rule added to global memory (`feedback_code_style.md`) and canonical style doc (`docs/prompts/ai-workflow/php-code-style-guidance.md`)

### Slice 20.5 — Enum Wiring & Hydrator Audit [COMPLETE ✓]

**Hydrator fix**: Switched `::from()` → `::tryFrom()`; unknown enum values now skip the setter; union types containing `array` pass raw arrays through correctly.

**Enum wiring:**
- `CollaborationRole` → `Collaboration::$role` (was `?string`)
- `CollaborationStatus` enum created (`Accepted`, `Pending`, `Rejected`); wired to `Collaboration::$status`; old `in_array` validation removed
- `SharedLinkAccess` → `SharedLink::$access` (was `?string`)
- `BoxItemType` — evaluated, left as utility enum (no resource setter to wire to)

**DTOs / narrowing:**
- `PathCollection` DTO created (`src/Dto/PathCollection.php`); `File::setPathCollection` and `Folder::setPathCollection` coerce raw API arrays internally
- `File::setSharedLink` narrowed to `?SharedLink`

**Tests**: HydratorTest updated; CollaborationFactoryTest and CollaborationServiceTest updated for enum types; PathCollectionTest and EnumTest additions.

**Migration guide**: Sections 11–13 added.

### Roadmap / planning updates
- Property hooks deferred to post-v1; rationale recorded in roadmap Deferred section
- Consistent docblocks added as post-v1 item
- PHP 8.4 property hooks added as post-v1 item

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
