### Summary
Completed Slice 17 (v1 Release Readiness) — Code Gate and Documentation Gate.

### Code Gate Changes
- `BoxApiErrorTrait::error()` return type corrected `void` → `never` (always throws; PHPStan now narrows correctly at all call sites).
- Yoda conditionals fixed in `WebhookVerifier.php` lines 18 and 61.
- Stale `handleResponseContent` comment removed from `tests/Service/ServiceResponseHandlingTest.php`.
- All legacy symbol scan targets confirmed clean (no stale references to removed names).

### Documentation Gate Changes
- `docs/README.md`: Foundation status updated; v1.0 migration guide link added.
- `docs/migration/upgrading-0.11-to-1.0.md`: Replaced stale "Step 12 Planned" stub with full Token Storage, JWT/S2S, and Webhook Verification sections.
- `docs/user/programmatic-usage.md`: Added §4a JWT/S2S (enterprise token, app user token, auto-mode).
- `docs/user/cli-test-harness.md`: Added JWT CLI commands, token storage options table; removed stale `--transport` option.
- `CHANGELOG.md`: Replaced "Unreleased" with full v1.0.0 entry covering Steps 10–17.

### Slice 18 Changes (Documentation Cleanup)
- Archived 12 completed step trackers/audits to `docs/archive/steps/`.
- Archived 7 superseded planning/audit files to `docs/archive/planning/`.
- Fixed status drift in `docs/planning/release-task-lists.md`, `docs/planning/v1/overview.md`, `docs/planning/README.md`, `docs/README.md`.

### Verification
- `composer review` passed: 334 tests, 902 assertions.
- PHPStan level 0: no errors.
- PHP_CodeSniffer: no errors.
- PHP lint: no syntax errors.
