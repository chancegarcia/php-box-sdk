# V1.0 Documentation Gap Inventory

This document tracks the documentation gaps for the V1.0 SDK.

| Gap | Category | Affected Docs | Why It Matters | Priority | Recommended Action | Status |
|---|---|---|---|---|---|---|
| Direct Transport Contract | v1 architecture | `v1-strategy-and-contracts.md` | Blocks planning for advanced user API | **P0** | Define detailed contract and usage patterns | In Progress |
| Response Wrapper Contract | response wrapper | `v1-strategy-and-contracts.md` | Core dependency for services and transport | **P0** | Define PSR-7-backed wrapper methods | In Progress |
| `json()` Helper Decision | response wrapper | `v1-strategy-and-contracts.md` | Ergnomics for direct transport users | **P1** | Document decision and error behavior | In Progress |
| Auth Provider / Token Storage Boundary | auth provider | `v1-strategy-and-contracts.md` | Security and core auth workflow stability | **P1** | Define responsibilities and flows | In Progress |
| JWT/S2S Feasibility Checkpoint | JWT/S2S auth | `v1-strategy-and-contracts.md` | Strategic requirement for v1.0.0 | **P1** | Document checkpoint and requirements | In Progress |
| Error Taxonomy | error handling | `v1-architecture-rules.md` | Consistent error handling across SDK | **P1** | Consolidate and refine exception hierarchy | In Progress |
| Logging and Redaction Policy | logging/redaction | `v1-architecture-rules.md` | Security and auditability | **P1** | Define PSR-3 usage and redaction rules | In Progress |
| Retry and Rate-Limit Behavior | retry/rate-limit | `v1-architecture-rules.md` | SDK reliability and performance | **P1** | Define default behavior and config | In Progress |
| Migration Documentation Requirements | migration | `v1-strategy-and-contracts.md` | Essential for v0.x -> v1.0 adoption | **P1** | List required topics for migration guide | In Progress |
| Full v1 Migration Guide | migration | N/A | High-effort user-facing doc | **P2** | Draft after core implementation stable | Not Started |
| Direct Transport Usage Guide | user-facing docs | N/A | Needed for advanced users | **P2** | Create guide after transport implementation | Not Started |
| JWT/S2S User Guide | user-facing docs | N/A | Essential for S2S users | **P2** | Create guide after JWT implementation | Not Started |
| Retry/Error/Logging User Guide | user-facing docs | N/A | Help users configure SDK reliability | **P2** | Create guide after implementation | Not Started |
| Service-specific Docs | user-facing docs | N/A | Document individual services/endpoints | **P2** | Ongoing as services are built | Not Started |
| Docs Folder Organization | docs organization | N/A | Clean up the legacy/planning mess | **P3** | Final task before v1 release | Not Started |
| Docs Index and Link Cleanup | docs organization | N/A | Ensure all docs are discoverable | **P3** | Final task before v1 release | Not Started |

## Legend

- **P0**: blocks implementation planning
- **P1**: should be closed before implementation starts
- **P2**: needed before release
- **P3**: can wait until docs organization or later
