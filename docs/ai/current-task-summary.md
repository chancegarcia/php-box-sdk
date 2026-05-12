### Summary
- Reconciled roadmap and documentation drift following Step 12 completion.
- Renamed the main v1 tracker to `docs/planning/v1-release-roadmap.md`.
- Updated Step 13 to be **Auth Lifecycle/Auth Provider Extraction** and deferred API Fixture Realism to Step 15.2.

### Changes
- Renamed `docs/planning/10-v1-release-work.md` to `docs/planning/v1-release-roadmap.md` via `git mv`.
- Updated all internal documentation links and references to point to the new canonical roadmap filename.
- Corrected Step 13 definition in the roadmap and handoff summary to prioritize Auth Provider extraction.
- Realigned the remaining v1 sequence to reflect that JWT/S2S implementation depends on the new Auth Provider boundary.
- Updated `docs/planning/README.md` and `docs/README.md` to reflect the new roadmap filename and current Step 13 focus.
- Adjusted previous audit documents and task summaries to maintain internal consistency regarding Step 13 reordering.

### Verification
- Verified all documentation links via project-wide search.
- Confirmed Step 12 remains marked complete and Step 13 is marked as not started.
- Verified that the handoff summary correctly points the next chat to Auth Provider audit and planning.

### Follow-ups
- Start Step 13: Auth Lifecycle/Auth Provider Extraction audit and planning.
