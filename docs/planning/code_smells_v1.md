# Code Smells Audit — Pre-v1

- **Timestamp**: 2026-05-14 15:20:33 (America/Indiana)
- **Scope**: Full `src/` directory audit

---

## 1. Known / Confirmed Blockers (already tracked)

These are in the handoff summary and have approved fix plans.

| ID | Location | Issue |
|:---|:---|:---|
| B1 | `Service/BoxClientFactory::createClient()` | Never loads `accessToken`/`refreshToken` from config provider into the client. |
| B2 | `Service/Folder/FolderService::updateFolder()` line 136 | `sendUpdateToBox($uri, $params, 'PUT', null, 'flat')` — `'PUT'` passed as return-type selector, throws `OutOfBoundsException` at runtime. |
| B3 | `Service/ServiceInterface` | Exposes internal plumbing as public API (`queryBox`, `putIntoBox`, `getFromBox`, `sendUpdateToBox`, `handleBoxResponse`, `TOKEN_URI`, `REVOKE_URI`). Plan approved, sliced for next session. |

---

## 2. Newly Found Bugs

These were not in the handoff and need fixes before v1.

### N1 — FolderService::updateFolder() $ifMatch branch also broken (line 132)
`src/Service/Folder/FolderService.php:132`

```php
$response = $connection->put($uri, $params, true);
```

`Connection::put()` only accepts two params; the third arg (`true`) is silently dropped.
`$params` is an array, so `Connection::put()` runs `http_build_query($params)` — URL-encoded body, not JSON.
Box API expects `application/json`. Both branches of `updateFolder()` are broken.

**Fix (same slice as B2):** Replace line 132 with:
```php
$response = $connection->put($uri, json_encode($params));
```

### N2 — Service::sendUpdateAndHydrate() and getResourceFromBox() call legacy methods internally
`src/Service/Service.php:529,546`

`sendUpdateAndHydrate()` calls `sendUpdateToBox()`. `getResourceFromBox()` calls `getFromBox()` which calls `queryBox()`.
The ServiceInterface cleanup plan migrates call sites in concrete services *to* these two helpers, then removes the legacy methods.
But the helpers themselves still depend on those legacy methods — so removal is impossible without also migrating the helpers first.

**Fix (part of ServiceInterface cleanup slice):**
- `getResourceFromBox()`: replace `getFromBox($uri, 'decoded')` with `$this->getConnection()->query($uri)` + `$this->handleBoxResponse($response, 'decoded')`.
- `sendUpdateAndHydrate()`: replace `sendUpdateToBox($uri, $params, 'decoded')` with `$this->getConnection()->put($uri, json_encode($params))` + `$this->handleBoxResponse($response, 'decoded')`.

Once those are migrated, `sendUpdateToBox`, `getFromBox`, `queryBox`, `putIntoBox` can be removed from the base class.

---

## 3. Public API Surface Issues (fix before v1 locks the API)

### A1 — ClientConfig::setoAuth2AuthCode() — typo in method name
`src/ClientConfig.php:72`

```php
public function setoAuth2AuthCode(?string $oAuth2AuthCode): void
```

Lowercase `o` after `set`. All other setters use `setOAuth2*`. Should be `setOAuth2AuthCode()`.
Used in `BoxClientFactory::createClient()` line 34. Fix is a rename — breaking if any external caller uses the old name, fine pre-v1.

### A2 — Connection::connect() — unimplemented method on interface
`src/Connection/Connection.php:110`, `src/Connection/ConnectionInterface.php:46`

`connect()` throws `BoxException('method not yet implemented')`. Declared on `ConnectionInterface`.
Any caller gets a runtime exception. Dead method on a public interface.

**Recommended fix:** Remove from both interface and concrete class.

### A3 — ConnectionInterface::postFile() return type inconsistency
`src/Connection/ConnectionInterface.php:97`

```php
public function postFile(...): array|BoxResponseInterface;
```

`Connection::postFile()` always returns `BoxResponseInterface`. The `array` union in the interface is a leftover that widens the contract unnecessarily. Fix: narrow to `BoxResponseInterface`.

---

## 4. Deprecated / Legacy Code (addressed in ServiceInterface cleanup)

These are all part of the approved ServiceInterface cleanup slice.

| ID | Location | Issue |
|:---|:---|:---|
| L1 | `ServiceInterface` + `Service` | `queryBox`, `putIntoBox`, `getFromBox`, `sendUpdateToBox`, `handleBoxResponse`, `TOKEN_URI`, `REVOKE_URI` — internal plumbing on the public interface. |
| L2 | `Service::handleResponseContent()` | Marked `@deprecated v0.11.0` but still called by `handleBoxResponse()`. Cannot be removed without updating the caller. The deprecated annotation is misleading — either remove the annotation or migrate `handleBoxResponse` to inline the content-handling logic. |
| L3 | `Connection::post()` — deprecated `$nameValuePair` path | `$nameValuePair=true` triggers `@trigger_error(E_USER_DEPRECATED)`. Call sites: `FolderService::createFolder()` line 81, `FolderService::copyFolder()` line 191. Fix: pass `json_encode($params)` directly and drop the third arg. |
| L4 | `Connection::put()` — deprecated array-params path | Passing an array triggers `@trigger_error(E_USER_DEPRECATED)` and URL-encodes. After N1 is fixed this path should have no call sites and can be removed. |
| L5 | `AuthenticatedServiceInterface extends ServiceInterface` | Inherits all legacy ServiceInterface methods. Must be updated when ServiceInterface is cleaned. |

Concrete services still calling legacy methods (all migrate in ServiceInterface cleanup):

| Service | Legacy call |
|:---|:---|
| `FolderService::getFolderItems()` | `queryBox()` |
| `FolderService::getFolderCollaborations()` | `queryBox()` |
| `CollaborationService::getFolderCollaborations()` | `queryBox()` |
| `GroupService::listGroups()` | `queryBox()` |
| `UserService::listUsers()` | `queryBox()` |
| `SearchService::search()` | `queryBox()` |
| `UserEventService::getEvents()` | `getFromBox()` |

---

## 5. Dead Code

### D1 — FileService: untyped dead properties (and standing rule for all resource services)
`src/Service/File/FileService.php:50-51`

```php
protected $sharedLink;
protected $access;
```

Never read or written anywhere in the class or its interface.

**Standing rule:** Before removing any apparently unused property from a resource service, verify against the Box API v2 docs whether the feature is planned but not yet implemented. If it is relevant (e.g., `$sharedLink` on FileService could support future shared-link management operations), add a `// TODO: post-v1 — see Box API docs: <feature>` comment and note it in this doc. Only remove if the Box API docs confirm the property has no planned use or the feature is already fully covered elsewhere.

**D1 split into two sessions:**
- **Session A (light):** Check Box API v2 docs for `FileService::$sharedLink` and `$access` specifically. Resolve and close those two properties.
- **Session B (thorough):** Full Box API v2 field coverage audit across all resource services (File, Folder, Group, User, Collaboration, Event). Produces a gap tracking doc as a Step 18 deliverable.

### D2 — Connection: dead auth-flow state
`src/Connection/Connection.php`

- `$responseType = "code"` (mixed, never set by SDK callers)
- `$state = null` (mixed, never set by SDK callers)
- `$requestType = "GET"` (never set externally; `getRequestType()` never called)
- `$authenticationResponse` (mixed, getter/setter exist, never populated by the SDK)

Relics of an old OAuth flow where Connection managed auth state. All getters/setters and the properties can be removed.

### D3 — Connection: dead transport selection machinery
`src/Connection/Connection.php:65`

```php
public const TRANSPORT_GUZZLE = 'guzzle';
```

`$transportName` is always `TRANSPORT_GUZZLE`. The `if (self::TRANSPORT_GUZZLE === $this->getTransportName())` branch is always true. The `--transport` CLI option was removed in a prior slice. `setTransportName()` / `getTransportName()` have no useful external callers. The constant, the property, and the selection logic can be removed; `getGuzzleOptions()` merging can be done unconditionally.

### D4 — Client: orphaned mutable caches ($files, $collaborations)
`src/Client.php:83-87`

`Client::$files` has `setFiles()`/`getFiles()` but is never auto-populated. Same for `$collaborations` / `setCollaborations()`. Leftover scaffolding. Can be removed or explicitly documented as user-managed caches — either way, needs a decision before v1 API lock.

---

## 6. Minor Quality Issues

### Q1 — Service::error() duplicates BoxLoggerTrait::error() ✓ RESOLVED
`src/Service/Service.php`, `src/Trait/BoxLoggerTrait.php`

`Service::error()` removed. `BoxLoggerTrait::error()` upgraded to the richer implementation (sets `error`, `errorDescription`, `status`, `boxCode`; iterates all data keys for `addContext()`; logs with full context). Both `Client` and all `Service` subclasses now share one implementation via the trait.

### Q1b — BoxLoggerTrait contains dead methods (v1 fix)
`src/Trait/BoxLoggerTrait.php`

`BoxLoggerTrait` was a legacy artifact from the old `Model` god class. After Q1 unification it now has two dead methods:

- **`parseResponse()`** — `Client` overrides it with its own simpler version; `Service` never calls it (uses `handleBoxResponse()` instead); `Connection` never calls it. Confirmed dead.
- **`debug()`** — trivial null-guard (`if logger instanceof LoggerInterface { logger->debug() }`). PHP 8.4 nullsafe `$this->logger?->debug()` makes it redundant at every call site.

The trait name is also misleading (it's really about Box API error construction, not logging). Rename is a pre-v1 API surface fix since the trait is public.

**Plan:**
- Remove `parseResponse()` from the trait. Audit call sites — expect zero.
- Remove `debug()` from the trait. Replace any callers with `$this->logger?->debug()`.
- Rename `BoxLoggerTrait` → `BoxApiErrorTrait`; update all three users (`Client`, `Service`, `Connection`).
- Keep `error()`, `getRedactor()`, `setRedactor()`.

### Q2 — CollaborationService::getFolderCollaborations() hardcodes folders URL inline
`src/Service/Collaboration/CollaborationService.php:19`

```php
$uri = "https://api.box.com/2.0/folders/" . $folder->getId() . "/collaborations";
```

Should reference `FolderService::ENDPOINT`. Minor — correct but fragile.

### Q3 — Client::addCollaboration() bypasses service layer
`src/Client.php:411`

Builds params and calls the raw connection directly instead of delegating to `CollaborationService::addCollaboration()`. The service method exists and does the same thing.

### Q4 — Client::query() is a thin pass-through
`src/Client.php:880`

Just calls `getConnection()->query()` + `parseResponse()`. Blurs the line between Client and Connection. Low risk but consider removing or marking `@internal`.

### Q5 — Service::$allowedReturnTypes 'array' entry is unreachable
`src/Service/Service.php:74-79`

`validateReturnType()` short-circuits with `if ('array' === $type) { return; }` before reaching `in_array`. The `'array'` entry in the array is never reached. Moot once legacy methods are removed.

---

## Summary: Slice Assignments

| Finding | Severity | Slice | Status |
|:---|:---|:---|:---|
| B1, B2, N1, A1, Q1 | Bug / API surface | Session — BoxClientFactory + FolderService fix slice | ✓ Done |
| B3, N2, L1–L5 (call site migration) | Blocker | Next session — ServiceInterface cleanup | Pending |
| Q1b (BoxLoggerTrait dead methods + rename) | API surface / dead code | ServiceInterface cleanup slice or standalone v1 slice | Pending |
| A2 (Connection::connect() removal) | API surface | ServiceInterface cleanup slice | Pending |
| A3 (postFile return type) | API surface | ServiceInterface cleanup slice | Pending |
| D1-A (FileService $sharedLink / $access) | Dead code / API coverage | Session A — targeted Box API docs check | Pending |
| D1-B (full resource service field audit) | API coverage | Session B — Step 18 deliverable | Pending |
| D2, D3 (Connection dead state / transport) | Dead code | Post-ServiceInterface slice | Pending |
| D4 (Client orphaned caches) | Dead code / API decision | Step 18 | Pending |
| Q2–Q5 | Minor | Step 18 or post-v1 | Pending |