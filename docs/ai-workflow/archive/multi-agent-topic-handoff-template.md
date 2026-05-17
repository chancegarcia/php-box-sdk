# Multi-Agent Topic Handoff: [Topic Name]

## Handoff Metadata

- Handoff created:
- Created by:
- Intended next tool:
- Intended audience:
- Current repository or workspace:
- Related planning docs:
- Related workflow docs:

## Handoff Purpose

Briefly explain why this handoff exists.

Examples:

- Continue planning in another chat.
- Move from external planning to repository-aware discovery.
- Move from discovery to implementation.
- Summarize a completed implementation slice.
- Prepare for human review.
- Preserve context for later.

## Topic Summary

Summarize the topic in a few paragraphs.

Include:

- what the work is about,
- why it matters,
- current scope,
- what is intentionally out of scope.

## Current State

Describe the current state of the topic.

Include:

- what has been discussed,
- what has been inspected,
- what has been implemented,
- what has been validated,
- what has not been done yet.

## Tool Context

Identify which tools have been involved and how their outputs should be interpreted.

### External Planning Assistant Context

If an external planning assistant was used, summarize:

- what it helped with,
- what assumptions it made,
- what must be verified in the repository,
- what should be treated as advisory only.

### Repository-Aware Agent Context

If JetBrains AI Assistant, Junie, or another repo-aware agent was used, summarize:

- what repository/files were inspected,
- what changes were made,
- what validation was run,
- what findings are repository-grounded.

### Human Approval Context

Summarize human decisions and approvals.

Include:

- approved scope,
- rejected scope,
- deferred scope,
- unresolved decisions.

## Source-of-Truth Notes

Identify what is authoritative and what is advisory.

Example:

- Human-approved decisions are authoritative for scope.
- Actual repository files are authoritative for implementation state.
- Current validation output is authoritative for validation state.
- Repository-aware findings are stronger than external planning assumptions.
- External planning summaries are advisory until verified.
- Older prompts or copied context may be stale.

## Decisions Made

List decisions that have already been made.

| Decision | Status | Notes |
|---|---|---|
| [Decision] | Approved / Deferred / Rejected / Open | [Notes] |

## Working Assumptions

List assumptions that are currently being used.

| Assumption | Confidence | Verification Needed |
|---|---|---|
| [Assumption] | High / Medium / Low | Yes / No / Notes |

## Open Questions

List unresolved questions.

| Question | Blocking? | Owner / Next Step |
|---|---|---|
| [Question] | Yes / No | [Next step] |

## Risks and Concerns

List risks, concerns, and mitigations.

| Risk | Impact | Mitigation / Follow-Up |
|---|---|---|
| [Risk] | High / Medium / Low | [Mitigation] |

## Scope

### In Scope

- [Item]
- [Item]

### Out of Scope

- [Item]
- [Item]

### Future-Track Candidates

List valuable but out-of-scope items.

- [Future-track candidate]
- [Future-track candidate]

## Repository Boundary Notes

If repositories are involved, identify boundaries clearly.

Include:

- coordination repository,
- child repositories,
- standalone repositories,
- files that belong to each repository,
- any known `.git` boundary concerns.

Do not assume one repository can depend on local files from another repository unless explicitly approved.

## Documentation Drift Notes

Identify docs that may need to be checked or updated.

| Document | Drift Concern | Action |
|---|---|---|
| [Path] | [Concern] | Update / Review / No action |

## Validation Status

Summarize validation.

| Validation Item | Status | Notes |
|---|---|---|
| [Command/check] | Not Run / Passed / Failed / Blocked | [Notes] |

If validation was not run, explain why.

## Approval Gates

List approval gates relevant to the next step.

- [ ] Approval after discovery inventory
- [ ] Approval before implementation
- [ ] Approval before dependency major upgrades
- [ ] Approval before lockfile changes
- [ ] Approval before runtime/deployment changes
- [ ] Approval before database/migration/connection behavior changes
- [ ] Approval before secret-handling changes
- [ ] Approval before moving future-track work into current scope
- [ ] Approval before marking complete

Remove or add gates as appropriate for the topic.

## Recommended Next Step

State the recommended next action.

Be specific.

Examples:

- Continue planning in an external assistant.
- Ask Junie to inspect the repository and produce discovery findings.
- Ask Junie to implement the approved slice.
- Ask the human to approve the documentation structure.
- Perform a documentation drift check.
- Prepare a validation-only prompt.

## Suggested Prompt for Next Chat

Paste a ready-to-use prompt for the next assistant.

START PROMPT

[Write prompt here.]

END PROMPT

## Do Not Do

- Do not expose secrets.
- Do not assume repository state without inspection.
- Do not implement changes unless approved.
- Do not expand scope without approval.
- Do not treat external planning output as authoritative repository state.
- Do not skip approval gates.
- Do not mix future-track candidates into current scope without approval.