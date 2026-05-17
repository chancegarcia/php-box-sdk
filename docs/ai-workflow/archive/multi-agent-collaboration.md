# Multi-Agent Collaboration Workflow

## Purpose

This document defines a reusable workflow model for using multiple AI tools together during software planning, repository discovery, implementation, validation, and handoff.

The workflow is intended to complement JetBrains AI Assistant and Junie workflows. It recognizes that external planning assistants, such as Claude-style chat tools, may be useful for planning and design, while JetBrains AI Assistant and Junie are better suited for repository-aware work inside the IDE.

## Core Principle

External planning assistants can help decide **how to think about the work**.

Repository-aware agents should verify **what is actually true in the repository**.

Human approval controls transitions between planning, discovery, implementation, validation, and scope changes.

## Tool Categories

### External Planning Assistant

Examples:

- Claude chat
- ChatGPT-style external planning chat
- Other non-repository-aware LLM chat tools

Best used for:

- planning and strategy
- design discussion
- architectural tradeoffs
- workflow refinement
- prompt drafting
- risk review
- summary review
- documentation structure proposals
- converting rough notes into actionable prompts

Limitations:

- External planning assistants are advisory unless their output is based on current inspected repository context.
- They should not be treated as authoritative about repository state.
- They should not be trusted for current file paths, dependency versions, implementation details, validation status, or repository boundaries unless verified.
- They should not directly perform source changes.
- They may produce stale, incomplete, or over-generalized plans if repository state is not inspected.

### Repository-Aware Agent

Examples:

- JetBrains AI Assistant
- Junie

Best used for:

- inspecting actual files
- navigating code
- repository discovery
- implementation
- documentation updates
- dependency/configuration review
- validation
- IDE problem inspection
- documentation drift checks
- maintaining repository boundaries

Limitations:

- Repository-aware agents should follow approved plans and prompts.
- They should stop at approval gates.
- They should not expand scope without approval.
- They work best with slice-specific prompts after master planning is complete.

### Human Approver

Responsibilities:

- approve scope
- approve repository boundaries
- approve release-track boundaries
- approve implementation slices
- approve dependency major upgrades
- approve changes to repository order
- approve future-track work moving into current scope
- approve when a repository or task is validated
- arbitrate conflicts between planning suggestions and repository-grounded findings

## Authority and Source-of-Truth Rules

Use this order of authority unless a project defines a stricter policy:

1. Human-approved decisions and task scope
2. Actual repository files and current inspected state
3. Current project tracking/status documents
4. Current validation output
5. Repository-aware agent findings
6. External planning assistant recommendations
7. Older summaries, copied context, or stale prompts

External planning output must be verified against repository state before implementation.

Repository-aware discovery can supersede earlier planning assumptions, but scope changes still require human approval.

## Recommended Workflow

### 1. Planning and Design

Use an external planning assistant or repository-aware chat to clarify:

- goals
- constraints
- risks
- release-track boundaries
- approval gates
- documentation structure
- sequencing
- implementation strategy
- prompt shape

Expected output:

- planning summary
- proposed workflow
- master prompt or kickoff prompt
- risk list
- open questions
- proposed documentation structure

### 2. Repository-Grounded Discovery

Use JetBrains AI Assistant or Junie to inspect the actual repository.

The repository-aware agent should:

- inspect current files
- identify repository boundaries
- identify tooling and configuration
- produce discovery findings
- identify blockers and risks
- propose implementation slices
- update or propose tracking documents
- stop before implementation unless implementation is explicitly approved

Expected output:

- repository-grounded inventory
- dependency/tooling findings
- validation command inventory
- documentation drift notes
- implementation slice proposal
- approval gate checklist

### 3. Human Review

The human reviews:

- discovery findings
- proposed implementation slices
- blockers
- risks
- future-track candidates
- documentation structure
- approval gates

The human then approves, revises, or rejects the next step.

### 4. Repository-Aware Implementation

Use Junie or JetBrains AI Assistant for implementation.

The repository-aware agent should:

- work one approved slice at a time
- make local edits
- update lockfiles only when approved or in approved scope
- run or document validation
- update status tracking
- perform documentation drift checks
- stop at approval gates

Expected output:

- implemented slice
- validation results
- updated tracking docs
- drift check summary
- next-step recommendation

### 5. Optional External Review

After repository-aware discovery or implementation, a sanitized summary may be pasted into an external planning assistant for review.

Useful review questions:

- Are risks missing?
- Is the sequence sensible?
- Are approval gates sufficient?
- Are future-track candidates being mixed into current scope?
- Can this be compressed into a clearer next implementation prompt?

External review should not replace repository-grounded validation.

## Prompt Type Guidance

### Master Planning Prompt

Use for large planning efforts.

Recommended length:

- 2,000 to 4,500 words

Best for:

- defining operating rules
- release-track boundaries
- approval gates
- documentation structure
- cross-repository coordination
- known constraints

Do not repeat the master prompt for every implementation task. Treat it as a kickoff artifact or constitution.

### Repository Discovery Prompt

Recommended length:

- 800 to 1,500 words

Best for:

- inspecting one repository or a small group of repositories
- inventorying tooling and dependencies
- identifying blockers
- proposing implementation slices
- stopping before implementation

### Implementation Slice Prompt

Recommended length:

- 400 to 1,000 words

Best for:

- one approved repository
- one approved task slice
- focused source/config/doc edits
- validation and tracking updates

### Focused Fix Prompt

Recommended length:

- 150 to 500 words

Best for:

- small bug fixes
- narrow compatibility fixes
- targeted documentation updates
- one failing validation item

### Validation or Handoff Prompt

Recommended length:

- 300 to 800 words

Best for:

- summarizing current state
- checking validation status
- preparing the next chat/session
- documenting blockers and next steps

## Handoff Rules

When moving between tools or chat sessions, provide a concise **New-Chat Startup Package**:

1. **Context attachments to include**: list the summaries, trackers, workflow docs, and task-relevant files the user should attach.
2. **Files to open first**: list the project files the next assistant should inspect immediately (implementation, tests, status docs).
3. **Suggested opening message**: provide a short, ready-to-paste prompt for the next chat stating status, next intended action, and constraints.
4. **First verification steps**: include checking working tree status, recent commits, current tracker state, and relevant tests.
5. **Scope and Safety**: identify what is next, what is deferred, avoid secrets, and treat summaries as subordinate to repository state.

General Handoff Principles:
- Summarize current state and identify what is authoritative vs. advisory.
- Include only necessary context; avoid secrets and proprietary details.
- List decisions made, open questions, and approval gates.
- State whether the next tool should plan, inspect, implement, validate, or review.

## Secret and Sensitive Context Handling

When using external planning assistants:

- Do not paste secrets.
- Do not paste private credentials.
- Do not paste tokens, keys, passwords, or production connection strings.
- Sanitize environment/config examples.
- Prefer summaries over raw sensitive file contents.
- If sensitive information is necessary to discuss, describe it generically.

Repository-aware tools may inspect local files as needed, but outputs should avoid exposing secrets in summaries, prompts, planning docs, or handoffs.

## Documentation Drift Checks

When a task changes plans, workflows, status, dependencies, commands, runtime requirements, or repository responsibilities, perform a documentation drift check.

A drift check should compare affected documentation across:

- coordination-level planning docs
- child-repository docs
- workflow docs
- prompt templates
- status tracking docs
- decision records

If drift is found, either update the affected docs or record a follow-up task.

## Approval Gates

Use approval gates when moving between major phases or risk levels.

Common approval gates:

- after planning
- after discovery inventory
- after proposed implementation slices
- before dependency major upgrades
- before lockfile updates if not already approved
- before changing runtime requirements
- before changing deployment assumptions
- before changing CI/Docker assumptions
- before database/migration/connection behavior changes
- before secret-handling changes
- before moving future-track work into current scope
- before marking a repository validated
- before moving to the next repository
- before marking an epic complete

## Future-Track Candidate Handling

During planning or discovery, agents may identify useful work that is outside the current scope.

Examples:

- CI implementation
- Docker implementation
- deployment automation
- security hardening
- secret management cleanup
- broad tooling rollout
- architecture revisions
- broad regression test expansion
- major modernization
- behavior redesign

Do not implement future-track candidates unless explicitly approved.

Record them separately from current-scope tasks.

## Regression Test Semantics

Regression tests may be used to capture current behavior.

Regression tests mean:

- They protect existing behavior during normal development.
- They are valid concerns when they fail during bug fixes, feature additions, dependency updates, EOL compatibility work, and maintenance.
- They are not necessarily absolute product truth during explicitly approved refactoring, architecture strengthening, or intentional behavior redesign.
- During approved refactoring, failing regression tests should trigger review and conscious decision-making, not automatic preservation of legacy behavior.
- Do not weaken or remove regression tests without approval.

## Recommended Generic Workflow Additions

Existing JetBrains-oriented workflows can remain focused on repository-aware work.

Add multi-agent guidance as a companion layer rather than replacing the JetBrains workflow.

Recommended model:

- External planning assistants are advisory.
- Repository-aware agents verify actual repository state.
- Humans approve transitions and scope changes.
- Handoffs preserve decisions and reduce context loss.
- Documentation drift checks keep plans and implementation aligned.

## Summary

Use external planning assistants for planning, design, and prompt refinement.

Use JetBrains AI Assistant and Junie for repository-grounded discovery, implementation, validation, and drift checks.

Use human approval gates to control scope, risk, and transitions.