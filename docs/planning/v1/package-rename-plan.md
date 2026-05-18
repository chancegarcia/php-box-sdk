# V1 Package and Repository Rename Plan

## 1. Summary

- **Goal**: Rename the Composer package and GitHub repository as part of the final v1.0 stable release to better reflect the project's identity and future.
- **GitHub Repository**: The existing repository will be renamed from `box.net-v2api-sdk` to `php-box-sdk`.
- **Composer Package**: The package name will change from `chancegarcia/box-api-v2-sdk` to `chancegarcia/php-box-sdk`.
- **Timing**: The rename will occur immediately before or as part of the final v1.0.0 stable tag and push.
- **PHP Namespaces**: No PHP namespace changes will occur. The `Box\` namespace remains the standard.
- **Old Package Status**: `v0.11.x` is the final transition release series for the old package name.
- **Packagist Strategy**: The old package (`chancegarcia/box-api-v2-sdk`) will be abandoned on Packagist with a replacement suggestion pointing to `chancegarcia/php-box-sdk`.

## 2. Pre-Rename Readiness Checklist

- [ ] Confirm `chancegarcia/php-box-sdk` is available or already reserved on Packagist.
- [ ] Confirm GitHub repository rename target `php-box-sdk` is available under the `chancegarcia` account.
- [ ] Confirm GitHub admin access for repository rename.
- [ ] Confirm Packagist maintainer access for publishing/updating the new package metadata.
- [ ] Confirm Packagist maintainer access for abandoning the old package.
- [ ] Confirm `v0.11` is documented as the final transition release for the old package in `CHANGELOG.md` and `README.md`.
- [ ] Confirm v1 public API and migration docs are stable enough for final-release branding.
- [ ] Confirm the v1 release tracker (`v1-release-roadmap.md`) includes the package/repo rename step.
- [ ] Confirm no PHP namespace changes are included in the package/repo rename.
- [ ] Confirm no private/downstream names, credentials, account identifiers, or private implementation details are present in public docs.
- [ ] Confirm public-facing documentation updates are held until the new package/repo path is usable.

## 3. Composer Package Rename Checklist

- [ ] Update `composer.json` metadata to use `"name": "chancegarcia/php-box-sdk"` at the final-release rename point.
- [ ] Validate package description, keywords, homepage, and support URLs in `composer.json` to reflect the new GitHub URL.
- [ ] Confirm `autoload` and `autoload-dev` remain unchanged (preserving `Box\` namespace).
- [ ] Confirm Composer install examples in docs use `composer require chancegarcia/php-box-sdk` only after the package is installable.
- [ ] Confirm Packagist displays the correct package name and renamed GitHub repository URL.
- [ ] Confirm old Composer package (`chancegarcia/box-api-v2-sdk`) is abandoned on Packagist with replacement suggestion `chancegarcia/php-box-sdk`.
- [ ] Confirm old package documentation states that `v0.11` was the final transition release.
- [ ] Verify a clean install in a temporary Composer project using the new package name.
- [ ] Run `composer validate` to ensure `composer.json` integrity.

## 4. GitHub Repository Rename Checklist

- [ ] Rename the existing GitHub repository `box.net-v2api-sdk` to `php-box-sdk` in Repository Settings.
- [ ] Verify GitHub redirects from `https://github.com/chancegarcia/box.net-v2api-sdk` to `https://github.com/chancegarcia/php-box-sdk`.
- [ ] Update repository description, homepage, and topics in the GitHub "About" sidebar.
- [ ] Review branch protection rules to ensure they persisted through the rename.
- [ ] Review GitHub Actions workflows (`.github/workflows/`) for any hardcoded repository paths.
- [ ] Update badges in `README.md` to point to the new repository paths.
- [ ] Review release links and generated changelog links for correctness.
- [ ] Review issue templates and pull request templates (`.github/ISSUE_TEMPLATE/`, etc.).
- [ ] Review GitHub Pages settings if applicable.
- [ ] Review deploy keys, secrets, and environments for any name-based dependencies.
- [ ] Confirm Packagist webhook/integration still works after the repo rename (may require a manual update on Packagist).
- [ ] Confirm old clone URLs continue to redirect.
- [ ] Confirm final `v1.0.0` tag/release points to the renamed repository.

## 5. Documentation Update Checklist

Documentation updates must happen **last**, as part of the rename process, once the new package/repo path is available.

- [ ] Update `README.md` install command: `composer require chancegarcia/php-box-sdk`.
- [ ] Update `README.md` badges (Build, License, Packagist, etc.).
- [ ] Update `README.md` repository and Packagist links.
- [ ] Add a prominent migration note to `README.md` regarding the rename.
- [ ] Update `docs/` installation examples and programmatic usage guides.
- [ ] Update `v1-release-roadmap.md` release blockers to reflect rename complete.
- [ ] Update `../roadmap.md` references to the package name.
- [ ] Update `CONTRIBUTING.md` if it contains repository links.
- [ ] Update `CHANGELOG.md` for the `v1.0.0` entry to highlight the rename.

### Documentation-Drift Check
- [ ] Search entire codebase for `box-api-v2-sdk` and replace with `php-box-sdk`.
- [ ] Search entire codebase for `box.net-v2api-sdk` and replace with `php-box-sdk`.
- [ ] Confirm old names are retained only where intentional (e.g., migration notes).
- [ ] Confirm documentation explicitly states that PHP namespaces (`Box\`) have **not** changed.
- [ ] Confirm documentation says `v0.11` was the final transition release for the old package.
- [ ] Confirm documentation does not publish non-working install instructions before Packagist is ready.

## 6. Final V1 Stable Release Sequencing

1. Finish all v1 API and architecture work (Phases 6-19 of the Implementation Checklist).
2. Finalize v1 migration documentation content, keeping placeholders for the new package/repo names.
3. Confirm package/repo rename access and permissions on GitHub and Packagist.
4. Reserve or verify availability of `chancegarcia/php-box-sdk` on Packagist.
5. Prepare the final release branch (e.g., `release/v1.0.0`).
6. Run `composer review` to ensure all tests, linting, and analysis pass.
7. **Rename the existing GitHub repository** to `php-box-sdk`.
8. Update Packagist integration/webhook for the renamed repository to ensure sync.
9. **Update `composer.json`** to name `chancegarcia/php-box-sdk` and update URLs.
10. Update all public documentation, badges, and links in the codebase.
11. **Commit and push** these changes to the renamed repository.
12. **Abandon the old package** (`chancegarcia/box-api-v2-sdk`) on Packagist with the replacement suggestion.
13. Verify clean install: `composer require chancegarcia/php-box-sdk` in a new directory.
14. **Tag and push `v1.0.0`**.
15. Publish GitHub release notes, highlighting the rename and the "no namespace change" policy.
16. Announce the release and migration instructions via appropriate channels.
17. Monitor Packagist, GitHub redirects, and CI for 24-48 hours.

## 7. Validation Checklist

- [ ] `composer validate`
- [ ] `composer dump-autoload`
- [ ] `composer test`
- [ ] `composer analyse`
- [ ] `composer cs:check`
- [ ] If style issues are found, run `composer cs:fix`, then re-run checks.
- [ ] **Verify clean install** in a temporary project:
    - `composer require chancegarcia/php-box-sdk`
- [ ] Verify Packagist package page points to the renamed GitHub repository.
- [ ] Verify old package page shows "Abandoned" with a link to the new one.
- [ ] Verify GitHub release links and tag links work.
- [ ] Verify old GitHub repository URL (`box.net-v2api-sdk`) redirects correctly.
- [ ] Verify README badges resolve and show correct status.
- [ ] Verify no accidental namespace changes (e.g., `Box\` should still be `Box\`).

## 8. Risk Register

| Risk | Mitigation |
| :--- | :--- |
| `chancegarcia/php-box-sdk` is taken on Packagist | Verify availability early; reserve if possible. |
| GitHub redirects fail for some users | Document the new URL clearly; ensure all internal links are updated. |
| Packagist webhook breaks | Manually trigger a "Force Update" on Packagist after rename and re-configure webhook. |
| Users miss the rename and stay on `v0.11` | Use "Abandoned" status on Packagist to trigger CLI warnings during `composer update`. |
| Documentation update drift | Use a checklist and global search to ensure all references are updated simultaneously. |
| CI permissions/secrets break | Check GitHub Actions settings immediately after rename. |
| Users expect namespace changes | Explicitly document "No Namespace Changes" in README, Release Notes, and Migration Guide. |

## 9. Rollback / Recovery Plan

- **GitHub Rename**: GitHub allows renaming back to the original name if the old name hasn't been taken.
- **Packagist Mistake**: If `composer.json` has errors, push a fix immediately. Packagist allows updating metadata by pushing new tags/commits.
- **Broken Redirects**: If GitHub redirects fail, update all known external links manually and contact GitHub support if necessary.
- **Abandoned Status**: "Un-abandoning" a package is possible on Packagist if done in error.
- **Failed Clean Install**: If the new package won't install, check Packagist sync status and `composer.json` requirements/stability settings.

## 10. Communication Checklist

- [ ] **README Migration Note**: High-visibility section at the top of `README.md`.
- [ ] **Changelog Entry**: Detailed entry in `CHANGELOG.md` under `v1.0.0`.
- [ ] **V1 Upgrade Guide**: Section in `docs/v1-migration.md` (or similar) about the name change.
- [ ] **GitHub Release Note**: Summarize the rename and emphasize the stable `Box\` namespace.
- [ ] **Packagist Note**: Replacement suggestion provided during abandonment.
- [ ] **Explicit Install Command**: Always show `composer require chancegarcia/php-box-sdk`.
