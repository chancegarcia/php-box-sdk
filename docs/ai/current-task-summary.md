# Reviewer Notes

## Actionable Follow-ups to Persist
- **Service type-safety follow-up**: `Box\Service\Service` still contains untyped methods and properties. Correct this in Step 12.4. (Persisted to `docs/ai/current-handoff-summary.md` and `docs/audits/12-token-storage-completion-audit.md`).
- **Persistence Guidance**: Update generic AI workflow docs to clarify that planning artifacts should be persisted by default. (Completed in this task).

### Summary
- Updated project and generic workflow documentation to preserve reviewer follow-ups and improve artifact persistence guidance.
- No source code or tests were modified.

### Changes
- Updated `docs/ai/current-handoff-summary.md` and `docs/audits/12-token-storage-completion-audit.md` to include the **Service type-safety follow-up**.
- Refined `docs/prompts/ai-workflow/prompt-delivery-expectations.md` with sections on "Persistence Expectations" and "Reviewer Notes and Actionable Follow-ups".
- Updated `docs/prompts/ai-workflow/single-repository-workflow.md` to emphasize persistence by default and refined handoff triggers.
- Reorganized `docs/ai/current-task-summary.md` to clarify the status of reviewer notes and actionable items.

### Verification
- Ran `composer cs:check` to ensure no documentation formatting issues were introduced.
- Verified that no secrets or real credentials were included in the documentation updates.
- Confirmed that no source code changes were made.

### Notes
- Project-specific follow-ups are now durable in the planning/audit docs.
- Generic workflow improvements apply to all future planning and audit tasks.
