# Documentation and Planning Drift Audit - May 2026

## Executive Summary

Following the completion of **Step 11 — Factory Modernization and Service Boundaries**, a comprehensive audit was performed to identify stale, conflicting, or incomplete documentation across the `docs/` folder. While recent steps (7–11) have maintained high-quality trackers and handoff summaries, several high-level planning documents (Roadmap, Release Task Lists, v1 Overview) have significant "status drift" and "scope drift."

The project is currently in a strong architectural state, but the planning documentation requires a synchronization pass to ensure a reliable baseline for **Step 12 — Token Storage Completion** and beyond.

## Scope of Audit

- **Trackers**: Steps 7, 8, 9, 10, 11.
- **Planning**: Roadmap, Release Task Lists, v1 Overview, Implementation Checklist.
- **Architecture/Decisions**: Strategy and Contracts, Architecture Rules, Decision Index.
- **Migration/User Docs**: README, Upgrading guides, Programmatic Usage.
- **Audits**: All files in `docs/audits/`.

## Canonical Source Recommendations

When conflicts exist, the following sources should be treated as canonical:

| Topic | Canonical Source |
| :--- | :--- |
| **Current Progress** | `docs/planning/10-v1-release-work.md` |
| **Architecture Rules** | `docs/planning/v1/architecture-rules.md` |
| **Technical Strategy** | `docs/planning/v1/strategy-and-contracts.md` |
| **Decisions** | `docs/planning/v1/decision-index.md` |
| **Step Details** | Individual step trackers (e.g., `docs/planning/11-factory-service-boundary-audit.md` - *Wait, actually Step 11 details are in 10-v1-release-work.md*) |

*Correction*: Step 11 details were largely managed within `docs/planning/10-v1-release-work.md`. Step 10 has its own detailed tracker `docs/planning/10-resource-namespace-interface-rationalization.md`.

## Drift Findings

### 1. Status Drift

| Document | Finding | Risk |
| :--- | :--- | :--- |
| `docs/planning/v1/implementation-checklist.md` | Shows Step 7 as "In progress". Lists many subsequent steps as "Not started" which are actually complete (Users, Groups, Files, Folders, Collaborations, Events). | **High** - Misleading for anyone checking high-level status. |
| `docs/planning/v1/overview.md` | Lists "Remaining items in Box\Model" (ModelTrait, BoxModelInterface, BaseModel) which were removed in Step 9. | **Medium** - Stale technical tasks. |
| `docs/planning/roadmap.md` | Lists "v0.11.0" as "Current Focus / Transition Release" but the project is deep into v1.0 implementation. | **Low** - Contextual drift. |
| `docs/planning/release-task-lists.md` | v1.0 tasks like "standardize IDs as string" and "standardize dates as DateTimeImmutable" are listed as pending but were implemented in Steps 10/11. | **Medium** - Duplicate tracking drift. |
| `docs/README.md` | Status section says Step 10 is "In Progress". It is actually complete. | **Medium** - Entry-point drift. |

### 2. Decision and Scope Drift

| Document | Finding | Risk |
| :--- | :--- | :--- |
| `docs/planning/v1/strategy-and-contracts.md` | Mentions "Feasibility checkpoint after foundation" for JWT/S2S. This checkpoint is now explicitly Step 14 in the main tracker. | **Low** - Minor phrasing drift. |
| `docs/planning/v1/architecture-rules.md` | Rule 7 on Collections says "V1.0 should use Doctrine Collections selectively, not universally." This is correct but "Selectively" is subjective; current implementation uses them for almost all list responses. | **Low** - Guidance drift. |
| `docs/planning/v1/api-coverage-audit.md` | Lists Sign Requests and Webhooks as "High priority for V1.0". The current tracker (Step 16/17) has Webhooks but Sign Requests are often deferred. | **Medium** - Priority drift. |

### 3. Migration Drift

| Document | Finding | Risk |
| :--- | :--- | :--- |
| `docs/migration/upgrading-0.11-to-1.0.md` | Generally very accurate. However, it lacks a clear section on the "Token Storage" changes that are about to happen in Step 12. | **Low** - Expected omission (pre-implementation). |

## Status Mismatch Table (Steps 7–11)

| Step | Title | Actual Status | Recorded Status (Main Tracker) | Recorded Status (Other Docs) |
| :--- | :--- | :--- | :--- | :--- |
| 7 | Foundation Refinement | ✓ | ✓ | In Progress (Checklist) |
| 8 | Service Layer Hardening | ✓ | ✓ | Not Started (Checklist) |
| 9 | Legacy Architecture Removal | ✓ | ✓ | Not Started (Checklist) |
| 10 | Resource Namespace Rationalization | ✓ | ✓ | In Progress (Checklist, README) |
| 11 | Factory Modernization | ✓ | ✓ | Not Started (Checklist) |

## Deferred Follow-Up Inventory

Collected from Step 11 completion and audits:

| Item | Origin | Target | Status |
| :--- | :--- | :--- | :--- |
| Event Resource Modernization | Step 11.9 | Step 13 or Post-v1 | `AdminEvent` and `Event` still use legacy patterns. |
| Resource Property Type Narrowing | Step 11.9 | Post-v1 | Move from `mixed` to specific types where safe. |
| Legacy Client State/Cache Methods | Step 11.9 | Step 17 | `setFolders`, `getCollaborations` etc. remain for v0.11 compatibility. |
| Filesystem Token Storage | Step 12 | Step 12 | Feasibility evaluation required. |
| Doctrine ORM / Symfony Storage | Step 12 | Post-v1 | Explicitly deferred to framework integration layer. |

## Recommended Cleanup Slices

### Slice A: Planning Document Synchronization [✓]
- Update `docs/planning/v1/implementation-checklist.md` to match `10-v1-release-work.md`. [✓]
- Update `docs/roadmap.md` and `docs/planning/release-task-lists.md` to reflect completed v1 work. [✓]
- Update `docs/README.md` status section. [✓]
- Sync `docs/planning/v1/overview.md` (remove completed "Remaining items"). [✓]
- Update root `README.md` status note. [✓]

### Slice B: Canonical Source and Decision Alignment
- Refine `docs/planning/v1/strategy-and-contracts.md` and `docs/planning/v1/decision-index.md` to ensure they point to the main v1 tracker for step-specific details.
- Clarify "Selective" Doctrine Collection usage in `architecture-rules.md` based on Step 10/11 outcomes.

### Slice C: Migration and User Documentation Polish
- Ensure `docs/migration/upgrading-0.11-to-1.0.md` has a placeholder for Step 12 changes.
- Verify `docs/user/programmatic-usage.md` examples align with the finalized Step 11 service delegation patterns (e.g., using `$client->folders()` if applicable).

## Risks if not cleaned up
- **Onboarding Risk**: New contributors (or AI agents in new sessions) will be confused by conflicting "In Progress" states.
- **Implementation Risk**: Step 12 implementation might miss constraints defined in stale versions of strategy docs.
- **Maintenance Risk**: Stale "Priority" lists might lead to scope creep or out-of-order work.

## Next Prompt Recommendation

**Slice A: Planning Document Synchronization**
Focus on updating the high-level trackers and roadmap files to accurately reflect the 100% completion of Steps 7 through 11.

---

**Audit Performed By**: Junie (AI Assistant)
**Date**: 2026-05-11
**Status**: Step 12 can proceed **AFTER** Slice A cleanup.
