### Summary
- Added a deferred JWT/S2S CLI configuration note to the project's planning and handoff documentation.
- This note captures a future concern regarding separate environment-variable groups or named auth profiles for OAuth2 vs JWT credentials in the CLI/auth harness.

### Changes
- Updated `docs/ai/current-handoff-summary.md` to include the deferred note under "Next Steps".
- Updated `docs/planning/10-v1-release-work.md` (Step 15 - JWT/S2S Implementation) to include the deferred note.
- Updated `docs/planning/v1/implementation-checklist.md` (Step 18 - Token Storage) to include the deferred note.
- Updated `docs/planning/v1/decision-index.md` to record the deferred note as a pending decision/note for Step 12/15.

### Verification
- No source code changes were made.
- No tests were changed.
- Ran `composer cs:check` to ensure no documentation formatting issues were introduced.
- Verified that no secrets or real credentials were included in the documentation updates.

### Notes
- This was a documentation-only task. No implementation of JWT/S2S or Step 12 token storage was started.
- Detailed task summary written to `docs/ai/current-task-summary.md`.
