# Coordinating Repository AI Workflow

## Purpose

This workflow is for a central **Coordinating Repository** that manages the roadmap, planning, architecture decisions, and integration status across multiple downstream **Implementation Repositories**.

## Roles

- **Coordinating Repository**: Owns the roadmap, cross-repo terminology, architecture decisions (ADRs), global glossary, and dependency sequencing.
- **Implementation Repository**: Owns the source code, unit/integration tests, and repository-specific validation for a single component or SDK.
- **AI Assistant (Coordinator)**: Analyzes cross-repo requirements and generates implementation prompts.
- **AI Assistant (Implementation)**: Executes prompts within a specific implementation repository.
- **Human Reviewer**: Approves cross-repo plans and validates individual repository changes.

## Core Principles

- **Coordinator Ownership**: The coordinating repo is the source of truth for "What" and "Why".
- **Implementation Autonomy**: Individual repos own "How", adhering to their own local guidelines and tooling.
- **Data Privacy**: Do not leak private downstream details into open-source implementation repos.
- **Sanitized Handoff**: Prompts passed to implementation repos must be scoped, sanitized, and contextualized for that specific repo.
- **Dependency Tracking**: Cross-repo work must follow a planned sequence to avoid integration breakage.
- **Drafts and Refinement**: Reusable prompt patterns are drafts and must be tailored per repository.

## Recommended Coordinating Repo Structure

```plain text
docs/
  roadmap/      # High-level milestones and initiatives
  architecture/ # ADRs and global design patterns
  glossary/     # Unified domain terminology
  prompts/      # Reusable templates for cross-repo work
  projects/     # Per-initiative trackers and status
  status/       # Global integration and release status
  decisions/    # Log of major project decisions
```

## Cross-Repo Tracker Workflow

The coordinator maintains a tracker for each major initiative:

- **Initiative**: Unified goal (e.g., "Add AI Support").
- **Affected Repos**: List of implementation repos involved.
- **Dependency Map**: Which repo must be updated before others.
- **Per-Repo Status**: Tracks the slice progress in each repository.
- **Implementation Prompts**: Draft prompts generated for each repository.
- **Integration Risks**: Known points of friction between repositories.
- **Release Sequencing**: The order in which repositories must be tagged and released.

## Prompt Handoff Workflow

1. **Coordinator Preparation**: The Coordinator AI creates/refines a prompt for a specific implementation repo.
2. **Scoping**: The prompt includes ONLY the context necessary for that repo.
3. **Sanitization**: The human reviewer ensures no private customer data or internal project names are leaked.
4. **Execution**: The prompt is run in the target implementation repository.
5. **Local Reporting**: The implementation AI writes a summary to its local `var/tmp/last-task-summary.md`. This persisted summary should be the canonical detailed review summary and must match the final response as closely as practical.
6. **Feedback Loop**: The summary is returned to the Coordinating Repo.
7. **Status Update**: The Coordinator AI updates the cross-repo tracker based on the results.
8. **Commit and Sequence**: The implementation repo is committed/pushed before the next dependent repo begins its work.

## Context Sanitization Guidance

- **Redact Credentials**: Never include real tokens or keys.
- **Anonymize**: Replace customer names, account IDs, and internal project codenames with generic placeholders.
- **Generic Work**: Keep open-source SDK/Library work generic; do not mention the private application that will consume it.
- **Deferred Integration**: Defer private integration details to a private coordinating or integration repository.

## Coordinating Repo AI Prompt Requirements

When asking the Coordinator AI to generate work:

- **Prompt Generation**: Instruct the AI to generate implementation prompts, not to execute them across repo boundaries automatically.
- **Assumptions**: Require the AI to list all assumptions and open questions.
- **Validation**: Include repo-specific validation expectations (e.g., "Ensure `composer test` passes in the SDK repo").
- **Constraints**: Explicitly mention public API stability and compatibility requirements.
- **Drift Checks**: Require the AI to check for documentation drift across the glossary and ADRs.
- **Human-in-the-Loop**: All generated prompts require human review before they are sent to implementation repos.

## Integration and Release Workflow

- **Per-Repo Validation**: Each repo must pass its own local test suite.
- **Cross-Repo Compatibility**: Perform integration testing in a separate environment or a dedicated integration repo.
- **Tagging Order**: Release the most-downstream dependencies first.
- **Unified Changelog**: Coordinate changelog entries across repos to provide a cohesive release story.

## Adaptation Guidance

A Coordinating AI should be able to adapt this workflow by:
- Replacing folder names and repository names with project-specific ones.
- Mapping each implementation repository's specific validation commands.
- Adding project-specific security and privacy rules.
- Customizing the release process based on the project's CI/CD pipeline.
