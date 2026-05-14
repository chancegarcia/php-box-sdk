# AI Handoff Summary

- **Timestamp**: 2026-05-14 16:05:00 (America/Indiana)
- **Project**: `chancegarcia/box-api-v2-sdk` (PHP 8.4+)

## Status
- **Next Step Status**: In Progress
- **Roadmap Position**: Pre-v1 fix slices in progress. Next: ServiceInterface cleanup slice.
- **Test baseline**: 330 tests, 898 assertions (unchanged from Slice 16)

## Completed This Session

### Audit
Full `src/` code smell audit completed → `docs/planning/code_smells_v1.md`.
Key new findings beyond the known blockers:
- **N1** — `FolderService::updateFolder()` `$ifMatch` branch (line 132) was also broken: array passed to `Connection::put()` → URL-encoded body, not JSON. Both branches now fixed.
- **N2** — `Service::sendUpdateAndHydrate()` and `getResourceFromBox()` internally call the legacy methods they're supposed to replace. Must be migrated as part of ServiceInterface cleanup before the legacy methods can be removed.
- **Q1b** — `BoxLoggerTrait` contains two dead methods (`parseResponse()`, `debug()`) and is misnamed. Plan: remove dead methods, rename to `BoxApiErrorTrait`, update three users (`Client`, `Service`, `Connection`). Approved for v1.
- **D1** — Standing rule: check Box API v2 docs before removing any unused property from a resource service. `FileService::$sharedLink`/`$access` are the immediate case. Split into Session A (targeted check) and Session B (full field audit, Step 18 deliverable).

### Fix Slice (complete)
| Finding | Fix |
|:---|:---|
| A1 | `ClientConfig::setoAuth2AuthCode()` renamed to `setOAuth2AuthCode()`. Caller in `BoxClientFactory` updated. |
| B1 | `BoxClientFactory::createClient()` now loads `accessToken`/`refreshToken` from config provider into client token. |
| B2 + N1 | Both branches of `FolderService::updateFolder()` fixed. `$ifMatch` branch: `json_encode($params)` passed to `put()`. Fallback branch: replaced broken `sendUpdateToBox('PUT', ...)` with direct `$this->getConnection()->put()` + `handleBoxResponse()`. |
| Q1 | `BoxLoggerTrait::error()` upgraded to richer implementation. `Service::error()` removed. One unified implementation covers `Client` and all `Service` subclasses. |

## Upcoming Slices

| Slice | Title | Notes |
|:---|:---|:---|
| ServiceInterface cleanup | Remove legacy plumbing from `ServiceInterface` and `Service` base class | See detailed plan below |
| D1-A | FileService property check | Check Box API v2 docs for `$sharedLink` and `$access` on FileService |
| Q1b | BoxLoggerTrait cleanup | Remove `parseResponse()`, `debug()`; rename to `BoxApiErrorTrait` |
| 17 | v1 Release Readiness | Code gate, docs gate (migration guide + user guides + changelog), release metadata |
| 18 | Documentation Cleanup and Organization | Archive step trackers, retire superseded files, fix status drift; includes D1-B full field audit |

## ServiceInterface Cleanup — Detailed Plan (next session)

### What to remove from ServiceInterface
- `TOKEN_URI`, `REVOKE_URI` constants
- `queryBox()`, `putIntoBox()`, `getFromBox()`, `sendUpdateToBox()`, `handleBoxResponse()`
- Audit and likely remove: `getToken()`/`setToken()`, `getConnection()`/`setConnection()`

### Migrate helpers before removing legacy methods
`getResourceFromBox()` and `sendUpdateAndHydrate()` in `Service` currently call the legacy methods internally. Migrate them first:
- `getResourceFromBox($uri, $class)`: replace `getFromBox($uri, 'decoded')` with `$this->getConnection()->query($uri)` + `handleBoxResponse($response, 'decoded')`
- `sendUpdateAndHydrate($uri, $params, $class)`: replace `sendUpdateToBox($uri, $params, 'decoded')` with `$this->getConnection()->put($uri, json_encode($params))` + `handleBoxResponse($response, 'decoded')`

### Migrate concrete service call sites
All remaining `queryBox()` / `getFromBox()` calls in concrete services:

| Service | Legacy call | Replacement |
|:---|:---|:---|
| `FolderService::getFolderItems()` | `queryBox($uri, 'flat')` | `$this->getConnection()->query($uri)` + `handleBoxResponse($response, 'flat')` |
| `FolderService::getFolderCollaborations()` | `queryBox($uri, 'flat')` | same pattern |
| `CollaborationService::getFolderCollaborations()` | `queryBox($uri, 'flat')` | same pattern |
| `GroupService::listGroups()` | `queryBox($uri, 'flat')` | same pattern |
| `UserService::listUsers()` | `queryBox($uri, 'flat')` | same pattern |
| `SearchService::search()` | `queryBox($uri, 'flat')` | same pattern |
| `UserEventService::getEvents()` | `getFromBox($uri, 'decoded')` | `$this->getConnection()->query($uri)` + `handleBoxResponse($response, 'decoded')` |

### Also in this slice
- **A2**: Remove `Connection::connect()` from both `ConnectionInterface` and `Connection` (throws "not implemented").
- **A3**: Narrow `ConnectionInterface::postFile()` return type from `array|BoxResponseInterface` to `BoxResponseInterface`.
- **L3**: Update `FolderService::createFolder()` and `copyFolder()` to pass `json_encode($params)` directly to `post()` and drop the `$nameValuePair=true` arg.
- **`AuthenticatedServiceInterface`**: Update after ServiceInterface is cleaned (it extends ServiceInterface).

## Key Architecture Decisions (Carry Forward)
- Auth providers: `OAuth2Provider` and `JwtProvider` both implement `AuthProviderInterface`.
- Env vars: `BOX_OAUTH_*` (OAuth2), `BOX_JWT_*` (JWT), `BOX_AUTH_MODE` (mode selector).
- Config provider methods: provider-prefixed — `getOAuth2ClientId()`, `getJwtClientId()`, etc.
- Private key: `EnvConfigProvider` reads PEM file; `JwtAuthConfig::$privateKey` is always PEM content.
- CLI transport: `--transport` removed; `ConnectionInterface` transport methods kept for programmatic use.
- CLI storage: `--storage-type pdo` or `--storage-type filesystem` (with `--storage-path` or `BOX_STORAGE_FILE_PATH`).
- Command wiring: manual in `bin/box-sdk`, no DI container.
- No plan mode. Claude Code CLI executes code directly; human reviews and commits.
- `ClientConfig` is a pure OAuth2 DTO — does not implement `ConfigProviderInterface`.
- `BoxClientFactory::createClient()` now loads access/refresh tokens from config provider.
- `ClientConfig::setOAuth2AuthCode()` — lowercase-o typo fixed this session.
- `BoxLoggerTrait::error()` is the single unified error-throwing implementation (Service::error() removed).
- Webhook signing: `Box\Webhook\WebhookVerifier`; formula is `base64(HMAC-SHA256(body + timestamp, key))`; management CRUD deferred.
- Audit doc: `docs/planning/code_smells_v1.md` — full smell registry with slice assignments.

## Transition Note
Continuing in Claude Code CLI (native macOS Terminal). CLAUDE.md exists at project root and will be loaded automatically.
Memory files are at: `~/.claude/projects/-Users-chance-PhpstormProjects-mine-box-sdk/memory/`
