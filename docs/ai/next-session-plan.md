# Next Session Plan

**Updated**: 2026-05-16 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Read `CLAUDE.md` and `docs/ai/current-handoff-summary.md`.

**Two pending items from the workflow migration session** (2026-05-16):
1. Commit the staged workflow changes (git mv of archive files + new v2.0 templates).
2. Resolve the `docs/ai/` commit state (see handoff for details).

**v1.0.0 is ready to tag** (Steps 17 and 18 complete, 372 tests passing).

---

## v1.0.0 Release Sequence (Human-Driven)

```bash
git tag v1.0.0
git push origin v1.0.0
```

See `docs/planning/packagist-rebrand-guide.md` for rename/Packagist steps. Do not prompt about rename.

---

## Workflow Migration Follow-up (local Claude can finish)

From the handoff:
1. Merge `.junie/guidelines.md` content into `CLAUDE.md` (project-specific only)
2. Terminology pass on `docs/planning/`: "slice" → "Task", "step" → "Story"
3. Update CLAUDE.md current status: correct test count (372/1002), update terminology
4. Resolve `docs/ai/` committed files (untrack session files via `git rm --cached`)

See `docs/ai-workflow/session-start.md` for the standard session startup prompt.

---

## Possible Next Sessions: Early v1.1

- **409 `item_name_in_use`**: Named constant/helper on `ConflictException`. Low effort.
- **Retry/backoff**: Implement retry loop for 429 and transient errors.
- **API coverage expansion**: Pick a family from `docs/audits/api-coverage-matrix.md`.
  Comments or Tasks are good v1.1 candidates.

---

## Deferred / Post-v1

- Array generic annotation pass (`@param array` → `list<T>` / `array<K,V>`).
- `@throws` completeness pass.
- Property hooks (PHP 8.4).
- Full endpoint parity (tracked in coverage matrix).
