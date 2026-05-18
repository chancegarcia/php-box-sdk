# Workflow Evolution — Briefing for Refinement

This file is the starting context for the AI assistant that will polish these workflow documents. Read it before reading anything else in this directory.

---

## What This Directory Is

A set of reusable AI-assisted development workflow templates originally written for a specific project (`chancegarcia/box-api-v2-sdk`, a PHP 8.4+ Box API SDK). The intent is to extract and generalize them so other projects can adopt and adapt them.

---

## What Changed (Old Model → New Model)

### Old model (what the current docs describe)

- **Two AI roles**: Claude Code = *prompt generator*, Junie = *executor*, human = reviewer.
- Claude would write a detailed prompt → Junie would execute it → human would review the diff.
- Project guidelines lived in `.junie/guidelines.md`.
- Task summaries were written to `var/tmp/last-task-summary.md` (not committed).
- Handoff was a single file (`docs/ai/current-handoff-summary.md`).
- Plan mode was used for planning phases.

### New model (what actually works — used from ~Slice 15 onward)

- **One AI role**: Claude Code CLI = *direct executor*, human = reviewer.
- Claude describes the plan in chat → human approves → Claude executes immediately. No separate prompt file written to disk.
- Project guidelines live in **`CLAUDE.md`** at the repo root (Claude Code's native config file).
- Cross-session memory lives in **Claude Code's persistent memory system** (`~/.claude/projects/<path>/memory/`) — a set of typed `.md` files indexed by `MEMORY.md`.
- Session continuity uses **two files** instead of one:
  - `docs/ai/current-handoff-summary.md` — what was done this session.
  - `docs/ai/next-session-plan.md` — scoped plan for the next session.
- **No plan mode** — Claude Code's default mode is used for all tasks.
- **No `current-task-summary.md`** — this file was retired. The two handoff files replace it.

---

## Key Patterns to Carry Forward

These patterns proved out in practice and should be reflected in the polished docs:

### CLAUDE.md as the authoritative project config
The project's `CLAUDE.md` overrides everything. It carries: workflow rules, validation commands, timestamp conventions, architectural decisions, and current status. Keep it short and scannable — it loads into every session.

### Memory system for cross-session context
Claude Code's memory files (user profile, feedback, project state, references) persist facts that would otherwise be re-explained every session. Workflow docs should describe when to write a memory vs. when to update the handoff docs.

### Two-file handoff pattern
- `current-handoff-summary.md`: what happened, decisions made, test baseline at close.
- `next-session-plan.md`: scoped, actionable plan for the next session — not a general roadmap, not a full re-explanation of the project.
Both are overwritten each session; they are not committed (or committing them is acceptable if they live in `docs/ai/`).

### Describe-approve-execute
Claude describes the plan in 3–5 bullet points in chat. Human approves (or redirects). Claude executes. No intermediate prompt file is written to disk. This is faster and keeps the conversation as the authoritative record.

### Slice gate: `composer review` (or equivalent)
Every slice ends with a full validation run. The validation command is project-specific (`composer review`, `make check`, `npm test`, etc.) and lives in `CLAUDE.md`. The workflow docs should describe the *pattern*, not the specific command.

### Human commits, never the AI
Still true. The AI stages, diffs, and proposes a message — the human runs `git commit`. This is a hard rule and should remain prominent.

### Step transition approval still required
The human must explicitly approve before moving to a new step or major slice. The AI must surface this at every step close.

---

## What to Preserve from the Existing Docs

- Slice-based approach (atomic units, one at a time, review before next).
- Zero secret exposure rule.
- "Inspect before editing" principle.
- The tracker structure template (still useful).
- Pre-implementation architecture preparation guidance.
- Crash/recovery guardrails.
- The reuse model (copy, detach, adapt — described in `README.md`).

---

## What to Remove or Rewrite

| Location | What to change |
|:---|:---|
| All files | Remove Junie-specific references; generalize to "the AI assistant" or "Claude Code" |
| `single-repository-workflow.md` | Replace `.junie/guidelines.md` with `CLAUDE.md`; replace `var/tmp/last-task-summary.md` with two-file handoff; remove plan-mode references; update "Recommended Setup" section |
| `single-repository-workflow.md` | Add memory system section |
| `multi-agent-collaboration.md` | **Retire** — the two-agent model is gone. JetBrains AI / Junie may be used occasionally but is not a primary executor; it does not warrant a dedicated workflow doc. |
| `prompt-delivery-expectations.md` | Rewrite — "Claude generates a prompt for Junie" is obsolete; replace with "describe-approve-execute" pattern |
| `handoff-summary-template.md` | Update to reflect the two-file pattern |
| `multi-agent-topic-handoff-template.md` | **Retire** alongside `multi-agent-collaboration.md` — no longer relevant without the two-agent model. |
| `coordinating-repository-workflow.md` | Review whether the multi-repo coordination pattern still applies; may be a lighter rewrite |
| `php-code-style-guidance.md` | Language-specific — keep as-is or extract to a project-specific file rather than the shared template |

---

## Refinement Goals

The polished output should be:

1. **Model-agnostic** — describes the workflow, not a specific AI tool. Where Claude Code specifics matter (CLAUDE.md, memory), name them but note they are Claude Code patterns that other tools may handle differently.
2. **Language-agnostic** — remove PHP/Composer specifics; use placeholder examples.
3. **Concise** — these are templates, not textbooks. A new project team should be able to read and adapt them in under an hour.
4. **Accurate to the new model** — every pattern described should reflect what actually worked, not what was originally planned.
