### Model Namespace Migration Decisions

**Status**: Historical / Reference only. Active decisions are consolidated in [v1.0 Planning](v1-planning.md).

This document lists classes currently under `Box\Model` where the long-term namespace/location is ambiguous or where movement is deferred.

| Current Class | Current Role | Recommended v1 Destination | Migration Strategy | Compatibility Concern | Recommended Action for v0.11.0 | Maintainer Decision | Status |
|---|---|---|---|---|---|---|---|
| `Box\Model\Mapper\ModelMapper` | Hydration Infrastructure | `Box\Mapper\ModelMapper` | Move | New in v0.11.0. | Move to `Box\Mapper`. | Newly created so was safely moved and no need for alias. | Completed |
| `Box\Model\Mapper\Hydrator` | Hydration Infrastructure | `Box\Mapper\Hydrator` | Move | New in v0.11.0. | Move to `Box\Mapper`. | Newly created so was safely moved and no need for alias. | Completed |
| `Box\Model\ModelTrait` | Base functionality | `Box\Trait\ModelTrait` | Refactor/Move | Used by many models. | Keep in `Box\Model` for now, refactor methods. | Refactor methods to helpers/traits; preserve compatibility in v0.11. | In Progress |
| `Box\Model\BoxModelInterface` | Base interface | `Box\Contract\BoxModelInterface` | Move | Core interface. | Keep in `Box\Model` for now. | Maintain backward compatibility for v0.11. | In Progress |
| `Box\Model\BaseModel` | Base class | `Box\Base\BaseModel` | Move | Core base class. | Keep in `Box\Model` for now. | Maintain backward compatibility for v0.11. | In Progress |

### Notes for Future Implementation
- Most classes have already been moved to resource-specific namespaces (e.g., `Box\File`, `Box\User`).
- Remaining classes in `Box\Model` are primarily infrastructure/base classes.
- Goal for v1.0 is to empty `Box\Model` or keep it strictly for legacy compatibility.
