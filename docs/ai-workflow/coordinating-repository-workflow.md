# Coordinating Repository AI Workflow

## Purpose

For a central **Coordinating Repository** that manages the roadmap, planning,
architecture decisions, and integration status across multiple downstream
**Implementation Repositories**.

## Roles

- **Coordinating Repository**: Owns the roadmap, cross-repo terminology, architecture
  decisions (ADRs), global glossary, and dependency sequencing.
- **Implementation Repository**: Owns source code, tests, and repository-specific
  validation for a single component or library.
- **Coordinator AI**: Analyzes cross-repo requirements and prepares implementation
  plans.
- **Implementation AI**: Executes plans within a specific implementation repository.
- **Human Reviewer**: Approves cross-repo plans and validates individual repository
  changes.

## Core Principles

- **Coordinator Ownership**: The coordinating repo is the source of truth for "What"
  and "Why".
- **Implementation Autonomy**: Individual repos own "How", adhering to their own local
  `CLAUDE.md` and tooling.
- **Data Privacy**: Do not leak private downstream details into open-source
  implementation repos.
- **Sanitized Handoff**: Plans passed to implementation repos must be scoped,
  sanitized, and contextualized for that specific repo.
- **Dependency Tracking**: Cross-repo work must follow a planned sequence to avoid
  integration breakage.
- **Drafts and Refinement**: Plan templates are drafts and must be human-reviewed
  before execution.
- **Documentation Drift Checks**: After every major task and whenever scope or
  planning changes, verify that cross-repo trackers, ADRs, and the glossary are
  consistent with actual implementation state.

## Recommended Coordinating Repo Structure

```
docs/
  roadmap/          # High-level milestones and initiatives
  architecture/     # ADRs and global design patterns
  glossary/         # Unified domain terminology
  planning/         # Per-initiative trackers and status
    drafts/         # Pending implementation plans awaiting review (gitignored)
  status/           # Global integration and release status
  decisions/        # Log of major project decisions
  ai/               # Session handoff files (gitignored)
```

`docs/planning/drafts/` and `docs/ai/` should both be gitignored. Draft plans are
ephemeral — they are either approved and folded into the tracker, or discarded.

## Cross-Repo Tracker Workflow

The coordinator maintains a tracker for each major initiative:

- **Review and Refine**: Begin with a tracker review before implementation. Confirm
  scope, task order, dependencies, non-goals, and validation expectations.
  Implementation must not begin until the tracker has been reviewed and accepted.
- **Initiative**: Unified goal across repos.
- **Affected Repos**: List of implementation repos involved.
- **Dependency Map**: Which repo must be updated before others.
- **Per-Repo Status**: Tracks task progress in each repository.
- **Implementation Plans**: Draft plans generated for each repository.
- **Integration Risks**: Known points of friction between repositories.
- **Release Sequencing**: The order in which repositories must be tagged and released.

## Plan Review Workflow (Coordinator → Implementation)

When the Coordinator AI prepares an implementation plan for a downstream repo:

1. **Draft**: AI writes the detailed plan as a Markdown file to
   `docs/planning/drafts/[repo-name]-[task-name].md`.
2. **Summary**: AI gives a concise chat summary — key steps, scope, risks, validation
   expectations.
3. **Human Review**: Human reviews the Markdown document in the IDE.
4. **Revise or Approve**: Human requests changes or approves the plan.
5. **Execute**: Approved plan is run in the implementation repository using the
   [Describe-Approve-Execute](single-repository-workflow.md#describe-approve-execute)
   pattern.
6. **Cleanup**: Draft file is deleted after execution. It does not become a permanent
   artifact.

## Implementation Handoff Workflow

1. **Coordinator Preparation**: Coordinator AI prepares a plan for a specific
   implementation repo (see [Plan Review Workflow](#plan-review-workflow-coordinator--implementation)).
2. **Scoping**: The plan includes ONLY the context necessary for that repo.
3. **Sanitization**: Human reviewer ensures no private customer data or internal
   project names are exposed.
4. **Periodic Handoffs**: Produce a handoff summary every 2–3 slices or before ending
   a session. Use the [Handoff Summary Template](handoff-summary-template.md).
5. **Execution**: Plan is run in the target implementation repository.
6. **Local Reporting**: The Implementation AI overwrites
   `docs/ai/current-handoff-summary.md` and `docs/ai/next-session-plan.md`. These
   are the canonical session records.
7. **Feedback Loop**: The summary is returned to the Coordinating Repo.
8. **Status Reconciliation**: Before closing a task, reconcile planning and handoff
   documentation in both repos:
   - Update roadmap and tracker to mark the task complete.
   - Identify the next task as current.
   - Update handoff files.
   - Ensure no contradictory status entries remain.
9. **Drift Check**: Verify the cross-repo tracker, ADRs, and glossary are consistent
   with what was actually implemented.
10. **Commit and Sequence**: The implementation repo is committed before the next
    dependent repo begins its work.

## Context Sanitization Guidance

- **Redact Credentials**: Never include real tokens or keys.
- **Anonymize**: Replace customer names, account IDs, and internal codenames with
  generic placeholders.
- **Generic Work**: Keep open-source library/SDK work generic; do not mention the
  private application that will consume it.
- **Deferred Integration**: Defer private integration details to a private coordinating
  or integration repository.

## Coordinator AI Requirements

When working with the Coordinator AI:

- **Plan Generation**: Instruct the AI to prepare implementation plans and write them
  to `docs/planning/drafts/` — not to execute them across repo boundaries
  automatically.
- **Assumptions**: Require the AI to list all assumptions and open questions.
- **Validation**: Include repo-specific validation expectations.
- **Constraints**: Explicitly state public API stability and compatibility requirements.
- **Drift Checks**: Require the AI to check for documentation drift across the glossary
  and ADRs before finalizing a plan.
- **Human-in-the-Loop**: All generated plans require human review and explicit approval
  before being sent to implementation repos.

## Integration and Release Workflow

- **Per-Repo Validation**: Each repo must pass its own local validation suite.
- **Cross-Repo Compatibility**: Perform integration testing in a separate environment
  or a dedicated integration repo.
- **Tagging Order**: Release the most-downstream dependencies first.
- **Unified Changelog**: Coordinate changelog entries across repos.

## Adaptation Guidance

A Coordinator AI should adapt this workflow by:
- Replacing folder names and repository names with project-specific ones.
- Mapping each implementation repository's specific validation commands.
- Adding project-specific security and privacy rules.
- Customizing the release process for the project's CI/CD pipeline.
