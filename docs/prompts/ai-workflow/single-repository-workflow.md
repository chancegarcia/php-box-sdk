# Single Repository AI Workflow

## Purpose

This workflow is designed for AI-assisted, slice-based software development within a single repository where planning, implementation, tests, documentation, and release work are co-located.

## Core Principles

- **Local Guidelines Override**: Project-specific guidelines (e.g., `.junie/guidelines.md`) always override generic prompt templates.
- **Inspect Before Editing**: Always examine existing code, tests, and documentation before proposing or making changes.
- **Preserve Public API**: Do not change public signatures or behavior unless the task explicitly requires a breaking change.
- **Planned Cutovers**: For major-version transitions, breaking removals are permitted when explicitly planned, tested, and documented.
- **Separate Planning from Implementation**: Use an initial planning phase to define the approach before writing code.
- **Atomic Slices**: Break large tasks into small, manageable implementation slices.
- **Review and Commit**: Review every slice's output and commit completed work before starting the next slice.
- **Avoid Scope Creep**: Stay focused on the current slice's goals. Document unrelated improvements as follow-ups.
- **Canonical Validation**: Use the repository's native validation commands (e.g., Composer scripts, Makefile targets).
- **No Junk Commits**: Do not commit generated summaries, logs, temp files, caches, or `vendor/` changes.
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

After completing a task or slice, the AI assistant writes a final summary to `var/tmp/last-task-summary.md`.

- **Frequency**: Overwrite the file after every task.
- **Structure**:
    - **Summary**: High-level overview of the result.
    - **Changes**: Bulleted list of modified files and logic.
    - **Verification**: Evidence of testing and validation.
    - **Notes**: Important details for the reviewer.
    - **Follow-ups**: Identified risks or deferred tasks.
- **Privacy**: Redact all secrets and private paths.
- **Usage**: The summary is used to provide feedback in the review chat and is not committed to the repository.

## Slice Workflow

1. **Tracker Creation**: Create or update an initiative tracker in `docs/planning/`.
2. **Draft Prompts**: Treat tracker-embedded prompts as drafts.
3. **Prompt Refinement**: Refine the next slice's prompt immediately before execution.
4. **Single Execution**: Run exactly one slice at a time.
5. **Human Review**: Review the AI's implementation and validation output.
6. **Cleanup/Refinement**: Run follow-up prompts if the slice requires minor fixes.
7. **Commit**: Commit the completed, verified slice.
8. **Progression**: Move to the next slice only after the previous one is committed.

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

## Validation

Use the repository's canonical validation commands. Do not substitute direct tool binaries if the project defines wrapper scripts.

*Example (PHP/Composer):*
- Use `composer test`, not `vendor/bin/phpunit`.
- Use `composer lint`, not `vendor/bin/phpcs`.

## Security

- **No Secrets**: Never use real credentials in prompts or code. Use placeholders like `YOUR_TOKEN`.
- **Sanitization**: Redact summaries before sharing them outside the secure environment.
- **Inspection**: Manually inspect all AI-generated files before committing.
