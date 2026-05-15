# Next Session Plan

**Updated**: 2026-05-15 05:10 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

`docs/user/api-coverage.md` is done. Gates 1–8 are complete. Pre-Gate-9 cleanup verified complete (search note, migration guide, legacy tests).

**Only task: Gate 9** — additional PSR-14 events.

**Before executing**, verify two things in source:
1. Where the 429 boundary lives — grep for `429` or `RateLimitException` in `src/`. Likely `Service::handleBoxResponse`.
2. How `JwtProvider` will receive the event dispatcher — does it already see the `Client`'s dispatcher, or does it need injection?

**Tooling**: use `composer cs:fix` (not `./vendor/bin/phpcbf`) — pre-approved in `.claude/settings.local.json`.

---

## Gate 9: Additional PSR-14 Events

Extend the event infrastructure beyond chunked upload to cover the rest of the SDK surface. Event classes go in `src/Event/` under the namespace that matches the firing site.

**Token lifecycle** — `src/Event/Auth/` — wire into `Client.php`:
- `TokenExchanged` — holds `TokenInterface $token`; fired after `exchangeAuthorizationCodeForToken()` succeeds
- `TokenRefreshed` — holds `TokenInterface $token`; fired after `refreshToken()` succeeds; primary hook point for v1.1 auto-refresh
- `TokenRevoked` — holds `TokenInterface $token`; fired after `destroyToken()` succeeds
- `TokenLoadedFromStorage` — holds `TokenInterface $token`; fired after `loadTokenFromStorage()` returns a token
- `TokenSavedToStorage` — holds `TokenInterface $token`; fired after `saveTokenToStorage()` writes

**Standard file upload** — `src/Event/File/` — wire into `FileService::uploadFile()`:
- `FileUploaded` — holds `\Box\Resource\File $file`; gives consumers a uniform event surface across both upload paths

**Rate limiting** — `src/Event/Http/` — wire into the 429 exception boundary in `Connection` (or `Service`):
- `RateLimitHit` — holds `int $retryAfter`; observability now, natural hook for v1.1 auto-retry loop

**JWT token generation** — `src/Event/Auth/` — wire into `JwtProvider::getToken()`:
- `JwtTokenGenerated` — holds `TokenInterface $token`; audit trail for enterprise S2S deployments

**Tests**: construction tests for each new event class; wire-up tests confirming events are dispatched (mock dispatcher, assert `dispatch()` called with correct event type).

**Docs**: add an "Events reference" section to `programmatic-usage.md` listing all dispatched events, their payload, and when they fire.

---

## Resolved Questions (do not re-open)

### Q1: EnvConfigProvider framing
`EnvConfigProvider` is environment-variable-driven, not CLI-exclusive. Any app populating `$_ENV`/`$_SERVER` (Symfony DotEnv, Docker env, etc.) can use it. CLI-only vars: `BOX_UPLOAD_FILE_PATH`, `BOX_UPLOAD_FOLDER_ID`, `BOX_JSON_FORMATTER`.

`ArrayConfigProvider` (accepts a plain array): good idea, confirmed deferred to v1.1.

### Q2: Auto-Retry + Auto-Token-Refresh
Not implemented. Deferred to v1.1. Only `RateLimitException` (wired to 429) exists.

### Q3: CHANGELOG v0.11.4 / v0.11.5
Removed both entries. Work was never tagged or released; v1.0.0 entry covers it.

### Q4: SearchService raw array return
Intentional. Box search returns heterogeneous entries (files, folders, web links) that cannot be strongly typed without a discriminated union. Typed return deferred to a future minor release. Noted in `docs/user/api-coverage.md`.

### Q5: FolderService::getFolderCollaborations
Removed from `FolderService` and `FolderServiceInterface`. `CollaborationService::getFolderCollaborations()` is the canonical home. Migration guide updated (Section 9).

---

## Acceptance Criteria for Slice 19

- Slice 19 all 9 gates complete
- `composer review` green
- `programmatic-usage.md` chunked upload + doc gaps + events reference filled
- API coverage matrix updated to reflect chunked upload as ✅
