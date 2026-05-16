# Next Session Plan

**Updated**: 2026-05-15 23:07 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Slice 20 items 1–4 and 8 are complete. **Pick up at item 5: type coverage audit.**

Completed this session:
- `?->` operator conversions ✓
- PHPDoc annotation spacing ✓
- `@throws` audit (missing tags added; false tags removed; docblock param types corrected) ✓
- Typed constants (`public const string/int/array` across 8 files) ✓
- v1 `@todo` audit (dead-code if-blocks removed; todos resolved, deferred to post-v1, or converted to `// Post-v1:` markers) ✓

---

## Slice 20 Remaining Scope

### ~~1. `?->` conversions~~ ✓
### ~~2. PHPDoc annotation spacing~~ ✓
### ~~3. `@throws` audit~~ ✓
### ~~4. Typed constants~~ ✓
### ~~8. v1 `@todo` audit~~ ✓

### 5. Type Coverage Audit
Review `src/` for untyped or `mixed` properties, parameters, and return types. Tighten where the actual type is known. Intentional `mixed` (e.g., legacy hydration) must be confirmed and documented.

Specific areas to check:
- **`BoxException`**: stale `@return self` removed from `setBoxCode` ✓. Remaining: `$error`, `$errorDescription`, `$boxCode`, `$status` all typed `mixed` — evaluate whether any can be narrowed.
- **`BoxException` constructor `$code`**: typed `mixed` — narrow to `int|string` since non-int codes are stored as `boxCode`.
- **`Box\Resource\Event`**: numerous `mixed` properties — audit and tighten where type is deterministic from Box API contract.
- **`Service`**: `$connection` and `$token` properties have no PHP type hints — add them.
- **`Collaboration`**: several methods have *no type hints at all* (not just `mixed`) — `setAccessibleBy`/`getAccessibleBy` (Box API: `User|Group` mini-object), `setCreatedBy`/`getCreatedBy`, `setItem`/`getItem` (Box API: `File|Folder` mini-object), `setType`/`getType`. Audit the full class for missing types.
- **Broad pass**: grep `src/Resource/` for methods with no parameter or return type hints — `Collaboration` is a known case but likely not the only one.

### 6. Property Hooks on DTOs / Value Objects
Audit `src/Dto/`, `src/Resource/`, `src/Connection/Token/` for get/set method pairs that qualify for PHP 8.4 property hooks. Apply when:
- Class is data-only (no interface method contracts declaring getters/setters)
- Property is public API (`$obj->prop` access is natural)
- Hook logic is lightweight: normalization, coercion, or a simple guard — no service calls, no side-effects beyond the property
- No fluent setter chain needed

Skip any class implementing an interface with getter/setter method signatures.

### 7. BoxClientFactory Namespace Move
Move `Box\Service\BoxClientFactory` → `Box\Factory\BoxClientFactory`. Rename `createClient()` → `createOAuth2Client()`. Update `BoxClientFactoryInterface`. Update all callers, tests, migration guide.

Pre-flight:
```
grep -rn "BoxClientFactory\|createClient\b" src/ tests/ bin/ --include="*.php"
```

### 8. v1 `@todo` Audit
Grep `src/` for `@todo` and `TODO` comments. For each:
- If the work is done: remove the comment.
- If it is a v1 blocker: create a tracking entry and resolve in Slice 20 or the appropriate slice.
- If it is post-v1: replace with a `// Post-v1:` comment or remove if obvious from context.

Pre-flight:
```
grep -rn "@todo\|TODO" src/ --include="*.php"
```

Specific areas to check: `File::setPathCollection`, `File::setSharedLink` (may have v1 todos that were either completed or lost to documentation drift — confirm current state before acting).

---

## Slice 21 — Docblock Quality & Legacy Tag Cleanup

### Purpose
Address remaining docblock quality issues and legacy annotation patterns that were not in scope for Slice 20's type/throws audit.

### Scope

#### `@inheritdoc` Correctness
Audit all uses of `@inheritDoc` and `{@inheritdoc}` across `src/`. Ensure:
- Only used on methods that actually override or implement a parent/interface method.
- Remove or replace with explicit docblocks where the tag is wrong, awkward, or misleading.
- Lowercase/uppercase form is consistent with PHPCS expectations.

Pre-flight:
```
grep -rn "@inheritdoc\|{@inheritDoc}" src/ --include="*.php"
```

#### `@package` / `@subpackage` Removal
Remove **all** `@package` and `@subpackage` tags from every file in `src/` and `tests/`.

**Decision**: Do not replace with `@psalm-package`/`@psalm-subpackage`. PSR-4 namespaces already provide the grouping structure; these tags are redundant. `@psalm-package` is a Psalm visibility modifier, not a documentation-grouping tag, and we use PHPStan not Psalm.

Pre-flight:
```
grep -rn "@package\|@subpackage" src/ tests/ --include="*.php"
```

#### `ConnectionInterface` / `EntrySource` Architectural Review
Re-read `ConnectionInterface` and `EntrySource` with fresh eyes. There are inline notes (deprecation hints, removal candidates) in parameter docs from earlier refactors. Determine for each:
- Is the symbol still architecturally sound for v1?
- If deprecation or removal is warranted, do it now rather than carrying a docblock note into the release.
- Document the decision; if deferred, explain why.

#### `json_encode` / `json_decode` Hardening
Grep `src/` for all `json_encode` and `json_decode` calls. Harden each:
- `json_encode`: pass `JSON_THROW_ON_ERROR`; handle failure explicitly.
- `json_decode`: pass `JSON_THROW_ON_ERROR`; narrow the return type assertion.
- Catch `JsonException` at the appropriate boundary and translate to a domain exception where needed.

Pre-flight:
```
grep -rn "json_encode\|json_decode" src/ --include="*.php"
```

### Acceptance Criteria
- No `@package`/`@subpackage` tags remain in `src/` or `tests/`
- `@inheritdoc` usage is correct and consistent
- `ConnectionInterface`/`EntrySource` decision recorded
- All `json_encode`/`json_decode` calls use `JSON_THROW_ON_ERROR`
- `composer review` green

---

## Slice 22 — License & Rebrand Preparation

### Purpose
Transition the license from MIT to Apache-2.0, clean up file-level license headers, add the rebrand/relicense note to user-facing docs, and document the Packagist transition process.

### Scope

#### LICENSE File
Replace `LICENSE` content with the full Apache 2.0 license text.

#### `composer.json` License Field
Change `"license": "MIT"` → `"license": "Apache-2.0"`.

#### File-Level License Headers
**Decision**: Remove per-file copyright/license blocks entirely. Apache 2.0 does not require per-file notices; the root `LICENSE` file satisfies the license. This is standard modern practice (Symfony, Laravel, Doctrine). Do not add `@license Apache-2.0` docblock tags as a replacement — the root file is sufficient.

Pre-flight (find all files with a license block):
```
grep -rln "MIT License\|MIT license\|@license" src/ tests/ --include="*.php"
```

#### README & CHANGELOG Note
In both `README.md` and `CHANGELOG.md`, add the following note in the appropriate v1.0.0 section:

> Note: Starting with v1.0.0, this SDK has been rebranded and transitioned from the MIT License to the Apache 2.0 License to provide better patent and trademark protections.

#### Remote URL Update Task
Add a checklist item in `docs/planning/release-task-lists.md` to update the remote repository URL to the new rebranded repo after the rename is performed (user-driven; do not perform the rename here).

#### Packagist Transition Guide
Create `docs/planning/packagist-rebrand-guide.md` documenting the full Packagist transition process for the rebrand. Content:

```markdown
# Packagist Transition Guide: Rebranding & Relicensing

When transitioning an open-source PHP package to a new brand, a new repository, and a new
license (e.g., MIT to Apache 2.0) for a `v1.0.0` release, follow this standard protocol.
This ensures that legacy users are not broken, while successfully redirecting traffic to
your new rebranded package.

---

## Step 1: Prepare the New Codebase

Before publishing to Packagist, ensure your renamed repository contains the updated
licensing information.

1. **Update `LICENSE`**: Replace the content of your existing `LICENSE` file with the full
   text of the Apache 2.0 license.
2. **Update `composer.json`**: Update the metadata to reflect the new package name, new
   repository URL, and the new license type.

```json
{
    "name": "new-brand/box-sdk",
    "description": "Rebranded and refactored Box.com API SDK",
    "license": "Apache-2.0",
    "support": {
        "issues": "https://github.com/new-organization/new-repo/issues",
        "source": "https://github.com/new-organization/new-repo"
    },
    "autoload": {
        "psr-4": {
            "NewBrand\\BoxSdk\\": "src/"
        }
    }
}
```

3. **Tag the Release**: Tag your new refactored codebase as `v1.0.0` (or your chosen stable
   version string) and push the tag to GitHub.

---

## Step 2: Publish the New Package on Packagist

1. Log in to [Packagist.org](https://packagist.org/).
2. Click the **Submit** button in the top navigation bar.
3. Paste the URL of your new, renamed GitHub repository
   (e.g., `https://github.com/new-organization/new-repo`) into the repository URL field.
4. Click **Check** and then **Submit** to finalize creation.
5. Set up the GitHub Webhook for Packagist on your new repository so that future releases
   sync automatically.

---

## Step 3: Deprecate the Old Package (The "Abandonment" Protocol)

To prevent breaking existing software stacks that rely on your `v0.11` release, **do not
delete** the old package. Instead, use Packagist's built-in abandonment mechanism.

1. Navigate to the Packagist page of your **old package**
   (e.g., `https://packagist.org/packages/old-brand/old-sdk`).
2. Click the **Abandon** button located near the top right of the package management interface.
3. A prompt will appear asking for a replacement package.
4. Enter the exact name of your **new package** (e.g., `new-brand/box-sdk`).
5. Confirm the action.

---

## What Happens Next?

- **Zero Downtime for Legacy Users**: Existing projects running `composer install` with a
  lockfile targeting `old-brand/old-sdk` will continue to download the `v0.11` code seamlessly.
- **Proactive Warning Notices**: If a developer runs `composer update` or tries to execute
  `composer require old-brand/old-sdk` in a new project, Composer will output a clear warning:

```text
Package old-brand/old-sdk is abandoned, you should avoid using it.
Use new-brand/box-sdk instead.
```

- **Search Engine Optimization**: Packagist will visually mark the old page as abandoned and
  display a prominent link pointing directly to your new package, preserving your project's
  discovery footprint.
```

### Acceptance Criteria
- `LICENSE` contains Apache 2.0 text
- `composer.json` `"license"` is `"Apache-2.0"`
- All per-file MIT license blocks removed
- README and CHANGELOG include the v1 relicense note
- `docs/planning/release-task-lists.md` has remote URL update checklist item
- `docs/planning/packagist-rebrand-guide.md` created
- `composer review` green

---

## After Slice 20

Sequence: **Slice 20.5** → Slice 21 → Slice 22 → Step 17 → Step 18 → rename (user-driven)

### Slice 20.5 — Enum Wiring & Hydrator Audit (next after Slice 20)

See `docs/planning/v1-release-roadmap.md` § Slice 20.5 for full scope. Summary:

1. **Hydrator audit first**: confirm `Hydrator` handles backed enum properties (check `User::setStatus(?UserStatus)` is actually hydrated correctly from a JSON string). Fix Hydrator if needed.
2. **Wire orphaned enums**: `CollaborationRole` → `Collaboration::setRole`, `SharedLinkAccess` → `SharedLink`, evaluate `BoxItemType`.
3. **Create missing enums**: `CollaborationStatus` (accepted/pending/rejected), `FolderSyncState` (synced/not_synced/partially_synced).
4. **Wire new enums**: `Collaboration::setStatus`, `Folder::buildFolderUpdate` — replace `in_array` guards.
5. Tests, migration guide entry, `composer review`.

---

## After Slice 20

Sequence: **Slice 20.5** → Slice 21 → Slice 22 → Step 17 → Step 18 → rename (user-driven)

### Slice 20.5 — Enum Wiring & Hydrator Audit (next after Slice 20)

See `docs/planning/v1-release-roadmap.md` § Slice 20.5 for full scope. Summary:

1. **Hydrator audit first**: confirm `Hydrator` handles backed enum properties (verify `User::setStatus(?UserStatus)` is correctly hydrated from a JSON string). Fix Hydrator if needed.
2. **Wire orphaned enums**: `CollaborationRole` → `Collaboration::setRole`, `SharedLinkAccess` → `SharedLink`, evaluate `BoxItemType`.
3. **Create missing enums**: `CollaborationStatus` (accepted/pending/rejected), `FolderSyncState` (synced/not_synced/partially_synced).
4. **Wire new enums**: `Collaboration::setStatus`, `Folder::buildFolderUpdate` — replace `in_array` guards.
5. **`PathCollection` DTO**: Box API confirmed — `path_collection` is `{ total_count, entries: [mini-folder] }`. Create DTO, narrow `File::setPathCollection`, update Folder if applicable.
6. **`SharedLink` array narrowing**: after Hydrator confirmation, narrow `File::setSharedLink` to `?SharedLink`.
7. Tests, migration guide entry, `composer review`.

---

## After Slice 22

- Step 17: v1 Release Readiness (docs, changelog, `composer.json` version bump, security scan)
- Step 18: Documentation Cleanup and Organization
- Package/repo rename (user-driven — user will ping when ready)

---

## Resolved Questions (do not re-open)

All Slice 19 resolved questions carry forward — see `current-handoff-summary.md`.

**`@package`/`@subpackage`**: Remove entirely; do not replace with `@psalm-package`/`@psalm-subpackage`. Namespaces serve this purpose. `@psalm-package` is a Psalm visibility modifier, not a grouping tag.

**File-level license headers**: Remove entirely. Apache 2.0 does not require per-file notices. Root `LICENSE` file is sufficient.

---

## Acceptance Criteria for Slice 20

- All Slice 20 items complete or explicitly deferred with rationale
- `composer review` green
- Migration guide updated for any breaking API changes (BoxClientFactory move)
