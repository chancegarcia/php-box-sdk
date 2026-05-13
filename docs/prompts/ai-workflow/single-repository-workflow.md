# Single Repository AI Workflow

## Purpose

This workflow is designed for AI-assisted, slice-based software development within a single repository where planning, implementation, tests, documentation, and release work are co-located.

## Core Principles

- **Local Guidelines Override**: Project-specific guidelines (e.g., `.junie/guidelines.md`) always override generic prompt templates.
- **Inspect Before Editing**: Always examine existing code, tests, and documentation before proposing or making changes.
- **Preserve Public API**: Do not change public signatures or behavior unless the task explicitly requires a breaking change.
- **Planned Cutovers**: For major-version transitions, breaking removals are permitted when explicitly planned, tested, and documented.
- **Test Preservation During V1 Modernization**: Existing pre-v1 tests are not automatically authoritative. During v1 refactors, tests should be classified (true behavior contract, characterization test, legacy shim test, implementation-coupled test, or stale/incorrect test). If a test preserves intentionally removed legacy behavior, it should be updated or removed as part of the slice.
- **Separate Planning from Implementation**: Use an initial planning phase to define the approach before writing code.
- **Atomic Slices**: Break large tasks into small, manageable implementation slices.
- **Review and Commit**: Review every slice's output and commit completed work before starting the next slice.
- **Avoid Scope Creep**: Stay focused on the current slice's goals. Document unrelated improvements as follow-ups.
- **Canonical Validation**: Use the repository's native validation commands (e.g., Composer scripts, Makefile targets).
- **No Junk Commits**: Do not commit generated summaries, logs, temp files, caches, or `vendor/` changes.
- **Periodic Handoffs**: Produce a handoff summary every 2–3 slices or before ending a session to ensure work can be resumed if context is lost.
- **Zero Secret Exposure**: Never include real credentials, tokens, or private data in code, tests, or summaries.

## Recommended Setup

- **AI Guidelines**: A central file (e.g., `.junie/guidelines.md`) defining project-wide rules.
- **Planning Folder**: `docs/planning/` for initiative trackers and design docs.
- **Prompts Folder**: `docs/prompts/` for reusable prompt templates and task-specific drafts.
- **Temp Summary File**: `var/tmp/last-task-summary.md` for persistent, non-committed task reporting.
- **Gitignore Preservation**: Ensure `.gitignore` ignores `var/tmp/*` but preserves the directory via `var/tmp/.gitkeep`.
- **Validation Conventions**: Standardized scripts for testing, linting, and static analysis.
- **Changelog Process**: A structured prompt/format for updating `CHANGELOG.md`.

## Persistent Task Summary Workflow

After completing a task or slice, the AI assistant writes a final summary by replacing the full contents of `docs/ai/current-task-summary.md` (or a project-specific path like `var/tmp/last-task-summary.md`).

- **Frequency**: Overwrite the file after every task unless explicitly skipped by the user or local guidelines. Do not use create-only behavior; an existing file must be overwritten.
- **Canonical Source**: The persisted task summary should be the canonical detailed review summary for a task.
- **Persistence by Default**: Repository-local planning, audit, tracker, and handoff outputs should normally be persisted to documentation files unless explicitly requested as chat-only.
- **Consistency**: The persisted task summary should match the final response summary as closely as practical.
    - **Encoding**: Persisted summaries must be plain UTF-8 Markdown text. They must not contain null bytes, control characters, corrupted class names, or binary content. If a generated summary contains corrupted text, rewrite it before reporting completion.
    - **Detail**: If the persisted summary includes additional detail, it must not contradict the final response.
- **Final Response**: The final response may be concise, but it should mention that the detailed summary was written to the file.
- **Structure**:
    - **Summary**: High-level overview of the result.
    - **Changes**: Bulleted list of modified files and logic.
    - **Verification**: Evidence of testing and validation.
    - **Notes**: Important details for the reviewer.
    - **Follow-ups**: Identified risks or deferred tasks.
- **Privacy**: Redact all secrets, credentials, tokens, private account IDs, and sensitive local paths.
- **Usage**: The summary is used to provide feedback in the review chat and is not committed to the repository.

## Periodic Handoff Summary Workflow

During long-running initiatives or before ending a session, the AI produces a concise handoff summary to preserve context.

- **Frequency**:
    - Every 2–3 completed slices.
    - Before ending a long AI session.
    - Before switching to a new major initiative.
    - Whenever task state, roadmap state, future-agent context, or actionable deferred follow-ups change.
    - Whenever the user asks for one.
- **Content**: Use the [Handoff Summary Template](handoff-summary-template.md). Include project name, current goal, completed/pending slices, active decisions, constraints, validation rules, and "gotchas".
- **New-Chat Startup Guidance**: When a session is ending or the user is starting a new chat, provide a concise startup package (see template) including recommended context attachments, files to open first, a suggested opening prompt, and first verification steps for the next assistant.
- **Actionable Follow-ups**: Actionable items discovered during review (e.g., from human-reviewer notes) should be refined and persisted into the handoff summary or relevant planning docs.
- **Storage**:
    - Paste directly into the chat.
    - Write to `docs/ai/current-handoff-summary.md` (or a project-specific path like `var/tmp/ai-handoff-summary.md`), overwriting any previous handoff.
- **No Commit**: Generated handoff files in temporary directories must not be committed. Accidental commits in documentation folders are acceptable if redacted.

## Slice Workflow

1. **Tracker/Plan Review**: For every new step, segment, roadmap item, or initiative, begin with a tracker/plan review and refinement pass before implementation. This is required even for low-risk work. Confirm scope, slice order, dependencies, non-goals, validation expectations, and draft prompts are current before running the first implementation slice. Implementation should not begin until the current tracker/plan has been reviewed and accepted by the human reviewer.
2. **Tracker Creation**: Create or update an initiative tracker in `docs/planning/`.
3. **Draft Prompts**: Treat tracker-embedded prompts as drafts.
4. **Prompt Refinement**: Refine the next slice's prompt immediately before execution.
5. **Single Execution**: Run exactly one slice at a time.
6. **Human Review**: Review the AI's implementation and validation output.
7. **Cleanup/Refinement**: Run follow-up prompts if the slice requires minor fixes.
8. **Commit**: Commit the completed, verified slice.
9. **Progression**: Move to the next slice only after the previous one is committed.
10. **Step Transition Approval**: Completing a step and updating its documentation does not constitute approval to begin the next step. The human reviewer must explicitly confirm before any next step or slice begins. AI assistants must surface this requirement at each step close — specifically, the handoff summary must show `Next Step Status: Pending Approval` and the AI must not proceed until the reviewer changes it to `Approved`. This rule applies equally to Junie and any other AI assistant working in this repository.

## Tracker Structure Template

```markdown
# [Title]

## Roadmap Reference
- [Link to roadmap or issue]

## Purpose
[Brief description of why this is being done]

## Scope
- [In-scope items]

## Non-Goals
- [Items explicitly excluded]

## Constraints
- [Backward compatibility, dependency limits, etc.]

## Status Table
| Slice | Description | Status |
| :--- | :--- | :--- |
| 1 | [Description] | [Todo/Done] |

## Slices
### Slice 1: [Name]
- **Goal**: [Specific outcome]
- **Draft Prompt**: [Refinable prompt template]

## Acceptance Criteria
- [Required functionality]

## Validation
- [Required commands, e.g., `composer test`]

## Deferred Follow-ups
- [Items for later]
```

## Pre-Implementation Architecture Preparation

Before starting implementation for major features or refactors, use a planning phase to produce:

- **Glossary**: Shared domain terminology.
- **Architecture Decisions**: Documentation of key choices (ADRs).
- **Contracts**: Interfaces and abstract boundaries.
- **Data Structures**: Value objects, DTOs, and domain models.
- **Boundary Logic**: Mapper/hydrator definitions.
- **Policy Definition**: Explicit rules for compatibility, deprecation, and removal.
- **Strategy**: Outlined plans for testing and migration.

## Prompt Patterns (Drafts)

> **Note**: These patterns are **drafts**. Tailor them to the project's language, tooling, structure, and current task before use.

### Pattern: Create Tracker
"Review [issue/requirement] and create a slice-based implementation tracker in `docs/planning/`. Define the scope, non-goals, and break the work into atomic slices with draft implementation prompts."

### Pattern: Refine Slice Prompt
"Refine the draft prompt for Slice [N] of the [Tracker Name]. Incorporate current repository state and any findings from previous slices. Ensure it includes specific validation commands."

### Pattern: Review Completed Slice
"Review the implementation of Slice [N]. Check against acceptance criteria, verify that all validation commands passed, and ensure no public API regressions were introduced."

## Final Documentation Status Reconciliation

Before the final response of an implementation slice, reconcile all planning and handoff documentation so they reflect the completed state of the current slice.

The AI should update, as applicable:
- Roadmap/tracker docs (e.g., `docs/planning/v1-release-roadmap.md`)
- Audit docs (e.g., `docs/audits/13-auth-lifecycle-provider-extraction-audit.md`)
- Current task summary (`docs/ai/current-task-summary.md`)
- Current handoff summary (`docs/ai/current-handoff-summary.md`)

The documentation must consistently state:
- The completed slice is marked complete.
- The next slice is identified as next/current.
- The parent step or initiative status is accurate.
- Strategic status points to the next slice.
- No contradictory status entries remain across roadmap, audit, handoff, and task summary.
- Validation counts in task summary and handoff match the latest validation run.
- Deprecated “will remove in v1” notices are not acceptable during v1 cleanup if the current work is the planned removal.

The final response should include a brief documentation/status reconciliation item.

## Workflow Guardrails: Crash and Reviewer Recovery

If an AI session crashes, loses context, or is restarted due to reviewer follow-up:
- **Working Tree Verification**: The assistant must first inspect the current working tree status and diffs to confirm the actual implementation state.
- **Status Document Reconciliation**: The assistant must re-verify roadmap, audit, and summary files against the actual files present in the repository before continuing.
- **Handoff Accuracy**: Handoff must always reflect the actual latest completed slice and next slice.

## Validation

Use the repository's canonical validation commands. Do not substitute direct tool binaries if the project defines wrapper scripts.

*Example (PHP/Composer):*
- Use `composer test`, not `vendor/bin/phpunit`.
- Use `composer lint`, not `vendor/bin/phpcs`.

## Security

- **No Secrets**: Never use real credentials in prompts or code. Use placeholders like `YOUR_TOKEN`.
- **Sanitization**: Redact summaries before sharing them outside the secure environment.
- **Inspection**: Manually inspect all AI-generated files before committing.
