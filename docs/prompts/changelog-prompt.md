# Changelog Generation Prompt

User inputs:
- Release Tag: upcoming-release-tag
    - Optional.
    - Replace upcoming-release-tag with the git tag, version, or release label that should be used for the changelog heading.
    - This value represents the tag or version being used for the release changes.
    - If the value is still upcoming-release-tag, blank, missing, or otherwise not set, use Unreleased as the changelog heading.
- Previous Release: last-release-tag
    - Optional.
    - Replace last-release-tag with a specific git tag only when you want to force the changelog comparison base.
    - This value represents the starting tag for the changelog comparison.
    - If the value is still last-release-tag, blank, missing, or otherwise not set, treat it as not provided and determine the comparison base automatically using the rules below.

Project context:
- This is an open source SDK, client library, package, framework integration, or developer-facing library project.
- The changelog audience includes library users, application developers, maintainers, release reviewers, contributors, and package consumers.
- The changelog should help readers understand what changed in the public API, behavior, compatibility, installation requirements, dependency expectations, integrations, documentation, testing support, and migration path.
- Keep the prompt applicable to any open source SDK or library. Do not assume a specific programming language, package manager, vendor, framework, cloud service, or runtime unless the repository clearly shows one.

Task:
Create or update CHANGELOG.md by comparing the current codebase against the selected previous release point.

Comparison base selection:
- If Previous Release is provided and is not last-release-tag, compare against that tag.
- If Previous Release is last-release-tag, blank, missing, or otherwise not set, determine the comparison base automatically.
- To determine the latest reachable release tag from the current branch:
    - First identify the current branch.
    - List release tags with semantic version ordering using git tag -l --sort=-v:refname.
    - Consider only tags in one of these pure semantic version formats:
        - major.minor.patch, such as 1.4.2
        - vmajor.minor.patch, such as v1.4.2
    - Ignore all other tags, including tags with suffixes, prefixes other than v, missing patch versions, branch names, environment labels, prerelease labels, build metadata, or other non-release formats.
    - From the sorted candidate tags, choose the first tag that is reachable from the current branch history.
    - The chosen tag should be near the top of the sorted semantic-version tag list, but it must still be reachable from the current branch.
- If the current branch has no suitable reachable release tag, apply the same logic to main:
    - List tags with git tag -l --sort=-v:refname.
    - Keep only pure major.minor.patch or vmajor.minor.patch tags.
    - Choose the highest semantic-version tag that is reachable from main.
- Do not use unsorted git tag --list as the basis for selecting the previous release tag.
- Do not require the user to provide a tag manually.
- Clearly base the changelog only on changes introduced after the selected comparison point.
- If no suitable prior tag exists, compare against the earliest meaningful project history and note that no prior release tag was available.

Expected git command pattern:
- When Previous Release is provided, use that tag directly as the comparison base.
- When Previous Release is not provided, use the tag selection process above.
- The command sequence should be equivalent to:
    - git rev-parse --abbrev-ref HEAD
    - git tag -l --sort=-v:refname
    - git log --oneline selected-release-tag..HEAD
- For example, if the selected comparison base is v1.4.2, compare with:
    - git log --oneline v1.4.2..HEAD

Audience:
- Primary: developers who install, upgrade, configure, or integrate the SDK/library in their applications
- Secondary: non-expert readers, maintainers, contributors, release reviewers, package consumers, and technical stakeholders who need to understand compatibility, migration impact and a quick sense of what has changed

Strong opinionated writing rules:
- Be selective. Include only the changes that matter most to users, integrators, maintainers, and package consumers.
- Prefer the top 5–10 most important changes, not an exhaustive catalog.
- Merge related low-level changes into one higher-level bullet whenever that improves readability.
- Treat the changelog as practical release notes, not a code audit.
- Prioritize public API impact, runtime behavior, compatibility, migration needs, dependency implications, and developer experience over completeness.
- Avoid internal implementation details unless they clearly affect public behavior, configuration, debugging, supported platforms, package installation, generated output, integrations, performance, reliability, or maintenance.
- Avoid speculation. If a change is not clearly supported by the diff, omit it.
- Do not mention context-attachment content unless it is necessary to explain a visible or user-relevant change.
- Write as if the reader may be deciding whether to upgrade a dependency in a production application and needs to know what could break, what improved, and what they need to do next.

Required output structure:
1. # Changelog
2. ## Unreleased if Release Tag is upcoming-release-tag, blank, missing, or otherwise not set; otherwise use the provided Release Tag as the release heading
3. ### Summary
4. ### Developer Details
5. ### Breaking Changes only if there are real breaking changes
6. ### Migration Notes only if users, maintainers, or integrators need to adjust code, configuration, dependency versions, runtime versions, build steps, credentials, integration setup, or workflows

Section rules:

### Summary
- 3–6 bullets only.
- Plain English.
- High-level.
- Explain what changed and why a library user, application developer, or maintainer should care.
- No deep technical jargon unless it is unavoidable.
- Prefer readable prose to code examples in this section.

### Developer Details
- More technical, but still concise.
- Group bullets by theme, not by file.
- Include relevant changes such as:
    - public API additions, removals, renames, signature changes, or behavior changes
    - changes to request/response models, data structures, serialization, validation, parsing, or generated types
    - authentication, authorization, token handling, credential, or session behavior
    - HTTP, transport, retry, timeout, pagination, rate-limit, error handling, or logging behavior
    - configuration options, environment variables, defaults, feature flags, or initialization behavior
    - dependency changes with installation, runtime, security, compatibility, or packaging implications
    - supported language, runtime, framework, platform, or package manager version changes
    - integration changes with external APIs, services, plugins, frameworks, or adapters
    - build, packaging, distribution, release artifact, module format, autoloading, or import/export behavior
    - generated code, schema, type definition, or documentation generation changes
    - test utilities, mocks, fixtures, local development tooling, or contributor workflow changes that affect users or maintainers
    - bug fixes with visible behavioral or compatibility impact
    - performance, memory, concurrency, or reliability changes when supported by the diff
    - documentation updates that materially improve installation, upgrade, configuration, usage, or troubleshooting
- Keep this section focused on release-relevant details.
- Do not turn this into a commit dump or file-by-file summary.
- When showing concrete usage changes, prefer separate Markdown code blocks for examples instead of inline code snippets.

### Breaking Changes
- Include only if the diff introduces actual breaking behavior.
- Make the impact unmistakable.
- State what will fail, stop compiling, stop installing, or behave differently.
- Include affected APIs, methods, classes, modules, configuration options, integrations, dependency constraints, package names, import paths, commands, or workflows when known.
- Keep it short and direct.
- If showing old and new usage, use clearly labeled Before and After examples with separate fenced code blocks.

### Migration Notes
- Include only when someone must change application code, imports, configuration, dependency constraints, runtime versions, credentials, build steps, deployment setup, integration setup, generated code usage, or operational workflow.
- Make each note actionable.
- If no migration is required, omit this section entirely.
- Prefer examples when they make the required change clearer.
- For examples, use fenced code blocks whenever possible instead of putting multiple statements inline.

Example formatting rule:
- Prefer this style for command, configuration, or code examples:

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
- Use the correct language identifier for fenced code blocks when the language is known from the repository, such as `php`, `javascript`, `typescript`, `python`, `ruby`, `java`, `kotlin`, `csharp`, `go`, `rust`, `swift`, `bash`, `json`, `yaml`, `xml`, `sql`, `env`, `toml`, `ini`, or `markdown`.
- Use text when the example is language-neutral or the correct language is unclear.
- Use inline code only for short identifiers, method names, class names, option names, file paths, command names, package names, constants, environment variable names, or very short expressions.
- Do not cram multiple statements into inline code.
- If an example has both old and new usage, label them as `Before` and `After`.
- Keep examples minimal and focused on the migration or behavior change.
- Do not include speculative examples that are not supported by the diff.
- Prefer code examples that users can copy directly.
- Use Markdown tilde fences for code examples when that avoids nested backtick issues.
- Never include real secrets, credentials, tokens, API keys, account IDs, passwords, private URLs, or private customer data. Use placeholders instead.

Content selection rules:
- Include only changes that are visible, user-facing, integration-relevant, compatibility-relevant, maintenance-relevant, or important for safe upgrades.
- If multiple changes tell the same story, combine them into one bullet.
- If a change is minor, internal, refactoring-only, or too granular, leave it out.
- If the release contains many small fixes, summarize them by outcome instead of listing every patch.
- If docs changes materially help users install, upgrade, configure, integrate, or troubleshoot the library, mention them briefly.
- If dependency changes affect installation, supported platforms, runtime behavior, security posture, package resolution, compatibility, or release artifacts, mention them.
- If dependency changes are routine and have no visible impact, omit them.
- If there are no notable changes in a category, do not create that category.
- Do not describe this as a CLI, application, service, or internal tool release unless the diff clearly shows that the repository is one.
- Do not assume proprietary or internal-only usage. Write for an open source audience unless the repository clearly says otherwise.

Tone:
- Professional
- Direct
- Concise
- Easy to scan
- Developer-focused
- Upgrade-oriented
- No marketing fluff
- No vague filler
- No “improved stability” unless you can say what actually improved
- No exaggerated claims

Process:
1. Determine the changelog release heading:
    - If Release Tag is provided and is not upcoming-release-tag, use that value as the release heading.
    - If Release Tag is upcoming-release-tag, blank, missing, or otherwise not set, use Unreleased.
2. Determine the comparison base:
    - If Previous Release is provided and is not last-release-tag, use that tag.
    - If Previous Release is last-release-tag, blank, missing, or otherwise not set, find the latest suitable reachable release tag from the current branch.
    - To find the latest suitable reachable release tag:
        - Run git rev-parse --abbrev-ref HEAD to identify the current branch.
        - Run git tag -l --sort=-v:refname to list tags in descending semantic-version order.
        - Filter the tag list to pure semantic version tags only:
            - major.minor.patch
            - vmajor.minor.patch
        - Ignore all other tag formats.
        - Select the first filtered tag that is reachable from the current branch.
    - If no suitable tag is reachable from the current branch, repeat the same filtered, sorted, reachability-aware selection process for main.
    - Prefer the highest reachable semantic version from the sorted git tag -l --sort=-v:refname output.
    - Do not use unsorted git tag --list to choose the comparison base.
3. Compare the current codebase against the selected comparison base.
4. Identify only the most important visible, user-facing, compatibility, integration, upgrade, and maintenance-relevant changes.
5. Collapse similar low-level changes into a few strong bullets.
6. Draft the changelog with the structure above.
7. Save it to CHANGELOG.md at the project root.

Final editorial standard:
- If a developer upgrading the SDK/library can’t act on it or understand why it matters, leave it out.
- If a package consumer would not notice the change in real usage, leave it out.
- If someone must know it to install, upgrade, integrate, debug, maintain, or deploy safely, say it plainly.
- If an example would make a migration safer or clearer, include it as a fenced code block rather than inline code.
- Keep the final changelog focused on real project impact, not raw implementation detail.