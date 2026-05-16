# Next Session Plan

**Updated**: 2026-05-16 04:19 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Slices 20, 20.5, 21, 22, and the pre-Step-17 polish slice are complete. PHPCS sniffs for docblock enforcement are fully wired. **Pick up at Step 17: v1 Release Readiness.**

---

## Step 17 — v1 Release Readiness

### Purpose
Final gate checks before tagging v1.0.0.

### Scope

1. **`composer review` gate** — Must be fully green (it is as of Slice 22).

2. **Docs gate**:
   - `docs/migration/upgrading-0.11-to-1.0.md` — Review for completeness. Sections 14 and 15 were added in Slice 21. Verify all breaking changes from the full roadmap are documented.
   - `CHANGELOG.md` — Review for completeness against all slices.
   - `docs/README.md` — Check for status drift (should reflect final v1 state).

3. **Release metadata in `composer.json`**:
   - Confirm `"version": "1.0.0"` is set.
   - Review `"keywords"` and `"description"` for accuracy.

4. **Security scan** — Grep for accidentally committed credentials:
   ```bash
   grep -r "BOX_" --include="*.php" src/ tests/ | grep -v "env\|getenv\|ENV\|example\|test\|spec\|comment"
   ```
   Also scan for hardcoded tokens/keys.

5. **v1-release-roadmap.md status** — Slices 20, 20.5, 21, 22 currently show "Not Started" — update to "Complete".

### Acceptance Criteria
- `composer review` green
- Migration guide covers all v0.11→v1 breaking changes
- `CHANGELOG.md` is accurate and complete
- `composer.json` version and metadata confirmed
- No credentials in source
- Roadmap tracker up to date

---

## Step 18 — Documentation Cleanup and Organization

### Scope

1. **Archive completed step trackers** — Move completed planning files to `docs/archive/`.
2. **Retire superseded planning files** — Remove or archive files that no longer reflect current state.
3. **Fix `docs/README.md`** — Reflect final v1 state.
4. **Update `v1-release-roadmap.md`** — Mark all completed slices.

---

## Deferred / Post-v1 Tasks (do not open unless asked)

- **Array generic annotation pass** — All `@param array`, `@return array`, `@var array` in existing docblocks must be converted to `list<T>` / `array<K,V>` / inline shapes. Full audit is post-v1.
- **`@throws` completeness pass** — Full audit of all remaining public methods deferred post-v1.
- **Naming standardization** — Standardize terminology in roadmap/planning docs. Deferred to post-v1.
- **Property hooks** — PHP 8.4 property hooks for DTOs. Deferred to post-v1.
- **Comments metadata** — `Task`, `File Request` endpoints. Deferred to post-v1.

---

## Resolved Questions (do not re-open)

- `@package`/`@subpackage`: Removed and auto-enforced by PHPCS. ✓
- `@author`/`@copyright`/`@license`: Removed from all files and now auto-enforced by PHPCS `ForbiddenAnnotations`. ✓
- File-level license headers: Removed entirely for Apache 2.0. Root `LICENSE` file is sufficient. ✓
- `$nameValuePair` in `Connection::post()`: Removed. ✓
- `BoxResponse::json()` on bad JSON: Now throws `\JsonException`. ✓
- `json_encode`/`json_decode`: Always `JSON_THROW_ON_ERROR`. ✓
- Property hooks: Deferred to post-v1. ✓
- `BoxClientFactory`: now `Box\Factory\BoxClientFactory`; method is `createOAuth2Client()`. ✓
- `static fn`: use whenever closure has no `$this`/`self`/`static`/`parent` reference. ✓
- Redundant `@param`/`@return` tags: auto-enforced via `ParameterTypeHint.UselessAnnotation` and `ReturnTypeHint.UselessAnnotation`. ✓
- Lone `@inheritDoc` blocks: auto-enforced via `UselessInheritDocComment`. ✓
- `ReferenceUsedNamesOnly`: wired with correct properties. ✓
- Apache 2.0 relicense: `LICENSE`, `composer.json`, `README.md`, `CHANGELOG.md` all updated. ✓
