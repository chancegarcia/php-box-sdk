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

## Namespace Migration (v1.0 Final)

Remaining items in `Box\Model` to be moved:

| Class | v1.0 Destination |
|---|---|
| `Box\Model\ModelTrait` | `Box\Trait\ModelTrait` |
| `Box\Model\BoxModelInterface` | `Box\Contract\BoxModelInterface` |
| `Box\Model\BaseModel` | `Box\Base\BaseModel` |

## Model Specific Transitions

- **Nested Resources**: Enforce object-only types for nested fields (e.g., `createdBy`, `parent`). Remove the transition-layer array support introduced in v0.11.0.
- **Path Collection**: Standardize on `PathCollection` DTO for all parent folder hierarchies.
- **Permissions**: Refactor from raw arrays to a typed `Permissions` DTO.

## API Coverage Expansion

Priority endpoints for v1.0:
- File Versions
- Collections
- Comments
- Tasks
- Metadata
- Webhooks
- Sign Requests

---
**Historical Context:**
- [Model Signature Audit](model-signature-audit.md)
- [Model Typing Decisions](model-typing-decisions.md)
- [Model Namespace Migration Decisions](model-namespace-migration-decisions.md)
- [PSR Compliance Assessment](psr-compliance-assessment.md)
