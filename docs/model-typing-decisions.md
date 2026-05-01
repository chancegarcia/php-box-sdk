### Model Typing Decisions (Ambiguous Cases)

**Status**: Historical Reference. Decisions implemented as of v0.11.0. Final v1.0 typing strategy is in [v1.0 Planning](v1-planning.md).

This document lists classes, properties, and setters where the correct type could not be confidently determined during the v0.11.0 type-safety audit.

| Class | Property | Current Type | Recommended Type | Reasoning | Maintainer Decision | Status |
|---|---|---|---|---|---|---|
| `Box\File\File` | `pathCollection` | `mixed` | `Box\DTO\Collection\PathCollection\|array\|null` | Represents a collection of parent folders. | for v1, we want a specific DTO; for v0.11, accept both DTO or array with @todo | Implemented (Transition) |
| `Box\File\File` | `createdBy` | `mixed` | `Box\User\User\|array\|null` | Usually a mini-user object. | v1 will be object only; v0.11 accept both class and array | Implemented (Transition) |
| `Box\File\File` | `modifiedBy` | `mixed` | `Box\User\User\|array\|null` | Usually a mini-user object. | v1 will be object only; v0.11 accept both class and array | Implemented (Transition) |
| `Box\File\File` | `ownedBy` | `mixed` | `Box\User\User\|array\|null` | Usually a mini-user object. | v1 will be object only; v0.11 accept both class and array | Implemented (Transition) |
| `Box\File\File` | `parent` | `mixed` | `Box\Folder\Folder\|array\|null` | Usually a mini-folder object. | v1 will be object only; v0.11 accept both class and array | Implemented (Transition) |
| `Box\File\File` | `permissions` | `mixed` | `array\|null` | Complex nested permissions. | TBD - keep as mixed/array for now. | Deferred |
| `Box\File\File` | `sharedLink` | `mixed` | `Box\Item\SharedLink\SharedLink\|array\|null` | Represents a shared link object. | v1 will be object only; v0.11 accept both class and array | Implemented (Transition) |
| `Box\User\User` | `enterprise` | `mixed` | `object\|array\|null` | Enterprise information is often a nested object. | v1 will be object only; v0.11 accept both class and array | Implemented (Transition) |

### Notes for Future Implementation
- Box API IDs are currently `string|int`. v1.0 should prefer `string`.
- Dates are currently `\DateTimeInterface|string|null`. v1.0 should prefer `\DateTimeImmutable|null`.
