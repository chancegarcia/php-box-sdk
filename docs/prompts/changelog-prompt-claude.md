# Changelog Guidance — Claude Code

This is the Claude Code CLI equivalent of `changelog-prompt.md`. The content rules, structure,
tone, and audience are identical to the Junie version. Only the process and interaction model
differ: Claude runs git commands directly, reads the existing changelog, and follows the
describe-approve-execute pattern before writing anything.

---

## When to use this

Use this guidance whenever asked to update `CHANGELOG.md` — for a new release, an unreleased
section, or a changelog review pass.

---

## Before writing anything: describe the plan

Before editing `CHANGELOG.md`:
1. Run the git commands below to determine the comparison base.
2. Summarize the comparison base chosen and the top changes found.
3. Describe the intended section structure (which sections will change and what will be added).
4. Wait for human approval before writing to disk.

---

## Determine the comparison base

**If the human specifies a previous release tag**: use it directly.

**If not specified**: find the latest suitable reachable release tag automatically.

```bash
git rev-parse --abbrev-ref HEAD
git tag -l --sort=-v:refname
```

- Keep only pure `major.minor.patch` or `vmajor.minor.patch` tags. Ignore all others (suffixes,
  pre-release labels, branch names, environment labels, etc.).
- From the sorted filtered list, choose the first tag reachable from the current branch.
- If none is reachable from the current branch, apply the same logic to `main`.
- Never use unsorted `git tag --list` as the selection basis.

**Then compare**:

```bash
git log --oneline <selected-tag>..HEAD
```

---

## Determine the release heading

- If a release tag is provided (e.g., `v1.0.0`), use it as the `## v1.0.0` heading.
- If no tag is specified, use `## Unreleased`.

---

## Project context

- Open source PHP SDK / client library for the Box API.
- Audience: developers who install, upgrade, configure, or integrate the SDK.
- Write for someone deciding whether to upgrade a production dependency — make clear what could
  break, what improved, and what they must do next.

---

## Required output structure

```
## <version or Unreleased>

### Summary

### Developer Details

### Breaking Changes   (only if there are real breaking changes)

### Migration Notes    (only if callers must change code, config, or dependencies)
```

---

## Section rules

### Summary
- **3–6 bullets only.**
- Plain English. High-level. No deep technical jargon.
- Explain what changed and why a library consumer should care.
- Prefer readable prose; save code names for Developer Details.

### Developer Details
- More technical, still concise.
- Group by theme, not by file or commit.
- Include: public API additions/removals/renames, auth/token changes, HTTP/transport behavior,
  configuration, dependency changes, bug fixes with visible impact, documentation that
  materially helps users upgrade.
- Do not dump commits or file-by-file changes.
- Prefer fenced code blocks for concrete usage examples over inline code.

### Breaking Changes
- Include only if the diff introduces actual breaking behavior.
- State what will fail, stop compiling, or behave differently.
- Name the affected APIs, methods, classes, configuration options, or workflows.
- Use labeled `Before` / `After` fenced code blocks when showing usage changes.

### Migration Notes
- Include only when callers must change code, imports, configuration, or dependencies.
- Make each note actionable.
- Use fenced code blocks for examples. Label `Before` and `After`.
- If no migration is required, omit the section entirely.

---

## Content selection rules

- Include only changes visible, integration-relevant, compatibility-relevant, or important for
  safe upgrades.
- Prefer the top 5–10 most important changes — not an exhaustive catalog.
- Merge related low-level changes into one higher-level bullet when that improves readability.
- If multiple changes tell the same story, combine them.
- If a change is minor, internal, or refactoring-only, omit it.
- Do not include internal implementation details unless they affect public behavior,
  configuration, debugging, or maintenance.
- Avoid speculation. If a change is not clearly supported by the diff, omit it.

---

## Code example rules

- Prefer fenced code blocks with the correct language identifier (`php`, `bash`, `json`, etc.).
- Use tilde fences (`~~~~`) when needed to avoid nested backtick issues.
- Use inline code only for short identifiers, method names, class names, option names,
  file paths, or environment variable names.
- Label old/new pairs as `Before` / `After`.
- Keep examples minimal, focused, and copy-pasteable.
- Never include real secrets, credentials, tokens, API keys, or private data. Use placeholders.

---

## Tone

- Professional, direct, concise, easy to scan.
- Developer-focused and upgrade-oriented.
- No marketing fluff, no vague filler.
- No "improved stability" unless you can say what actually improved.
- No exaggerated claims.

---

## Final editorial standard

- If a developer upgrading the SDK can't act on it or understand why it matters — leave it out.
- If a package consumer would not notice the change in real usage — leave it out.
- If someone must know it to install, upgrade, integrate, debug, or maintain safely — say it plainly.
- If an example makes a migration safer or clearer, include it as a fenced code block.
