# v1.0 Planning

This document tracks detailed architectural decisions, typing rules, and implementation goals for the v1.0 release of the Box PHP SDK.

## Core Architectural Goals

- **Client as Facade**: `Client` will become a lightweight facade over focused services (e.g., `FileService`, `UserService`).
- **No Legacy Baggage**: Remove all legacy aliases and deprecated namespaces (notably `Box\Model\*`).
- **Dependency Injection**: Replace mutable class-string configuration (setters like `setConnectionClass`) with standard constructor injection or dedicated factory patterns.
- **Service Consistency**: Services will return mapped model objects or typed DTOs consistently.
- **Clean Connection**: Decouple `Connection` from `Model` inheritance; make it a raw request/response layer.
- **PSR Compliance**: Achieve full PSR-12 compliance and align with PSR-7, PSR-17, and PSR-18 for HTTP interoperability.

## Typing and Data Standards

### Box IDs
- **Strict Typing**: Transition to strict `string` typing for all Box IDs to align with Box's API standards and reduce ambiguity. Even if IDs look like numbers, they are identifiers.

### Date and Time
- **Immutability**: Normalize all date fields to `\DateTimeImmutable`.
- **Strictness**: Getters and setters should use `\DateTimeImmutable` exclusively (no raw strings in v1).

### Enums and Constants
- **Native Enums**: Use PHP 8.4 Enums for fields with fixed value sets (e.g., `status`, `role`, `type`, `item_status`).

### Collections
- **Doctrine Collections**: Full migration to Doctrine Collections for all resource sets. Custom collection classes will be removed.

## Model Specific Transitions

- **Nested Resources**: Enforce object-only types for nested fields (e.g., `createdBy`, `parent`). Remove the transition-layer array support introduced in v0.11.0.
- **Path Collection**: Standardize on `PathCollection` DTO for all parent folder hierarchies.
- **Permissions**: Refactor from raw arrays to a typed `Permissions` DTO.

## API Coverage Expansion

### Shipped in v1.0.0
Core CRUD and primary operations for all initially scoped resource families:
- **FileService**: upload, get, update, delete, download, shared link
- **FolderService**: get, create, update, delete, list items, copy, collaborations, shared link, resolve shared link
- **UserService**: get current user, get by ID, list enterprise users
- **GroupService**: list groups, create, get, delete, list members, add/remove members
- **CollaborationService**: add (folders and files), get, update, delete
- **UserEventService**: get event stream
- **SearchService**: keyword search

### Deferred to v1.x
All of the following were listed as v1.0 priorities but were formally deferred:
- **File Versions** — version history management
- **Collections** — starred items / favorites
- **Comments** — file commenting
- **Tasks** — task and review workflows
- **Metadata** — structured metadata templates and instances
- **Webhooks** (CRUD) — webhook management (signature verification shipped in v1.0)
- **Sign Requests** — Box Sign integration

See [API Coverage Matrix](../../audits/api-coverage-matrix.md) for the full endpoint-level breakdown.

---
**Status:** v1.0.0 released. This document is a historical planning reference; the goals listed above are complete or formally deferred.

**Historical Context:**
- [Model Namespace Migration Decisions](../../migration/model-namespace-migration-decisions.md)
- [PSR Compliance Assessment](../../audits/psr-compliance-assessment.md)
- Model Signature Audit — archived to `docs/archive/planning/`
- Model Typing Decisions — archived to `docs/archive/planning/`
