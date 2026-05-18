# AI Plan Communication and Execution

This document defines how an AI assistant should describe, deliver, and execute plans
within a single-agent workflow.

## The Describe-Approve-Execute Pattern

The primary interaction pattern for all implementation work:

1. **Describe**: The AI describes the plan in 3–5 bullet points in chat before doing
   anything. Be specific: what files will be touched, what the outcome will be, what
   validation will be run.
2. **Approve**: The human approves, redirects, or asks questions.
3. **Execute**: The AI executes immediately after approval. No intermediate prompt file
   is written to disk.

The chat conversation is the authoritative record of what was decided and why.

## Plan Description Guidelines

- **Direct and specific**: Name the files, functions, or behaviors involved. Avoid
  vague language like "update the relevant code."
- **Bounded**: One task at a time. Do not propose multiple independent tasks in a
  single description.
- **Surface blockers first**: Identify ambiguities, missing requirements, or risks
  before describing the implementation steps.
- **Include validation**: Always state which validation command must pass as part of
  the task.
- **Scope-check**: If the request is unclear, ask for clarification rather than making
  broad assumptions. If the user insists on proceeding despite ambiguity, include
  open questions as explicit "Assumptions" in the plan.
- **Contradiction Detection**: If the human's instructions contradict a previously
  approved decision, stop and surface the contradiction before executing anything.

## Content Guidelines for Persisted Plans

When a plan or task description is written to disk (e.g., a tracker entry or a
coordinating-repo draft):

- **Necessary context only**: Include only the context required to perform the task.
- **Goals and deliverables**: Clearly state the outcome and any artifacts produced.
- **Constraints**: Include public API stability, backward compatibility, and
  security/privacy requirements.
- **Validation**: Specify the exact validation command that must pass.
- **Documentation**: Note any docs, changelogs, or READMEs that must be updated.
- **AI config**: Remind the executor to read `CLAUDE.md` before making changes.

## Formatting for Persisted Plans

When writing a task plan to a tracker or draft file:

- Use Markdown.
- Do not wrap the entire plan in triple backticks — it makes content harder to read
  in context.
- Clearly mark the start and end if embedded in a larger document:
  `START PLAN` / `END PLAN`.

## Security and Privacy

- **No secrets**: Never include real credentials, tokens, or private customer data in
  plans or summaries. Use placeholders: `YOUR_TOKEN`, `[CLIENT_ID]`.
- **Redact internal names**: Replace proprietary project names or account identifiers
  with generic placeholders unless the context is explicitly private.
- **Sanitize before sharing**: Review all plans and summaries before pasting them
  into external tools or public issue trackers.

## Persistence Expectations

- **Trackers and planners**: Repository-local planning, audit, tracker, and handoff
  outputs should be persisted to `docs/planning/` or `docs/ai/` unless explicitly
  requested as chat-only.
- **Explicit paths**: Plans should specify exact artifact paths when persistence is
  expected.
- **Chat-only intent**: If a plan is intentionally chat-only (e.g., a quick diagnostic
  pass), say so explicitly.
- **Reporting**: Final responses should state which files were created or updated.

## Reviewer Notes and Follow-ups

- **Dual nature**: Reviewer notes may contain both temporary context (useful now) and
  actionable follow-ups (needed for future tasks).
- **Persist actionable items**: Refine and write actionable follow-ups into the
  relevant tracker or handoff doc — do not leave them only in the session summary.
- **Don't discard context**: Do not remove useful reviewer notes from task summaries
  unless they have been successfully moved into durable documentation.

## Example Plan Description (Chat)

> **Plan for Task 3 — Add pagination support to the list endpoint:**
> 1. Add `limit` and `offset` parameters to `ListRequest`; update input validation.
> 2. Pass parameters through to the HTTP client call in `ListHandler`.
> 3. Add unit tests covering zero, default, and maximum offset values.
> 4. Update `docs/api/list-endpoint.md` to document the new parameters.
> 5. Run `[validation command]`; all tests must pass.
>
> Any concerns before I proceed?

## Reuse and Adaptation

When copying this document to a new project:
- Update the validation command examples to match the project's tooling.
- Adjust the `CLAUDE.md` reference if the project uses a different AI config filename.
- Tailor the security section to the project's compliance requirements.
