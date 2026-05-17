# Single Repository AI Workflow

## Purpose

For AI-assisted, task-based development within a single repository where planning,
implementation, tests, and documentation are co-located.

## Terminology

This workflow uses Jira's work item hierarchy as its standard. Use these terms
consistently in trackers, handoff files, and chat. Avoid loose synonyms
("step", "slice", "chunk") that obscure the hierarchy level being discussed.

| Term | Old Term | Description |
|:---|:---|:---|
| **Epic** | initiative, goal | An overall initiative or major goal (e.g., "Refactor to v1") |
| **Story** | step | A high-level goal within an epic, broken into tasks |
| **Task** | slice | An atomic, executable unit of work within a story |
| **Subtask** | sub-slice | A blocking item discovered during a task |
| **Bug** | bug | A defect; may be standalone or discovered during any work type |

**Subtask autonomy**: The AI may create and execute a subtask autonomously if it is
non-behavioral (no logic or scope change — e.g., a missing import, a missing file).
If the subtask introduces new logic or affects scope, stop and surface it for human
approval before proceeding.

**Subtask vs. reordering**: If a blocker affects the sequence of upcoming stories or
tasks, surface it for human review — the human decides whether to reorder, not the AI.

**Bug handling**: A trivially small bug that is clearly in-scope may be fixed as part
of the current task. Any bug that would change scope, behavior, or API surface must be
surfaced for human approval before fixing.

## Core Principles

- **Project Config is Authoritative**: The project's `CLAUDE.md` overrides all generic
  templates. Read it at the start of every session.
- **Inspect Before Editing**: Examine existing code, tests, and documentation before
  proposing or making changes.
- **Preserve Public API**: Do not change public signatures or behavior unless the task
  explicitly requires it.
- **Planned Cutovers**: For major-version transitions, breaking changes are permitted
  when explicitly planned, tested, and documented.
- **Test Classification During Refactors**: Classify tests as: behavior contract,
  characterization, legacy shim, implementation-coupled, or stale/incorrect. Tests
  that preserve intentionally removed legacy behavior should be updated or removed as
  part of the task.
- **Atomic Tasks**: Break large stories into small, manageable tasks.
- **Review and Commit**: Review every task and commit completed work before starting
  the next.
- **Avoid Scope Creep**: Stay focused on the current task. Document unrelated
  improvements as follow-ups.
- **Canonical Validation**: Use the repository's native validation commands as defined
  in `CLAUDE.md`.
- **No Junk Commits**: Do not commit generated summaries, logs, temp files, or caches.
- **Periodic Handoffs**: Produce a handoff summary every 2–3 tasks or before ending
  a session.
- **Zero Secret Exposure**: Never include real credentials, tokens, or private data in
  code, tests, or summaries.
- **Human Commits**: The AI stages changes, shows the diff, and proposes a message.
  The human runs `git commit`. This is a hard rule.
- **Documentation Drift Checks**: After every major task and whenever scope or
  planning changes, verify that trackers, handoff files, and `CLAUDE.md` are
  consistent with the actual repository state.
- **Contradiction Detection**: If a human instruction contradicts a previously approved
  decision or established constraint, stop immediately, surface the contradiction
  clearly, confirm which direction to proceed, then continue from the appropriate point.

## Recommended Setup

- **AI Config**: `CLAUDE.md` at the repo root — validation commands, architectural
  decisions, current epic/story, conventions. Keep it short and scannable; it loads
  every session.
- **Planning Folder**: `docs/planning/` for initiative trackers and design docs.
- **Handoff Folder**: `docs/ai/` for session files
  (see [Session Handoff Files](#session-handoff-files)).
  Add `docs/ai/` to `.gitignore` — AI tools that work with the local filesystem
  can read and write gitignored paths directly, so there is no need to commit
  ephemeral session files.
- **Gitkeep**: Commit a `docs/ai/.gitkeep` and ignore content with
  `docs/ai/*.md` in `.gitignore`, so the directory is preserved without committing
  handoff files.
- **Validation**: Standardized scripts for testing, linting, and static analysis —
  documented in `CLAUDE.md`.

## CLAUDE.md — The AI Config File

`CLAUDE.md` is the project's authoritative AI configuration. It overrides these
generic workflow templates and is read at the start of every session.

Typical contents:
- The canonical validation command (e.g., `make check`, `npm test`)
- Current epic and active story/task
- Architectural decisions relevant to the AI
- Naming and timestamp conventions
- Rules that override or extend these generic templates

**Keep it short** — long files are skimmed, not read. When `CLAUDE.md` starts to feel
bloated, do a compression review: strip anything already captured in persistent memory
or covered by these generic templates. The project-level file should carry only what
is project-specific and cannot be inferred from context.

## Memory System

For AI tools that support persistent cross-session memory (e.g., Claude Code's
`~/.claude/projects/<path>/memory/`):

- **Write a memory** for facts that should not need re-explaining each session:
  preferences, non-obvious constraints, validated architectural decisions, recurring
  feedback.
- **Update the handoff files** for current-session state: what was done, what's next.
- **Don't duplicate**: ephemeral session state → handoff files; persistent facts →
  memory.
- **Memory types**: user profile, project feedback, project state, references to
  external systems.
- **Compression reviews**: When global memory or `CLAUDE.md` grows unwieldy, do a
  compression pass — consolidate redundant entries, remove stale facts, and ensure
  both files remain human-readable. Natural checkpoint: the start of a new epic.

Keep memory entries tool-agnostic where possible. The goal is to capture context that
would otherwise need to be re-explained, not to encode tool-specific workflows that
already exist in these templates.

If the AI tool does not support persistent memory, rely on `CLAUDE.md` and the handoff
files alone.

## Describe-Approve-Execute

The primary interaction pattern for all implementation work:

1. **Describe**: The AI describes the plan in 3–5 bullet points in chat.
2. **Approve**: The human approves, redirects, or asks questions.
3. **Execute**: The AI executes immediately. No intermediate prompt file is written
   to disk.

The chat conversation is the authoritative record of decisions. No separate prompt
file is generated and handed off to a second agent.

If during execution the human's instructions contradict a previously approved decision,
stop and surface the contradiction before continuing (see Contradiction Detection).

## Session Handoff Files

Three files live in `docs/ai/` (gitignored) and bridge sessions:

### `docs/ai/current-handoff-summary.md`

What happened this session.

- Completed work and decisions made
- Test/validation baseline at close
- Gotchas discovered
- `Next Step Status: Pending Approval | Approved | Blocked`

The AI must not proceed to the next task until this status is changed to `Approved`
by the human.

### `docs/ai/next-session-plan.md`

Scoped, actionable plan for the next session.

- The specific task or story to execute next
- Required context files to open first
- Suggested opening message for the new session
- First verification steps
- Scope reminders (what is in and out of scope)

### `docs/ai/human-review-notes.md` (Optional)

A dedicated file for the human to prepare feedback or notes before a new session.

- Human overwrites this file with notes or multi-item feedback between sessions.
- AI checks for this file as the first step of every session startup; if present,
  reads and acknowledges the contents, then clears the file.
- For quick inline feedback during an active session, pasting into chat is fine —
  this file is for feedback prepared between sessions.

**Rules**:
- All three files are overwritten by their respective authors; they are not logs.
- They are not a general roadmap — they are a narrow bridge to the next session.
- All three files live in `docs/ai/` which is gitignored.
- Use the [Handoff Summary Template](handoff-summary-template.md).

## Documentation Drift Checks

A drift check verifies that planning docs, handoff files, and the repository are
consistent.

**Run a drift check:**
- After every major task
- Whenever scope or planning changes
- Whenever a planning document is updated
- Whenever a session is restarted or resumed from a handoff

**What to compare:**
- Tracker/epic/story status vs. what is actually committed
- `CLAUDE.md` contents vs. current project state
- Validation commands in docs vs. commands that actually work
- Handoff files vs. actual working tree state
- ADRs and glossary vs. how the codebase actually behaves

If drift is found: update the affected docs immediately, or record a follow-up story
in the relevant tracker.

## Task Workflow

1. **Tracker Review**: Before any new task, review and refine the story tracker.
   Confirm scope, task order, dependencies, non-goals, and validation expectations.
   Implementation must not begin until the tracker has been reviewed and accepted.
2. **Tracker Update**: Create or update the tracker in `docs/planning/`.
3. **Describe**: The AI describes the next task's plan in chat (3–5 bullets).
4. **Approve**: Human approves or redirects.
5. **Execute**: AI executes exactly one task.
6. **Validate**: Run the project's canonical validation command.
7. **Human Review**: Review the implementation and validation output.
8. **Cleanup**: Run follow-up passes for minor fixes if needed.
9. **Stage and Propose**: AI stages changes, shows the diff, and proposes a commit
   message. Human runs `git commit`.
10. **Drift Check**: Verify trackers, handoff files, and `CLAUDE.md` are consistent
    with the completed task.
11. **Handoff Update**: Overwrite `current-handoff-summary.md` and
    `next-session-plan.md`.
12. **Step Transition**: Completing a task does not authorize the next one. The human
    must explicitly approve. The handoff summary must show
    `Next Step Status: Pending Approval` until that approval is given.

## Tracker Structure Template

```markdown
# [Epic or Story Title]

## Roadmap Reference
- [Link to Jira epic/story or issue tracker]

## Purpose
[Why this is being done]

## Scope
- [In-scope items]

## Non-Goals
- [Items explicitly excluded]

## Constraints
- [Backward compatibility, dependency limits, etc.]

## Status Table
| Task | Description | Status |
| :--- | :--- | :--- |
| 1 | [Description] | Todo / In Progress / Done |

## Tasks
### Task 1: [Name]
- **Goal**: [Specific outcome]
- **Draft Plan**: [3–5 bullet description]

## Acceptance Criteria
- [Required functionality]

## Validation
- [Command(s) that must pass]

## Deferred Follow-ups
- [Items for later]
```

## Pre-Implementation Architecture Preparation

Before starting implementation for major epics or refactors, use a planning pass to
produce:

- **Glossary**: Shared domain terminology.
- **Architecture Decisions**: Key choices documented as Architecture Decision Records (ADRs).
- **Contracts**: Interfaces and abstract boundaries.
- **Data Structures**: Value objects, DTOs, domain models.
- **Boundary Logic**: Mapper/hydrator definitions.
- **Policy Definition**: Compatibility, deprecation, and removal rules.
- **Strategy**: Testing and migration plans.

## Plan Patterns

> These are starting-point descriptions, not rigid scripts. Adapt them to the current
> task before use.

**Initialize Project**: "Check for an existing `CLAUDE.md` at the repository root.
If absent, create one using the recommended structure from the workflow template.
If present, review it for compatibility with these workflow templates and surface any
conflicts or integration recommendations before proceeding."

**Create Tracker**: "Review [issue/requirement] and create a task-based story tracker
in `docs/planning/`. Define scope, non-goals, and break the work into atomic tasks."

**Execute Task**: "Implement Task [N] of [Story Name]. The goal is [specific outcome].
Validation must pass using [command]. Update the handoff files when done."

**Review Task**: "Review Task [N]. Check acceptance criteria, verify validation
passed, and confirm no public API regressions."

**Drift Check**: "Perform a documentation drift check. Verify that [tracker],
`CLAUDE.md`, and the handoff files reflect the current repository state."

**Compression Review**: "Review `CLAUDE.md` and global memory for stale or redundant
entries. Consolidate where possible. The goal is a file that is both complete and
scannable in under two minutes."

## Final Documentation Status Reconciliation

Before closing a task, reconcile all planning and handoff documentation:

- Tracker: mark the task complete; identify the next task as current.
- `current-handoff-summary.md`: reflect the completed state.
- `next-session-plan.md`: scoped plan for the next session.
- No contradictory status entries across docs.
- Validation counts match the latest run.

## Crash and Recovery

If a session crashes, loses context, or is restarted:

1. Check `docs/ai/human-review-notes.md` for any pending feedback from the human.
2. Inspect the working tree status and diffs to confirm actual implementation state.
3. Re-verify tracker and handoff files against actual repository files.
4. Do not rely solely on handoff summaries — treat repository state as authoritative.

## Language-Specific Guidance

The core workflow is language-agnostic. Language-specific conventions are maintained
in separate reference documents and should be copied alongside this workflow template
when adopting it in a project.

- **PHP**: [PHP Code Style Guidance](php-code-style-guidance.md) — conventions,
  static analysis, validation commands, and PHPDoc standards.

Add entries here as other language-specific guides are created.

## Validation

Use the canonical validation command defined in `CLAUDE.md`. Do not substitute
individual tool binaries if the project defines wrapper scripts.

*Example placeholder:* Use `make check` rather than running individual tool binaries
directly, unless diagnosing a specific tooling issue.

## Security

- **No Secrets**: Never use real credentials in plans, code, or summaries.
  Use placeholders like `YOUR_TOKEN` or `[CLIENT_ID]`.
- **Sanitization**: Redact summaries before sharing them outside a secure environment.
- **Inspection**: Manually inspect all AI-generated files before committing.
