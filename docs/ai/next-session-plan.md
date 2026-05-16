# Next Session Plan

**Updated**: 2026-05-16 01:15 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Slices 20 and 20.5 are complete. **Pick up at Slice 21: Docblock Quality & Legacy Tag Cleanup.**

---

## Slice 21 — Docblock Quality & Legacy Tag Cleanup

### Purpose
Address remaining docblock quality issues and legacy annotation patterns not covered by earlier slices. No new features — correctness and consistency only.

### Scope

#### 1. `@inheritdoc` Correctness
Audit all `@inheritDoc` / `{@inheritdoc}` uses in `src/`. Ensure each is on a method that actually overrides or implements a parent/interface method. Remove or replace where wrong or awkward.

Pre-flight:
```
grep -rn "@inheritdoc\|{@inheritDoc}" src/ --include="*.php"
```

#### 2. `@package` / `@subpackage` Removal
Remove all `@package` and `@subpackage` tags from `src/` and `tests/`.

**Decision**: Do not replace with `@psalm-package`/`@psalm-subpackage`. PSR-4 namespaces serve this purpose. `@psalm-package` is a Psalm visibility modifier, not a grouping tag. We use PHPStan, not Psalm.

Pre-flight:
```
grep -rln "@package\|@subpackage" src/ tests/ --include="*.php"
```

#### 3. `ConnectionInterface` / `EntrySource` Architectural Review
Re-examine `ConnectionInterface` and `EntrySource` with current eyes. Inline deprecation/removal notes were added during earlier refactors. Determine for each symbol whether it is still v1-sound, should be removed now, or should be deferred with a documented rationale.

#### 4. `json_encode` / `json_decode` Hardening
Audit all `json_encode` and `json_decode` calls in `src/`. Add `JSON_THROW_ON_ERROR` to every call. Catch `JsonException` at appropriate boundaries and translate to domain exceptions where needed.

Pre-flight:
```
grep -rn "json_encode\|json_decode" src/ --include="*.php"
```

#### 5. Legacy Survivor Audit
Scan `src/` for code or comments representing pre-v1 legacy paths that survived earlier purges. Specific known items:

- **`Connection::post` `$nameValuePair` / array `$params`**: Both `Connection.php` and `ConnectionInterface.php` carry "will be deprecated in the future" warnings. For a v1 release "future" is now — decide: remove the array-params and `$nameValuePair` paths, or document as intentionally supported and remove the deprecated language.
- **`FileService` / `FolderService` `method_exists($sharedLink, 'toArray')` fallback**: Guard labeled "Fallback for legacy models." `CreateSharedLinkRequest` has `toArray()` so the branch is live, but the comment implies dead models. Confirm whether the fallback is still needed; remove the comment and tighten the type if not.

Pre-flight:
```
grep -rn "deprecated\|legacy\|nameValuePair\|method_exists" src/ --include="*.php"
```

**Decision for `$nameValuePair` / array-params**: Read `Connection::post` and `ConnectionInterface` to understand the current callers. If no callers pass a `$nameValuePair`, remove the path and the "deprecated" language. If callers still use it, document as intentionally supported and drop the deprecated language. Do not carry "will be deprecated in the future" into a v1 release.

#### 6. Naming Convention & Method Accuracy Audit
Two passes:

**PSR-12 / convention compliance**
- Methods and properties must be camelCase — grep for underscored names
- Parameters must be camelCase — scan method signatures for snake_case param names
- No non-magic double-underscore names

Pre-flight:
```
grep -rn "function [a-z_]*_[a-z]" src/ --include="*.php"
grep -rn "\$[a-z][a-z]*_[a-z]" src/ --include="*.php"
```

**Method name descriptiveness**
Names must describe the *operation*, not just the return value. Focus areas:
- `Client` public API — any method where the name implies passive read but the body does I/O or mutation
- `AuthProviderInterface` / `OAuth2Provider` / `JwtProvider` — auth operations are prime candidates for vague getter names
- `Service` classes — look for `get*` methods that perform writes or multi-step operations
- `Connection` — any method whose name undersells what it does

Any public method rename is a breaking change — add a migration guide entry for each one.

### Acceptance Criteria
- No `@package`/`@subpackage` tags remain in `src/` or `tests/`
- `@inheritdoc` usage is correct and consistent
- `ConnectionInterface`/`EntrySource` decision recorded
- All `json_encode`/`json_decode` calls pass `JSON_THROW_ON_ERROR`
- `nameValuePair`/array-params decision made and implemented
- Legacy `method_exists` fallback in `FileService`/`FolderService` resolved
- No "will be deprecated in the future" language remains in a v1 release
- All public method and property names are camelCase (PSR-12)
- No public method name misrepresents its operation
- Migration guide updated for any public renames
- `composer review` green

---

## After Slice 21

Sequence: Slice 22 → Step 17 → Step 18 → rename (user-driven)

### Slice 22 — License & Rebrand Preparation
- Replace `LICENSE` with Apache 2.0 text
- `composer.json` `"license"` → `"Apache-2.0"`
- Remove all per-file MIT license blocks from `src/` and `tests/`
- Add relicense note to `README.md` and `CHANGELOG.md`
- Add remote URL update item to `docs/planning/release-task-lists.md`
- Create `docs/planning/packagist-rebrand-guide.md`

### Step 17 — v1 Release Readiness
- Full `composer review` gate
- Docs gate: migration guide, CHANGELOG, `docs/README.md` status drift
- Release metadata: `composer.json` version `1.0.0`, keyword/description review
- Security scan: grep for accidentally committed credentials

### Step 18 — Documentation Cleanup and Organization
- Archive completed step trackers to `docs/archive/`
- Retire superseded planning files
- Fix `docs/README.md` to reflect final v1 state

---

## Resolved Questions (do not re-open)
- `@package`/`@subpackage`: Remove entirely; do not replace with `@psalm-*` tags.
- File-level license headers: Remove entirely for Apache 2.0. Root `LICENSE` file is sufficient.
- Property hooks: Deferred to post-v1 (breaking API surface, Hydrator compatibility untested).
- `BoxClientFactory`: now `Box\Factory\BoxClientFactory`; method is `createOAuth2Client()`.
- Constructor hydration: belongs in factories, not resource/model constructors.
- `static fn`: use whenever closure has no `$this`/`self`/`static`/`parent` reference.
