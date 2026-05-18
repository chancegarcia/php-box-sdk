# AI Handoff Summary Templates

Three files live in `docs/ai/` (gitignored) and bridge sessions:

- `docs/ai/current-handoff-summary.md` — what happened this session (AI writes)
- `docs/ai/next-session-plan.md` — scoped plan for the next session (AI writes)
- `docs/ai/human-review-notes.md` — feedback prepared between sessions (human writes, optional)

---

## Template: `current-handoff-summary.md`

```markdown
# Handoff Summary

## Project
- Repository:
- Primary language/tooling:
- Current branch:
- Next Step Status: Pending Approval | Approved | Blocked
  _(Default: Pending Approval. AI must not proceed until changed to Approved.)_

## Current Work Item
- Epic:
- Story:
- Task completed this session:
- Commit hash:

## What Was Done
-

## Decisions Made
-

## Validation Baseline
- Command:
- Result at session close:

## Active Constraints and Preferences
-

## Security Notes
-

## Recent Gotchas
-

## Remaining Work
-
```

---

## Template: `next-session-plan.md`

```markdown
# Next Session Plan

## Objective
[One sentence: what the next session should accomplish]

## Work Item
- Epic:
- Story:
- Task to execute:
- Goal:

## Context to Load
- Files to open first:
- Tracker to review:
- Handoff to read: `docs/ai/current-handoff-summary.md`

## Suggested Opening Message
[Ready-to-paste message for the next session]

## First Verification Steps
1. Check working tree status and recent commits.
2. Confirm validation passes from the previous session baseline.
3. Confirm tracker reflects the correct current slice.

## Scope Reminders
- In scope:
- Out of scope:
- Deferred follow-ups:
```

---

---

## Template: `human-review-notes.md` (Optional)

```markdown
# Review Notes

## Feedback / Corrections
-

## Follow-up Items to Discuss
-

## Questions for the AI
-
```

---

## Usage Notes

- **Frequency**: Update AI-written files after every 2–3 slices, before ending a
  session, before switching to a new epic, or whenever asked.
- **Human notes**: Write `human-review-notes.md` any time between sessions. The AI
  reads and clears it at session start.
- **Actionable follow-ups**: Items discovered during review should be moved into the
  relevant tracker or follow-up section — not left only in the session summary.
- **No secrets**: Redact credentials and sensitive paths before writing.
- **Storage**: All three files live in `docs/ai/` which is gitignored. AI tools that
  work with the local filesystem can read and write these files directly without
  needing them to be committed.
