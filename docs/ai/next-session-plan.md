# Next Session Plan

**Updated**: 2026-05-16 03:53 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Slices 20, 20.5, and 21 are complete. PHPCS sniffs for docblock enforcement are wired. **Pick up at Slice 22: License & Rebrand Preparation.**

---

## Slice 22 — License & Rebrand Preparation

### Purpose
Replace the per-file MIT license headers with the project-level Apache 2.0 license and prepare metadata for the v1.0 rename.

### Scope

1. **Replace `LICENSE` file** — Replace current content with Apache 2.0 license text.
2. **`composer.json` license field** — Change `"license"` from current value to `"Apache-2.0"`.
3. **Remove per-file MIT license blocks** from all `src/` and `tests/` files. The file-level docblocks currently carry a full MIT license text. Remove the entire license block from each file (the root `LICENSE` file is sufficient for Apache 2.0).
4. **`README.md` and `CHANGELOG.md`** — Add a brief relicense note.
5. **`docs/planning/release-task-lists.md`** — Add remote URL update item (for when repo is renamed).
6. **Create `docs/planning/packagist-rebrand-guide.md`** — Step-by-step guide for Packagist rebrand at time of rename.

### Acceptance Criteria
- No per-file MIT license blocks remain in `src/` or `tests/`
- `LICENSE` file is Apache 2.0
- `composer.json` `"license"` is `"Apache-2.0"`
- `README.md` and `CHANGELOG.md` note the license change
- `composer review` green

---

## After Slice 22

Sequence: Step 17 → Step 18 → rename (user-driven)

### Step 17 — v1 Release Readiness
- Full `composer review` gate
- Docs gate: migration guide, CHANGELOG, `docs/README.md` status drift
- Release metadata: `composer.json` version `1.0.0`, keyword/description review
- Security scan: grep for accidentally committed credentials

### Step 18 — Documentation Cleanup and Organization
- Archive completed step trackers to `docs/archive/`
- Retire superseded planning files
- Fix `docs/README.md` to reflect final v1 state
- Update `v1-release-roadmap.md` status for Slices 20, 20.5, 21, 22 (currently show "Not Started" — stale)

---

## Pending After Next Commit

- **`ReferenceUsedNamesOnly` PHPCS sniff** — Add `SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly` to `phpcs.xml.dist` to enforce "no FQN when class is imported." Evaluate after the qualifier-cleanup commit is in; the sniff needs tuning (`allowPartialUses`, global-namespace exclusions). Add `@author`, `@copyright`, `@license` to `ForbiddenAnnotations` after Slice 22 license cleanup is committed.

---

## Deferred / Post-v1 Tasks (do not open unless asked)

- **Array generic annotation pass** — All `@param array`, `@return array`, `@var array` in existing docblocks must be converted to `list<T>` / `array<K,V>` / inline shapes. Rule is in style standards and now enforced by `MissingTraversableTypeHintSpecification` (excluded for now). Full audit is post-v1.
- **`@throws` completeness pass** — New style rule requires `@throws` on every method in the exception bubble chain. Main callers covered in Slice 21; full audit of all remaining public methods deferred post-v1.
- **Naming standardization** — Standardize terminology in roadmap/planning docs (steps/slices/sub-slices/sub-steps). Global guideline update deferred to post-v1.
- **Property hooks** — PHP 8.4 property hooks for DTOs. Breaking API surface, Hydrator compatibility untested. Deferred to post-v1.
- **`ForbiddenAnnotations` expansion** — Add `@author`, `@copyright`, `@license` to the forbidden list after Slice 22 license cleanup is committed.

---

## Resolved Questions (do not re-open)
- `@package`/`@subpackage`: Removed entirely and now auto-enforced by PHPCS. ✓
- File-level license headers: Remove entirely for Apache 2.0. Root `LICENSE` file is sufficient. ✓ (will be done in Slice 22)
- `$nameValuePair` in `Connection::post()`: Removed. Array params remain for OAuth2 form-encoding. ✓
- `BoxResponse::json()` on bad JSON: Now throws `\JsonException`. ✓
- `json_encode`/`json_decode`: Always `JSON_THROW_ON_ERROR`. ✓
- Property hooks: Deferred to post-v1. ✓
- `BoxClientFactory`: now `Box\Factory\BoxClientFactory`; method is `createOAuth2Client()`. ✓
- Constructor hydration: belongs in factories, not resource/model constructors. ✓
- `static fn`: use whenever closure has no `$this`/`self`/`static`/`parent` reference. ✓
- Redundant `@param`/`@return` tags: now auto-enforced via `ParameterTypeHint.UselessAnnotation` and `ReturnTypeHint.UselessAnnotation`. ✓
- Lone `@inheritDoc` blocks: now auto-enforced via `UselessInheritDocComment`. ✓
