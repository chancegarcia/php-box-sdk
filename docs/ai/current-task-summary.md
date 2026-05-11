### Summary
- Completed **Step 11.7.3 — Mixed Type Reduction and Client Factory Convenience Review**.
- Significantly reduced `mixed` usage in `Client` to improve type safety and v1 alignment.
- Retained `getNew*` methods as v1 facade conveniences with narrowed parameter types (`?array`).
- Narrowed `copyBoxFolder` to accept `Folder` objects only, removing legacy array/hydration support.
- Updated planning and audit documentation to reflect the current state of the SDK transition.
- Prepared the project for the next phase: **11.8 — Documentation, Migration, and Planning Drift Cleanup**.

### Changes
- **src/Client.php**:
    - Narrowed `getNewFolder`, `getNewUser`, `getNewGroup`, `getNewCollaboration`, and `getNewFile` parameter types from `mixed` to `?array`.
    - Narrowed `addFolder` parameter type from `mixed` to `Folder`.
    - Narrowed `getFolders` return type from `mixed` to `?array` and added detailed PHPDoc `@return array<string|int, Folder>|null`.
    - Narrowed `getFolderFromBox` parameter type from untyped to `string|int`.
    - Narrowed `copyBoxFolder` parameter types: `originalFolder` to `Folder`, `parent` to `Folder`. Removed internal hydration of `$parent` from `array`.
    - Narrowed config-like methods: `setDeviceId`, `setDeviceName`, `setState` now use `?string`. `getDeviceId`, `getDeviceName`, `getState` return `?string`.
    - Narrowed `setRoot` parameter to `?Folder` and removed redundant PHPDocs.
    - Narrowed `setFiles`, `setFolders`, `setCollaborations` to `?array` with specific PHPDoc types.
- **docs/planning/10-v1-release-work.md**:
    - Updated tracker to mark 11.7.3 as completed.
- **docs/audits/11-v1-service-coverage-auth-boundary-audit.md**:
    - Updated "Mixed types" finding to reflect `Client` cleanup in 11.7.3.

### Verification
- **Composer Review**: `composer review` passed successfully.
    - **Syntax**: `composer lint` (All files green).
    - **Tests**: `composer test` (233 tests, 616 assertions, OK).
    - **Static Analysis**: `composer analyse` (No errors).
    - **Style**: `composer cs:check` (Passed, OK).

### Notes
- Detailed handoff summary for the new AI chat has been written to `docs/ai/current-handoff-summary.md`.
- Resource property type narrowing is deferred to a future dedicated audit.
- State/cache methods in `Client` have been narrowed but their continued existence in `Client` will be reviewed during final integration review (11.9).
