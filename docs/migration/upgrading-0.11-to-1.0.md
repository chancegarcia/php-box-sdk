# Upgrading from v0.11 to v1.0

## What Changed?

v1.0 is a complete internal rebuild of the SDK. The goal was to make the codebase cleaner, safer, and easier to maintain long-term — not to change how you interact with Box.

**If you use the SDK through the `Client` class** (`$client->getFile()`, `$client->getFolder()`, `$client->uploadFileToBox()`, etc.), most of your code will continue to work with only a few updates. The main things you'll need to change are some import paths and how you catch errors.

**If you use internal SDK classes directly** — particularly the old `Box\File\File`, `Box\Folder\Folder`, or `Box\Model` classes, or methods like `mapBoxToClass()` — those have moved or been removed. This guide tells you exactly what to change.

**What's new and doesn't require any migration:**
- JWT/server-to-server authentication for enterprise and app-user integrations
- Built-in token storage (filesystem and database)
- Webhook signature verification

**The bottom line:** Most changes are find-and-replace on import paths and a handful of method renames. Set aside an hour for a typical integration; less if you only use `Client`.

---

## At a Glance: What You Must Change

Use this as a checklist. Each item links to the full section below.

| What | Effort | Details |
|:---|:---|:---|
| Resource class import paths | Low — find & replace | [Resource Namespaces](#1-resource-namespace-changes) |
| `mapBoxToClass()` calls | Low — method rename | [Model Hydration](#2-model-hydration) |
| Exception `catch` blocks | Low — class rename | [Exception Handling](#3-exception-handling) |
| `getLastResult()` calls | Low — remove, use exceptions | [Removed Debug Methods](#4-removed-debug-methods) |
| `getEvents()` result handling | Moderate — return type changed | [Event Service](#5-event-service) |
| Removed factory interfaces | Check if you implement them | [Factory Interfaces](#6-factory-interfaces) |
| Removed model base classes | Check if you extend them | [Removed Classes](#7-removed-classes-and-interfaces) |
| CLI `--transport` option | Remove from scripts | [CLI Changes](#8-cli-changes) |

---

## Breaking Changes

### 1. Resource Namespace Changes

All resource classes moved to `Box\Resource`. Update your import statements:

| Before (v0.11) | After (v1.0) |
|:---|:---|
| `Box\File\File` | `Box\Resource\File` |
| `Box\Folder\Folder` | `Box\Resource\Folder` |
| `Box\Group\Group` | `Box\Resource\Group` |
| `Box\Collaboration\Collaboration` | `Box\Resource\Collaboration` |
| `Box\Item\SharedLink\SharedLink` | `Box\Resource\SharedLink` |
| `Box\Event\Event` | `Box\Resource\Event` |
| `Box\Event\Admin\AdminEvent` | `Box\Resource\AdminEvent` |
| `Box\Event\User\UserEvent` | `Box\Resource\UserEvent` |
| `Box\User\User` | `Box\Resource\User` |

The old resource interfaces (`FileInterface`, `FolderInterface`, `GroupInterface`, etc.) have also been removed. Replace any interface type hints with the concrete resource class.

**Endpoint constants** previously on resource interfaces have moved to their service classes:

| Before | After |
|:---|:---|
| `FileInterface::URI` | `FileService::ENDPOINT` |
| `FileInterface::UPLOAD_URI` | `FileService::UPLOAD_ENDPOINT` |
| `FolderInterface::URI` | `FolderService::ENDPOINT` |
| `FolderInterface::SHARED_ITEM_URI` | `FolderService::SHARED_ITEM_ENDPOINT` |
| `GroupInterface::URI` | `GroupService::ENDPOINT` |
| `GroupInterface::MEMBERSHIP_URI` | `GroupService::MEMBERSHIP_ENDPOINT` |
| `CollaborationInterface::URI` | `CollaborationService::ENDPOINT` |
| `UserEventInterface::URI` | `UserEventService::ENDPOINT` |

---

### 2. Model Hydration

`mapBoxToClass()` has been removed. Use `Box\Mapper\Hydrator` instead:

**Before (v0.11):**
```php
use Box\Resource\User;

$user = new User();
$user->mapBoxToClass($data);
```

**After (v1.0):**
```php
use Box\Mapper\Hydrator;
use Box\Resource\User;

$user = new User();
(new Hydrator())->hydrate($user, $data);
```

In practice, services and factories already handle hydration internally when you call `$client->getFile()`, `$client->getFolder()`, etc. You only need `Hydrator` if you are manually constructing resources from raw API data.

**Also removed from resources and `ModelMapper`:** `toBoxVar()`, `toClassVar()`, `isInt()`, `removeEmpty()`, `classArray()`, `buildQuery()`.

**Use instead:** `$resource->toArray()` for serializing a resource to an array.

---

### 3. Exception Handling

The exception hierarchy is now more specific. Catching the base `BoxException` still works, but you lose access to the response details. Use the specific classes to inspect what went wrong:

**Before (v0.11):**
```php
try {
    $client->getFile($id);
} catch (\Box\Exception\BoxException $e) {
    // generic handling — no access to HTTP response
}
```

**After (v1.0):**
```php
try {
    $client->getFile($id);
} catch (\Box\Exception\ApiException $e) {
    // Box API returned an error (4xx/5xx)
    $response  = $e->getResponse();
    $errorData = $response->json();
    $boxCode   = $errorData['code'] ?? 'unknown';
} catch (\Box\Exception\TransportException $e) {
    // Network-level failure (timeout, DNS, etc.)
}
```

`BoxException` remains the base class, so existing `catch (BoxException $e)` blocks will still catch errors — they just won't have access to `getResponse()`.

**Security improvement (no action required):** Exception messages and log context now automatically redact access tokens, refresh tokens, and client secrets.

---

### 4. Removed Debug Methods

These service and client methods have been removed. They encouraged inspecting internal state rather than handling responses properly.

| Removed | Replacement |
|:---|:---|
| `getLastResult()` | Catch `ApiException` and call `$e->getResponse()->json()` |
| `getDefaultReturnType()` | Not needed — services return typed objects |
| `setDefaultReturnType()` | Not needed — services return typed objects |

Stateful properties `lastResultOriginal`, `lastResultDecoded`, and `lastResultFlat` are also gone.

---

### 5. Event Service

`UserEventService::getEvents()` now returns a typed `EventResponse` DTO instead of a raw array.

**Before (v0.11):**
```php
$events      = $eventService->getEvents(); // array|stdClass|null
$nextPos     = $eventService->getLastResult()['next_stream_position'] ?? null;
```

**After (v1.0):**
```php
use Box\Dto\Event\EventResponse;

$response = $eventService->getEvents(); // EventResponse
$events   = $response->getEntries();            // Doctrine Collection of Event objects
$nextPos  = $response->getNextStreamPosition(); // string
$size     = $response->getChunkSize();          // int
```

**Other event service changes:**
- Default `stream_position` is now `now` (Box API default) instead of `0`.
- The `type` parameter and optional `EventCollectionInterface` argument to `getEvents()` have been removed.
- Fluent/chained setters on `UserEventService` have been removed; setters now return `void`.

---

### 6. Factory Interfaces

The following "one-class mirror" factory interfaces have been removed. If you are type-hinting against these in your own code, switch to the concrete factory class:

| Removed interface | Use instead |
|:---|:---|
| `Box\Factory\FileFactoryInterface` | `Box\Factory\FileFactory` |
| `Box\Factory\FolderFactoryInterface` | `Box\Factory\FolderFactory` |
| `Box\Factory\UserFactoryInterface` | `Box\Factory\UserFactory` |
| `Box\Factory\GroupFactoryInterface` | `Box\Factory\GroupFactory` |
| `Box\Factory\CollaborationFactoryInterface` | `Box\Factory\CollaborationFactory` |

These infrastructure factory interfaces are **retained** (they remain meaningful extension points):
- `Box\Factory\ConnectionFactoryInterface`
- `Box\Factory\TokenFactoryInterface`
- `Box\Factory\AuthenticationResponseFactoryInterface`

---

### 7. Removed Classes and Interfaces

The entire legacy `Box\Model` base architecture has been removed:

- `Box\Model\BaseModel`
- `Box\Model\Model`
- `Box\Model\BoxModel`
- `Box\Model\BaseModelInterface`
- `Box\Model\ModelInterface`
- `Box\Model\BaseModelTrait`
- `Box\Model\ModelTrait`

**If your code extends any of these**, you will need to refactor. Resources in v1.0 are standalone classes — they do not extend a base model. Implement `Psr\Log\LoggerAwareInterface` directly if you need logging in a custom service.

The legacy `Box\Connection\ConnectionFactory` has also been removed. Use `Box\Factory\ConnectionFactory` (which implements `ConnectionFactoryInterface`).

---

### 8. CLI Changes

**`--transport` option removed.** Guzzle is now the only HTTP transport. Remove `--transport=guzzle` or `--transport=curl` from any scripts.

**`--storage-type memory` removed.** Use `--storage-type filesystem` (default) or `--storage-type pdo`.

See the [CLI Test Harness Guide](../user/cli-test-harness.md) for current command reference and storage options.

---

## New Features in v1.0

These are additions — no migration required.

### Client Setup (Recommended Pattern)

v1.0 introduces `BoxClientFactory` as the recommended way to build a client. It reads from environment variables and wires everything correctly:

```php
use Box\Service\BoxClientFactory;
use Box\Service\EnvConfigProvider;

$factory = new BoxClientFactory(new EnvConfigProvider());
$client  = $factory->createClient(); // OAuth2
// or
$client  = $factory->createClientForCurrentMode(); // JWT when BOX_AUTH_MODE=jwt
```

Direct construction with `new Client()` and `setClientId()`/`setClientSecret()` still works for simple cases.

### Token Storage

Persist tokens automatically without managing serialization yourself:

```php
use Box\Storage\Token\Filesystem\FilesystemTokenStorage;
use Box\Storage\Token\Pdo\TokenStorage as PdoTokenStorage;

// Filesystem (dev / single-server)
$storage = new FilesystemTokenStorage('/path/to/tokens.json');

// PDO (production / multi-server)
$pdo     = new \PDO('mysql:host=db;dbname=app', 'user', 'pass');
$storage = new PdoTokenStorage($pdo);

// Attach to client
$client->setTokenStorage($storage);
```

See [Token Storage](../migration/upgrading-0.11-to-1.0.md#token-storage) for the full reference.

### JWT / Server-to-Server Authentication

Set `BOX_AUTH_MODE=jwt` and the `BOX_JWT_*` environment variables. No browser redirect needed. See [Programmatic Usage — JWT/S2S](../user/programmatic-usage.md#jwt--server-to-server-s2s) for examples.

### Webhook Signature Verification

```php
use Box\Webhook\WebhookVerifier;

$verifier = new WebhookVerifier(primaryKey: $primaryKey, secondaryKey: $secondaryKey);

if (!$verifier->verify($body, $deliveryTimestamp, $primarySignature, $secondarySignature)) {
    // reject — invalid or replayed webhook
}
```

Enforces a 10-minute replay window. Supports primary/secondary key rotation.

---

## What Stayed the Same

- All `Client` methods for file, folder, user, group, and collaboration operations (`getFile()`, `getFolder()`, `uploadFileToBox()`, etc.) work the same way and return the same types.
- `Token`, `ClientConfig`, and connection setup are largely unchanged.
- PSR-3 logger injection: `$client->setLogger($logger)` works as before.
- OAuth2 flow: `buildAuthorizationUrl()`, `setAuthorizationCode()`, `exchangeAuthorizationCodeForToken()`, `refreshToken()`.
- `FileStream` for uploads works as before.
