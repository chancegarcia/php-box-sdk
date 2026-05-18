# Session Startup Prompt — box-sdk

Paste this as your first message when opening a new AI session in this project.

---

```
Read CLAUDE.md first. Then read docs/ai-workflow/single-repository-workflow.md.

Check docs/ai/human-review-notes.md — if it exists and has content, read and acknowledge it,
then clear the file.

Check docs/ai/current-handoff-summary.md and docs/ai/next-session-plan.md — if they exist,
read both and confirm the next step status before we begin.

Confirm you're ready and summarize the active epic/story/task from the handoff (or from
CLAUDE.md if no handoff exists yet).
```

---

- `docs/ai/` is gitignored — session files are local only. Claude Code reads/writes them directly.
- `CLAUDE.md` is authoritative. If it contradicts the workflow templates, `CLAUDE.md` wins.
- Canonical validation: `composer review`
