# AI Collaboration Workflow Guide

## Overview

This document covers two modes of running Claude in this project:

1. **JetBrains AI Chat** — current approach (Slices 15.2–15.4). Claude runs in the JetBrains
   AI chat panel. Docs must be attached manually each session.
2. **Claude Code CLI** — planned from Step 15.5+. Claude runs in the terminal with direct
   filesystem access and auto-loaded memory. No attachments needed.

Both modes follow the same collaboration split: **Claude generates prompts, Junie executes
code, human reviews and commits.**

---

## Continuing with JetBrains AI Chat

### Before Starting a New Chat

1. Confirm the previous slice is committed.
2. Confirm Junie has updated `docs/ai/current-task-summary.md` with the slice results.

### Files to Attach

Every new JetBrains AI chat session needs these three files attached:

- `docs/ai/current-handoff-summary.md` — step status, next step status, reviewer resolutions
- `docs/ai/current-task-summary.md` — what was just completed, validation results
- `docs/prompts/ai-workflow/single-repository-workflow.md` — workflow rules (Claude's role,
  step transition approval, commit responsibility)

The memory files that Claude Code CLI loads automatically are **not** loaded by JetBrains AI.
Attaching `single-repository-workflow.md` is the manual substitute.

### Chat Mode

Set the JetBrains AI chat mode to **Default** before typing anything.
Do not use Plan mode — it triggers an approval flow that does not apply to this workflow.

### Continuation Prompt

Paste this at the start of the new chat (update slice numbers as appropriate):

```
Continuing Step 15 (JWT/S2S Implementation) for chancegarcia/box-api-v2-sdk.
Slice 15.X is complete and committed. Please read the attached handoff summary,
task summary, and workflow doc, then confirm you're oriented before we continue.
```

Wait for Claude to confirm orientation before asking for the next slice prompt.

### What Happens Next

Claude will:
1. Read the attached docs and confirm the current state.
2. Generate the next Junie execution prompt when asked.
3. Save the prompt to `docs/prompts/step-15/slice-15-X-<name>.md` for easy copying.

You copy the prompt to Junie, Junie implements, you bring the summary back to Claude for
review, then commit when satisfied.

---

## Claude Code CLI Setup (Planned for Step 15.5+)

---

### Installation

Claude Code is distributed as an npm package. Node.js must be installed first.

```bash
npm install -g @anthropic-ai/claude-code
```

Verify the install:

```bash
claude --version
```

---

### Authentication

You need either a **Claude.ai Pro or Max subscription** or an **Anthropic API key**.

### Option A — Claude.ai account (recommended)

Run the following from any directory and follow the browser prompt:

```bash
claude
```

The CLI will open a browser window to authenticate via your Claude.ai account.
A Pro or Max subscription is required (free tier is not supported).

### Option B — API key

Get a key from the Anthropic Console, then set it in your shell profile:

```bash
export ANTHROPIC_API_KEY=sk-ant-...
```

---

### Starting a Session in This Project

Always start Claude Code from the project root so it loads the correct memory:

```bash
cd /path/to/box-sdk
claude
```

Claude Code automatically reads memory files from:
```
~/.claude/projects/-Users-chance-PhpstormProjects-mine-box-sdk/memory/
```

These files contain the workflow rules, project context, and collaboration patterns — the
same memory that has been built up across prior sessions. No attachments needed.

---

### Continuation Prompt for New Sessions

When picking up mid-step, use a short orientation prompt. Claude Code will read the memory
and the attached docs automatically. You can paste this directly:

```
Continuing Step 15 (JWT/S2S Implementation) for chancegarcia/box-api-v2-sdk.
Slice X.Y is complete and committed. Please read the handoff and task summary below,
then confirm you're oriented before we continue.
```

Then paste the contents of (or have Claude read):
- `docs/ai/current-handoff-summary.md`
- `docs/ai/current-task-summary.md`

Or simply ask Claude Code to read them directly — it has filesystem access:

```
Read docs/ai/current-handoff-summary.md and docs/ai/current-task-summary.md,
then confirm you're oriented for Step 15.
```

---

### How the Workflow Changes vs JetBrains AI

| JetBrains AI Chat | Claude Code CLI |
|---|---|
| Must attach handoff docs each session | Auto-reads memory files on startup |
| Must re-explain workflow per session | Workflow rules persisted in memory |
| Mode must be set to "Default" manually | No mode concept — always "default" |
| Copy prompt from chat to Junie manually | Same — paste prompt to Junie |
| Claude cannot write files to the project | Claude can write prompt files directly to `docs/prompts/` |

The collaboration split stays the same: Claude generates prompts, Junie executes code,
human reviews and commits.

---

### Memory Files in This Project

The memory index is at:
```
~/.claude/projects/-Users-chance-PhpstormProjects-mine-box-sdk/memory/MEMORY.md
```

Key memory files:
- `feedback_workflow.md` — Claude/Junie/human collaboration rules
- `feedback_plan_mode.md` — Don't use plan mode; generate prompts in default mode
- `project_context.md` — Project background, step-by-step release process, validation scripts

These are written and maintained by Claude Code automatically as the project evolves.

---

### Slice Prompt Files

Each Junie execution prompt is saved to `docs/prompts/step-15/` as it is generated.
If the chat UI is unavailable, open the file directly and copy from there.

Current prompts:
- `docs/prompts/step-15/slice-15-2-jwt-provider.md`

---

## Transition Plan

| Phase | Claude Interface | Junie |
|---|---|---|
| Slices 15.2–15.4 | JetBrains AI Chat | Executes all code |
| Step 15.5+ | Claude Code CLI (trial) | Executes all code |
| Post-Junie credits | Claude Code CLI only | Claude writes code directly |

When moving to **Claude Code CLI only** (no Junie), the workflow shifts:
- Claude reads and writes files directly in the terminal session.
- Human reviews diffs and commits — same as now.
- No more copy/paste of prompts between Claude and Junie.
- The slice prompt files in `docs/prompts/` are no longer needed (Claude holds the plan
  in memory and executes directly), but may still be written as a review artifact.

The step-by-step, human-review-before-commit discipline stays the same regardless of which
tool executes the code.
