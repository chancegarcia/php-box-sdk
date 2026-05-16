# Next Session Plan

**Updated**: 2026-05-16 15:28 (America/Indiana)
**Branch**: `release-v1.0.0`

---

## Start Here

Steps 17 and 18 are complete. The codebase is release-ready. **The only remaining work is tagging v1.0.0.**

---

## v1.0.0 Release Sequence (Human-Driven)

```bash
git tag v1.0.0
git push origin v1.0.0
```

See `docs/planning/packagist-rebrand-guide.md` for the rename/Packagist steps after tagging. Do not prompt about rename.

---

## Likely Next Session: AI Workflow Refinement

Copy `docs/prompts/ai-workflow/` to a dedicated workflow repo, then open Claude Code in that directory with this opening prompt:

> "Read `workflow-evolution.md` first, then the rest of the files in this directory. Your job is to polish these into a clean, model-agnostic, language-agnostic set of workflow templates that reflect the evolved workflow described in the evolution doc. Retire `multi-agent-collaboration.md` and `multi-agent-topic-handoff-template.md` — the two-agent model is gone."

---

## Possible Next Sessions: Early v1.1

If returning to box-sdk work before the workflow session, likely candidates:

- **409 `item_name_in_use`**: Add a named constant or helper on `ConflictException`. Low effort.
- **Retry / exponential backoff**: Implement retry loop for rate-limit (429) and transient errors; expose backoff customization (max attempts, base delay, multiplier). `Retry-After` header already parsed on `BoxResponseException`.
- **API coverage expansion**: Pick an unimplemented endpoint family from `docs/audits/api-coverage-matrix.md` and implement it. Comments or Tasks are good candidates for early v1.1.

---

## Deferred / Post-v1 (do not open unless asked)

- Array generic annotation pass (`@param array` → `list<T>` / `array<K,V>`).
- `@throws` completeness pass.
- Property hooks (PHP 8.4).
- Docblock consistency pass.
- Full endpoint parity (tracked in coverage matrix).
