# V1.0 Interface and Model Audit

This document audits the existing interfaces and models in `src/Model` and core resource namespaces against V1.0 architecture rules.

## src/Model Audit

| Class/Interface | Current Role | V1.0 Action | Destination |
| :--- | :--- | :--- | :--- |
| `ModelInterface` | Base interface for all models. | **Remove** | N/A |
| `BoxModelInterface` | Interface for Box-specific models. | **Remove** | N/A |
| `BaseModel` | Generic property container with `__call` logic. | **Remove** | N/A |
| `Model` | Abstract base for API models. | **Remove** | N/A |
| `BoxModel` | Abstract base for Box resources. | **Remove** | N/A |
| `ModelTrait` | Shared logic for models. | **Replace** | `Box\Trait\ModelTrait` (Refactored) |
| `BoxModelTrait` | Shared logic for Box models. | **Replace** | `Box\Trait\BoxModelTrait` (Refactored) |

## Resource Interface Audit

These interfaces primarily mirror concrete models and contain endpoint constants.

| Interface | V1.0 Action | Reason |
| :--- | :--- | :--- |
| `FileInterface` | **Remove** | Mirrors `File` model; contains URI constants. |
| `FolderInterface` | **Remove** | Mirrors `Folder` model; contains URI constants. |
| `UserInterface` | **Remove** | Mirrors `User` model; contains URI constants. |
| `GroupInterface` | **Remove** | Mirrors `Group` model; contains URI constants. |
| `CollaborationInterface` | **Remove** | Mirrors `Collaboration` model; contains URI constants. |
| `EventInterface` | **Remove** | Mirrors `Event` model. |
| `EventCollectionInterface` | **Remove** | Mirrors `EventCollection` model. |
| `SharedLinkInterface` | **Remove** | Mirrors `SharedLink` model. |

## Core Resource Classification (src/Model Class/Abstract)

| Class | Current Namespace | V1.0 Destination |
| :--- | :--- | :--- |
| `File` | `Box\File` | `Box\Resource\File` |
| `Folder` | `Box\Folder` | `Box\Resource\Folder` |
| `User` | `Box\User` | `Box\Resource\User` |
| `Group` | `Box\Group` | `Box\Resource\Group` |
| `Collaboration` | `Box\Collaboration` | `Box\Resource\Collaboration` |
| `Event` | `Box\Event` | `Box\Resource\Event` |
| `EventCollection` | `Box\Event\Collection` | `Box\Resource\EventCollection` |

## God-Abstraction Findings

1.  **Endpoint Constants**: Found in `FileInterface`, `FolderInterface`, `UserInterface`, `GroupInterface`, `CollaborationInterface`. These must be moved to `Box\Service` classes or internal URI maps.
2.  **Generic Behavior**: `BaseModel` uses `__call` to handle dynamic properties, which is anti-type-safety and must be replaced with explicit typed properties in `Box\Resource` classes.
3.  **URL Building**: `GroupInterface::getMembershipListUri()` exists in the interface. URL building must move to `Box\Service`.
4.  **Hydration**: Currently handled by `Hydrator` and `ModelMapper`, which are used by `ModelTrait`. This must be decoupled so `Box\Resource` classes are passive.
