### Summary
- Updated project AI workflow documentation to formalize "New-Chat Startup Guidance".
- Integrated guidance across generic templates and project-local instructions.
- Prepared for the conclusion of the current documentation slice without starting Slice 12.2.

### Changes
- Updated `docs/prompts/ai-workflow/handoff-summary-template.md` with a new "New-Chat Startup Guidance" section containing specific fields for transition packages.
- Modified `docs/prompts/ai-workflow/single-repository-workflow.md` to define the "New-Chat Startup Guidance" workflow step.
- Updated `docs/prompts/ai-workflow/multi-agent-collaboration.md` to incorporate the transition package into "Handoff Rules".
- Updated `docs/prompts/ai-workflow/README.md` to highlight the inclusion of startup guidance.

### Verification
- Ran `composer cs:check` to ensure no documentation changes introduced style violations (PASSED).
- Manually inspected all modified Markdown files for formatting and clarity.
- Verified that no source code, tests, or Slice 12.2 implementation work was started.
- Confirmed no secrets or private details were introduced.

### Notes
- The added guidance ensures that when a session ends, the assistant provides a concise package (attachments, files to open, prompt, verification steps) for the next session.
- Detailed task summary written to `docs/ai/current-task-summary.md`.

### Follow-ups
- Proceed to Slice 12.2 (In-Memory Storage Completion) after reviewing and committing these documentation changes.
