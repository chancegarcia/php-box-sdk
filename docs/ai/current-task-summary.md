### Summary
- Completed **Roadmap Step Naming and Documentation Drift Cleanup (Step 13.1)**.
- Standardized all Step 13 references to the `Step Title (Step N)` format.
- Reconciled slice numbering in Step 13 audit and roadmap docs (switched from `9.x` to `13.x`).
- Strengthened the **v1 Release Readiness (Step 17)** modernization gate requirements.

### Changes
- Updated `docs/audits/13-auth-lifecycle-provider-extraction-audit.md`:
    - Renamed slices from `9.x` to `13.x`.
    - Marked Auth Lifecycle/Auth Provider Extraction Discovery (Step 13.0) as completed, Roadmap Step Naming and Documentation Drift Cleanup (Step 13.1) as completed, and Guzzle Default Transport Cleanup (Step 13.2) as next.
    - Expanded v1 Release Readiness (Step 17) modernization gate checklist with specific legacy removal targets.
- Updated `docs/planning/v1-release-roadmap.md`:
    - Updated Strategic Status and roadmap reference range.
    - Added explicit slice entries for Step 13.0, Step 13.1, and Step 13.2 in the status table.
    - Marked Auth Lifecycle/Auth Provider Extraction (Step 13) as `In Progress`.

### Verification
- Manual inspection of all modified Markdown files.
- Verified all internal links and step references are consistent.
- No source code was modified; no functional validation required.

### Follow-ups
- Proceed to **Guzzle Default Transport Cleanup (Step 13.2)**.
- Detailed task summary and handoff persisted in `docs/ai/`.
