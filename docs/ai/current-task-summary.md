### Summary
- Performed a documentation and planning clarification pass for CLI token storage behavior.
- Established that CLI token storage is optional and configurable, not a global requirement.
- Defined auth resolution priority for CLI resource commands.
- Updated Step 12 planning to include CLI configuration review and fallback behavior.

### Changes
- Updated `docs/planning/v1/strategy-and-contracts.md` with CLI auth resolution order and storage interaction rules.
- Updated `docs/planning/10-v1-release-work.md` (Step 12) to include specific CLI review items and design decisions.
- Updated `docs/planning/v1/decision-index.md` with new decisions regarding CLI storage optionality and auth resolution.
- Updated `docs/planning/v1/implementation-checklist.md` (Step 18/Step 12) with refined CLI/harness scope.

### Verification
- Manually verified Markdown consistency and links.
- Ran `composer cs:check` to ensure no style regressions.
- Ran `composer review` to ensure project validity.

### Notes
- No source code changes were made in this task.
- No secrets or real credentials were introduced.
- Step 12 implementation remains "Not Started" but now has a clearer requirements baseline for CLI integration.
