# AI Handoff Summary

- **Timestamp**: 2026-05-15 00:44 (America/Indiana)
- **Project**: `chancegarcia/box-api-v2-sdk` (PHP 8.4+)

## Status
- **Roadmap Position**: Slices 17 and 18 complete. Doc cleanup complete. Slice 19 is next (not started).
- **Test baseline**: 334 tests, 902 assertions
- **v1 remaining**: Slice 19 (Chunked Upload + PSR-14 Events) → package/repo rename (user-driven)

## Next Action

**Start Slice 19 — Chunked Upload + PSR-14 Events.**

Read `docs/ai/next-session-plan.md` for the 8-gate plan. Open questions and doc cleanup tasks are all resolved — go straight to Gate 1.

Do not prompt about package/repo rename.

---

## Completed This Session (2026-05-15)

### Documentation Cleanup (all 7 tasks done)
1. **CHANGELOG.md** — Removed v0.11.4 and v0.11.5 entries; v1.0.0 now flows directly to v0.11.3.
2. **docs/README.md** — Updated API coverage matrix link, removed rename plan link, replaced AI status section with `[Internal](internal/status.md)`.
3. **docs/internal/status.md** — Created; links to handoff, task summary, next session plan, and rename plan.
4. **docs/audits/api-coverage-matrix.md** — Created (renamed from `15.5-api-coverage-matrix.md`); all ❌ endpoints flipped to ✅, service base cleanup noted as complete, deferred list accurate. Old file deleted.
5. **docs/planning/v1/overview.md** — API Coverage Expansion section updated to clearly show what shipped vs. deferred to v1.x.
6. **docs/planning/v1/architecture-rules.md** — Retry section marked "not implemented, deferred to v1.1"; `RetryExhaustedException` flagged as not yet in v1.0.
7. **docs/planning/v1/strategy-and-contracts.md** — JWT updated to "implemented", retry flagged deferred, Metadata bumped to v1.1.0, open questions resolved or closed.
8. **docs/user/programmatic-usage.md** — Fixed awkward "Models (Resources)" bullet under Data Types.

### Deferred (Post-Slice-19)
- `llms.txt` for the SDK — user-facing convention for LLM tooling; review after Slice 19 when API surface is more complete. Consider adding to Gate 8 docs at that point.

---

## Slice 19 — Chunked Upload + PSR-14 Events

8-gate plan in `docs/ai/next-session-plan.md`. Gates in order:
1. PSR-14 infrastructure
2. FileStream additions
3. DTOs
4. FileService low-level API
5. Orchestrator
6. Client facade
7. Tests
8. Documentation (+ `llms.txt` review)

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
