You are working in the current project.

User inputs:
- Release tag: insert-tag-here
    - Optional.
    - Replace `insert-tag-here` with a specific git tag only when you want to force the changelog comparison base.
    - If the value is still `insert-tag-here`, treat it as not provided and determine the comparison base automatically using the rules below.

Task:
Create or update `CHANGELOG.md` by comparing the current codebase against the selected previous release point.

Comparison base selection:
- If `Release tag` is provided and is not `insert-tag-here`, compare against that tag.
- If `Release tag` is `insert-tag-here`, missing, or otherwise not provided, prefer the latest reachable git tag on the current branch.
- If the current branch has no suitable release tag, compare against the latest reachable git tag on `main`.
- If multiple tags are candidates, use the most recent version tag according to semantic version ordering when possible.
- If semantic version ordering is not possible, use the most recent tag by commit ancestry or tag date.
- Do not require the user to provide a tag manually.
- Clearly base the changelog only on changes introduced after the selected comparison point.
- If no suitable prior tag exists, compare against the earliest meaningful project history and note that no prior release tag was available.

Audience:
- Primary: SDK consumers and integrators
- Secondary: non-expert readers who need a quick sense of what changed

Strong opinionated writing rules:
- Be selective. Include only the changes that matter most to users.
- Prefer the top 5–10 most important changes, not an exhaustive catalog.
- Merge related low-level changes into one higher-level bullet whenever that improves readability.
- Treat the changelog as a release note, not a code audit.
- Prioritize impact over completeness.
- Avoid internal implementation details unless they clearly affect user behavior.
- Avoid speculation. If a change is not clearly supported by the diff, omit it.
- Do not mention context-attachment content unless it is necessary to explain a user-facing change.
- Write as if the reader may be upgrading a production integration and needs to know what could break, what improved, and what they need to do next.

Required output structure:
1. `# Changelog`
2. `## Unreleased` or the appropriate current release heading
3. `### Summary`
4. `### Developer Details`
5. `### Breaking Changes` only if there are real breaking changes
6. `### Migration Notes` only if users need to adjust code or configuration

Section rules:

### Summary
- 3–6 bullets only.
- Plain English.
- High-level.
- Explain what changed and why a user should care.
- No deep technical jargon unless it is unavoidable.
- Prefer readable prose to code examples in this section.

### Developer Details
- More technical, but still concise.
- Group bullets by theme, not by file.
- Include:
    - API or public surface changes
    - behavior changes
    - additions and removals
    - bug fixes with user impact
    - configuration changes
    - CLI changes
    - compatibility implications
- Keep this section focused on upgrade-relevant details.
- Do not turn this into a changelog dump.
- When showing concrete usage changes, prefer separate Markdown code blocks for examples instead of inline code snippets.

### Breaking Changes
- Include only if the diff introduces actual breaking behavior.
- Make the impact unmistakable.
- State what will fail or behave differently.
- Keep it short and direct.
- If showing old and new usage, use clearly labeled `Before` and `After` examples with separate fenced code blocks.

### Migration Notes
- Include only when users must change code, config, or workflow.
- Make each note actionable.
- If no migration is required, omit this section entirely.
- Prefer examples when they make the required change clearer.
- For examples, use fenced code blocks whenever possible instead of putting multiple statements inline.

Example formatting rule:
- Prefer this style for code examples:

- **Unchain Setters**: Break any chained setter calls into individual statements.
    - *Before*:

      ~~~~php
      $folder->setName('New Name')->setParentId('0');
      ~~~~

    - *After*:

      ~~~~php
      $folder->setName('New Name');
      $folder->setParentId('0');
      ~~~~

- Avoid this style when the example contains more than a trivial expression:

    - *Before*: `$folder->setName('New Name')->setParentId('0');`
    - *After*: `$folder->setName('New Name'); $folder->setParentId('0');`

Code example rules:
- Prefer fenced code blocks for examples when possible.
- Use the correct language identifier on fenced code blocks, such as `php`, `bash`, `json`, `yaml`, `xml`, or `markdown`.
- Use inline code only for short identifiers, method names, class names, option names, file paths, single constants, or very short expressions.
- Do not cram multiple statements into inline code.
- If an example has both old and new usage, label them as `Before` and `After`.
- Keep examples minimal and focused on the migration or behavior change.
- Do not include speculative examples that are not supported by the diff.
- Prefer code examples that users can copy directly.
- Use Markdown tilde fences for code examples when that avoids nested backtick issues.

Content selection rules:
- Include only consumer-visible and developer-relevant changes.
- If multiple changes tell the same story, combine them into one bullet.
- If a change is minor, internal, or too granular, leave it out.
- If the release contains a lot of small fixes, summarize them by outcome instead of listing every patch.
- If there are docs changes that materially help SDK users, mention them briefly.
- If there are no notable changes in a category, do not create that category.

Tone:
- Professional
- Direct
- Concise
- Easy to scan
- No marketing fluff
- No vague filler
- No “improved stability” unless you can say what actually improved

Process:
1. Determine the comparison base:
    - If `Release tag` is provided and is not `insert-tag-here`, use that tag.
    - If `Release tag` is `insert-tag-here`, missing, or otherwise not provided, find the latest reachable release tag from the current branch.
    - If none is suitable, find the latest reachable release tag from `main`.
    - Prefer semantic version tags over non-version tags.
2. Compare the current codebase against the selected comparison base.
3. Identify only the most important user-facing and upgrade-relevant changes.
4. Collapse similar low-level changes into a few strong bullets.
5. Draft the changelog with the structure above.
6. Save it to `CHANGELOG.md` at the project root.

Final editorial standard:
- If a reader can’t act on it or understand why it matters, leave it out.
- If a reader would not notice the change in real usage, leave it out.
- If a reader must know it to upgrade safely, say it plainly.
- If an example would make a migration safer or clearer, include it as a fenced code block rather than inline code.