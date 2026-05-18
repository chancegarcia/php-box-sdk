# Roadmap

## Project Vision

Build a reliable, developer-friendly PHP SDK for working with the Box API. The SDK functions as a clean boundary layer — framework-neutral, PSR-compliant, and suitable for standalone use or integration into application frameworks.

## Release History

### v0.11.0 (Functional Transition) — Complete
Bridge release from v0.10.x. Modernized for PHP 8.4, introduced recursive hydration, Doctrine Collections, `FileStream`, and PSR alignment. Legacy aliases preserved for transition.

### v1.0.0 — Released
Full architectural overhaul. No legacy baggage. Key additions:
- JWT/S2S authentication (`JwtProvider`, `JwtAuthConfig`, `box:jwt:token` CLI)
- Token storage: Filesystem, PDO, In-Memory behind `TokenStorageInterface`
- Webhook signature verification (`WebhookVerifier`)
- Resource namespace rationalization (`Box\Resource`)
- Service layer hardening (`BoxClientFactory`, `ClientServiceRegistry`, `AuthenticatedServiceInterface`)
- Guzzle-only transport; PSR-3/7/17/18 compliant
- Chunked file upload (low-level session API + client orchestrator)
- Optional PSR-14 event dispatcher

---

## Post-v1 Release Plan

### v1.1 — Core File Operations + Ergonomics

| Item | Notes |
|:---|:---|
| Upload new file version | `POST /files/{id}/content` |
| Copy file | `POST /files/{id}/copy` |
| File versions listing | `GET /files/{id}/versions` |
| Auto-pagination helper | Cross-cutting; applies to folder items, users, groups, search |
| Search advanced params | content_types, date ranges, ancestor_folder_ids, metadata filters |
| Auto-retry with Retry-After | Retry loop in transport layer; was deferred from v0.11 |
| PHP Enums for roles/statuses | Expand `SharedLinkAccess`, collaboration roles, user status |
| CLI command additions | New commands for v1.1 features |

### v1.2 — Metadata

Full Box metadata family — self-contained, worth its own release.

| Item | Notes |
|:---|:---|
| Metadata templates (CRUD) | `/metadata-templates` |
| File metadata instances (CRUD) | `/files/{id}/metadata/...` |
| Folder metadata instances (CRUD) | `/folders/{id}/metadata/...` |

### v1.3 — Webhook Management + Content Interactions

| Item | Notes |
|:---|:---|
| Webhook management CRUD | `/webhooks` — create/update/delete/list (verification already shipped) |
| Comments | `/comments`, `/files/{id}/comments` |
| Tasks + task assignments | `/tasks`, `/task-assignments` |
| Collections (starred items) | `/collections` |
| Web links | `/web-links` |
| Thumbnails | `GET /files/{id}/thumbnail/{ext}` |
| Zip downloads | `/zip-downloads` |
| Long-poll events | `OPTIONS /events` |

### v1.4 — Enterprise Admin + Symfony Bundle

JWT/S2S-powered admin operations plus the first framework integration.

| Item | Notes |
|:---|:---|
| Symfony bundle | Service definitions, env var bridge, config format |
| User create/update/delete | `POST/PUT/DELETE /users/{id}` |
| Email aliases + avatar | `/users/{id}/email-aliases`, `/users/{id}/avatar` |
| User memberships | `GET /users/{id}/memberships` |
| Group update + membership details | `PUT /groups/{id}`, `GET /group-memberships/{id}` |
| Group collaborations | `GET /groups/{id}/collaborations` |
| Enterprise events | Admin log stream |
| Collaboration pending invites | `GET /collaborations` |
| Collaboration allowlist | `/collaboration-whitelist-*` |

### v1.5 — Remaining Gaps and Housekeeping

| Item | Notes |
|:---|:---|
| Trash management | `GET/DELETE /files/{id}/trash`, `/folders/{id}/trash` |
| Folder locks | `/folder-locks` |
| Watermarks | `GET/PUT/DELETE` on files + folders |
| File collaborations listing | `GET /files/{id}/collaborations` |
| Sign Requests | `/sign-requests` — Box Sign |

---

## v2.0 — Extended Framework Integrations

| Item | Notes |
|:---|:---|
| Laravel service provider | Facade, config, service container wiring |
| Doctrine ORM token storage | Was explicitly deferred in v1 strategy-and-contracts |

---

## Design Principles (Carry Forward)

- **Framework-neutral core**: No framework dependencies in the core SDK. Framework integrations live in separate packages.
- **PSR-compliant**: PSR-3 (logging), PSR-7/17/18 (HTTP), PSR-14 (events). Optional injection pattern for all.
- **Service-first**: Business operations live in focused services; `Client` is a thin facade.
- **No legacy baggage**: Each major version is a clean cut. Transition helpers go in a dedicated minor release if needed.
- **CLI as verification harness**: Commands stay thin. New commands only when they meaningfully verify SDK behavior.

---

**See also:**
- [Documentation Index](../README.md)
- [API Coverage Matrix](../audits/15.5-api-coverage-matrix.md)
- [PSR Compliance Assessment](../audits/psr-compliance-assessment.md)
