# Next Session Plan

**Updated**: 2026-05-15 00:44 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Documentation cleanup is complete. Open questions Q1–Q3 are resolved. Go straight to **Slice 19, Gate 1**.

---

## Slice 19 — Chunked Upload + PSR-14 Events

### Gate 1: PSR-14 EventDispatcher Infrastructure (small, do first)

Add `psr/event-dispatcher` to `composer.json` `require`.

Wire optional dispatcher into `Client`:
```php
private ?EventDispatcherInterface $eventDispatcher = null;

public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
public function getEventDispatcher(): ?EventDispatcherInterface
```

Create event classes in `src/Event/File/`:
- `UploadSessionCreated` — holds `UploadSession`
- `UploadPartUploaded` — holds `UploadPart`, part number, total parts
- `UploadSessionCommitted` — holds resulting `File`
- `UploadSessionAborted` — holds session ID and the caught `Throwable`

### Gate 2: FileStream Additions (small)

In `src/Http/FileStream.php`:
- `getSize(): int` — `fstat($this->resource)['size']`
- `readChunk(int $length): string` — `fread($this->resource, $length)`
- `isEof(): bool` — `feof($this->resource)`

### Gate 3: DTOs (small)

`src/Dto/File/UploadSession.php` — `session_id`, `upload_url`, `part_size`, `session_expires_at`, `total_parts`, `num_parts_processed`

`src/Dto/File/UploadPart.php` — `part_id`, `offset`, `size`, `sha1`

### Gate 4: FileService Low-Level API (medium)

```
UPLOAD_SESSION_ENDPOINT = 'https://upload.box.com/api/2.0/files/upload-sessions'

createUploadSession(string|int $parentId, string $filename, int $fileSize): UploadSession
uploadPart(string $sessionId, string $data, int $offset, int $totalSize): UploadPart
listUploadSessionParts(string $sessionId): array  // returns UploadPart[], for resumability
commitUploadSession(string $sessionId, array $parts, string $fileSha1): File
abortUploadSession(string $sessionId): void
```

Key implementation notes:
- `Content-Range: bytes {offset}-{lastByte}/{totalSize}` — `$lastByte = $offset + strlen($data) - 1`
- `Digest: sha={base64_encode(sha1($data, true))}` — raw binary flag is critical
- No Connection changes needed — `request('PUT', $url, ['body' => ..., 'headers' => [...]])` works

### Gate 5: Orchestrator (medium)

```php
public function chunkedUpload(string|FileStream $file, string|int $parentId, ?int $partSize = null): File
```

Algorithm: normalize file → get size → createUploadSession → loop readChunk/uploadPart/dispatch → commitUploadSession → wrap in try/catch for abort+rethrow. Track whole-file SHA1 via `hash_init/update/final('sha1')`.

### Gate 6: Client Facade (small)

```php
public function chunkedUpload(string|FileStream $file, string|int $parentId): File
```

Pass event dispatcher to service before delegating.

### Gate 7: Tests (medium)

- DTO construction tests
- FileStream addition tests
- FileService unit tests per low-level method (mocked Connection)
- Orchestrator: happy path, abort-on-failure, event dispatch via mock dispatcher

### Gate 8: Documentation

`programmatic-usage.md` — chunked upload section (convenience method + low-level pattern for custom orchestration with events).

**Also fill these doc gaps** (identified prior session):
- Token storage wiring to client (`setTokenStorage`)
- Webhook verification in an HTTP handler context
- Folder CRUD examples (`createFolder`, `getFolderItems`)
- Search example
- Error recovery pattern (401 → refresh → retry)

**Also review `llms.txt`** — consumer-facing LLM convention file. Deferred from pre-Slice-19 cleanup; revisit here when API surface is more complete. Decision: add to repo or defer to post-v1.

---

## Resolved Questions (do not re-open)

### Q1: EnvConfigProvider framing
`EnvConfigProvider` is environment-variable-driven, not CLI-exclusive. Any app populating `$_ENV`/`$_SERVER` (Symfony DotEnv, Docker env, etc.) can use it. CLI-only vars: `BOX_UPLOAD_FILE_PATH`, `BOX_UPLOAD_FOLDER_ID`, `BOX_JSON_FORMATTER`.

`ArrayConfigProvider` (accepts a plain array): good idea, confirmed deferred to v1.1.

### Q2: Auto-Retry + Auto-Token-Refresh
Not implemented. Deferred to v1.1. Only `RateLimitException` (wired to 429) exists.

### Q3: CHANGELOG v0.11.4 / v0.11.5
Removed both entries. Work was never tagged or released; v1.0.0 entry covers it.

---

## Acceptance Criteria for Slice 19

- Slice 19 all 8 gates complete
- `composer review` green
- `programmatic-usage.md` chunked upload + doc gaps filled
- `llms.txt` decision made (add or formally defer)
- API coverage matrix updated to reflect chunked upload as ✅
