# Junie Project Guidelines

You are working on `chancegarcia/box-api-v2-sdk`, a PHP SDK for Box API v2024.0 workflows. Treat this project as a library/SDK boundary layer, not an application. Changes should preserve public API stability where practical, improve type safety, and support the ongoing v0.11-to-v1.0 transition.

Project context:
- When updating `CHANGELOG.md`, always follow the instructions in `docs/prompts/changelog-prompt.md`. Treat that file as the source of truth for changelog structure, tone, comparison-base selection, and content-selection rules. Do not hand-edit changelog entries using ad hoc rules unless the user explicitly overrides the changelog prompt.
- Language/runtime: PHP 8.4 or higher.
- Package namespace: `Box\`, PSR-4 autoloaded from `src/`.
- Tests namespace: `Box\Tests\`, PSR-4 autoloaded from `tests/`.
- Main tooling:
    - PHPUnit 10.5
    - PHP_CodeSniffer with PSR-12
    - PHPStan
    - Composer scripts:
        - `composer lint`
        - `composer cs:check`
        - `composer cs:fix`
        - `composer analyse`
        - `composer test`
        - `composer review`
- Key dependencies:
    - Symfony Console / Dotenv / HttpFoundation
    - Guzzle 7
    - PSR-3 logging
    - PSR HTTP Message / Client interfaces
    - Doctrine Collections
    - Monolog

General development rules:
- Write PHP 8.4-compatible code only. Do not introduce features requiring newer PHP versions.
- Follow PSR-12 formatting.
- Always use PHP short array syntax `[]` for array literals. Legacy `array(...)` syntax must not be used.
- Use Yoda conditions where practical, such as to help prevent accidental assignments, but do not prefer them when they reduce readability.
- Prefer strict, explicit types for parameters, return values, and properties.
- Preserve backward compatibility unless the task explicitly asks for a breaking change.
- When changing public behavior, update or add tests.
- Avoid broad rewrites. Make focused, incremental changes.
- Do not introduce global state unless absolutely necessary.
- Do not commit generated files, caches, logs, temp files, or vendor changes.
- Never hard-code real credentials, tokens, auth codes, file paths containing user-specific secrets, or Box account data. Use placeholders in examples and tests.
- Prefer small, composable services and interfaces over large procedural additions.

Architecture guidelines:
- Final v1 primary resource classes must use the flat `Box\Resource\{ResourceName}` pattern.
- Do not introduce accidental double/nested primary resource names such as `Box\Resource\File\File`, `Box\Resource\Event\Event`, or `Box\Resource\SharedLink\SharedLink`.
- Nested resource namespaces are allowed only for subordinate/supporting types when explicitly justified (e.g., collections, entries, sources, permissions, or nested value-object-like types).
- When introducing or moving resources, document any intentional nested namespace exception in the relevant planning/audit docs.
- Before completing a resource namespace slice, search for accidental double/nested primary resources and confirm final public class names match the tracker.
- Prefer the newer flattened namespace structure for new code, such as:
    - `Box\Client`
    - `Box\Resource\File` (Primary resource)
    - `Box\Resource\Folder` (Primary resource)
    - `Box\User\...` (Note: User resource is `Box\Resource\User`)
    - `Box\Group\...` (Note: Group resource is `Box\Resource\Group`)
    - `Box\Event\...` (Note: Event resource is `Box\Resource\Event`)
    - `Box\Connection\...`
    - `Box\Mapper\...`
- For detailed architecture rules and resource namespace inventory, refer to:
    - `docs/planning/10-resource-namespace-interface-rationalization.md`
    - `docs/audits/10-resource-namespace-interface-audit.md`
- Avoid adding new code under legacy `Box\Model\...` namespaces unless specifically maintaining a compatibility alias.
- Legacy aliases may exist to support older consumers. If adding or modifying aliases:
    - Keep them minimal.
    - Mark them deprecated.
    - Point users to the new flattened namespace.
    - Do not place business logic in alias classes/files.
- Keep model, DTO, connection, transport, mapper, and service responsibilities separate.
- `Client` should remain a high-level SDK facade. Avoid stuffing low-level HTTP or hydration logic directly into it when a dedicated service/transport/mapper is more appropriate.
- Preserve the transition-layer intent: v0.11 bridges legacy v0.10 usage and future v1.0 design.

Models and DTOs:
- Prefer typed models and DTOs over raw associative arrays for new functionality.
- New model setters should not be fluent. Setters should return `void`.
- Do not add chained setter usage in source, tests, examples, or docs.
- Box IDs should generally be treated as `string|int` where the existing API supports both, because Box IDs may be large numeric strings.
- For nested Box resources, prefer typed resource objects. Only accept legacy arrays where existing transition behavior requires it.
- If accepting legacy arrays for backward compatibility, keep the behavior explicit and documented in code/tests when relevant.
- Keep hydration behavior centralized in mapper/hydrator classes rather than duplicating recursive mapping logic across models.

Collections:
- Prefer Doctrine Collections for new collection work.
- Do not expand the old custom collection layer unless maintaining compatibility.
- When returning or accepting collections, use appropriate Doctrine collection interfaces where practical.

HTTP, transport, and responses:
- Preserve the pluggable HTTP transport design.
- Keep HTTP execution logic in transport/connection classes rather than models or DTOs.
- Prefer PSR-compatible abstractions where already used by the project.
- Preserve and improve robust error handling:
    - Throw descriptive SDK exceptions.
    - Include response/context information where existing exception types support it.
    - Avoid swallowing API errors silently.
- Do not make real network calls in unit tests. Mock or fake transport/connection behavior.

Logging:
- Use PSR-3 logging abstractions.
- Do not introduce direct `echo`, `var_dump`, `print_r`, or ad hoc logging in library code.
- CLI commands may write to console output, but SDK internals should use logger abstractions.

CLI guidelines:
- The CLI lives under `bin` and `src/Command`.
- Keep CLI commands thin where possible.
- Move reusable SDK behavior into services/classes under `src`.
- CLI output should avoid leaking secrets. Mask tokens and credentials.

Testing guidelines:
- Add or update PHPUnit tests for behavioral changes.
- Prefer isolated unit tests over tests requiring real Box API access.
- Use mocks/fakes/stubs for HTTP transports and API responses.
- Tests should be deterministic and not depend on network, local user files, or real credentials.
- Since PHPUnit is configured to fail on deprecations, notices, and warnings, avoid test code that triggers them unless the test intentionally asserts deprecation behavior and handles it safely.
- Run the most relevant checks after changes. For broad changes, run `composer review`.
- After completing any task or slice, write the final task summary by replacing the full contents of `var/tmp/last-task-summary.md`.
    - Always overwrite the file on every task; do not use create-only behavior. Replace its entire contents.
    - If `var/tmp/` does not exist, create the directory if appropriate, but do not remove `var/tmp/.gitkeep`.
    - The persisted task summary should match the final response summary as closely as practical.
    - Persisted summaries must be plain UTF-8 Markdown text without null bytes, control characters, corrupted class names, or binary content.
    - If a generated summary contains corrupted text, rewrite it before reporting completion.
    - If the persisted summary includes additional detail, it must not contradict the final response.
    - Prefer making `var/tmp/last-task-summary.md` the canonical detailed review summary.
    - The final response may be concise, but it should mention that the detailed summary was written to the file.
    - The persisted summary must include, where applicable:
        - Summary
        - Changes
        - Verification
        - Notes
        - Follow-ups
    - Keep the file redacted and free of secrets, credentials, tokens, private account IDs, local sensitive paths, and downstream/private implementation details. Redact any sensitive output.
    - Do not treat `var/tmp/last-task-summary.md` as a source artifact to commit.
    - Do not remove `var/tmp/.gitkeep`.
- **Periodic Handoff Summaries**: During long-running initiatives or before ending a session, produce a handoff summary to preserve context.
    - **Frequency**: Every 2–3 completed slices, before switching major initiatives, or before ending a long session.
    - **Template**: Use `docs/prompts/ai-workflow/handoff-summary-template.md`.
    - **Storage**: Paste into the chat or write to `var/tmp/ai-handoff-summary.md`.
    - **No Commit**: Do not commit generated handoff files in `var/tmp/`.

Static analysis and style:
- Avoid nested ternary operators. Use explicit branching or a named helper method when conditional logic becomes nested. Simple one-level ternaries are acceptable when they remain readable.
- Do not add custom PHPCS/PHPStan enforcement for nested ternaries unless explicitly requested; enforce this preference through review and project guidelines for now.
- Prefer `SomeClass::class` over hard-coded fully qualified class-name strings when referencing PHP classes or interfaces in code, including exception messages and logs.
- Do not apply this rule to arbitrary user-facing text, protocol strings, API type values, or external identifiers that are not PHP symbols.
- Keep PHPStan compatibility in mind, even though the current configured level is low.
- Do not add unnecessary baseline entries.
- Prefer code that would pass stricter static analysis over minimal level-0 compliance.
- Run `composer cs:check` or `composer cs:fix` for style-sensitive changes.

Documentation guidelines:
- Update README and docs when changing public APIs, workflows, CLI behavior, configuration, or migration guidance.
- Keep examples compatible with PHP 8.4.
- Use new flattened namespaces in documentation unless documenting migration from legacy namespaces.
- Do not show fluent setter chains.
- Use placeholder credentials such as `YOUR_CLIENT_ID`, `YOUR_CLIENT_SECRET`, `YOUR_ACCESS_TOKEN`, and `YOUR_AUTH_CODE`.
- Keep migration notes clear about deprecations and future v1.0 removals.

Security and credentials:
- Never expose real tokens, client secrets, refresh tokens, auth codes, account IDs, or personal paths.
- Do not read from `.env` in tests unless the test is explicitly about env config and uses controlled fixtures/placeholders.
- Avoid committing files from `var/`, temporary upload fixtures, logs, or local scratch files.
- Be careful with OAuth examples: always use placeholders and explain secure storage where relevant.

Deprecation and v1 Removal Policy:
- New code should prefer the v0.11/v1.0-style APIs.
- v0.11 transition work: preserve backward compatibility where practical.
- v1 cutover work: intentionally remove legacy pre-v1 / v0.x architecture and APIs, with tests and migration docs. v1 is the clean target architecture; this does not mean removing newly established v1 APIs. The goal is to completely remove the old legacy API architectural layer as part of the v1 major-version cutover.
- Legacy pre-v1 architecture/API for removal includes:
    - legacy `Box\Model\...`-based architecture,
    - legacy model traits (e.g., trait-based mapping/hydration),
    - old `mapBoxToClass`-centered flows,
    - legacy service base patterns conflicting with the hardened foundation,
    - old custom collection layers where replaced,
    - compatibility aliases that are no longer part of v1,
    - fluent setter expectations in v1-facing models/DTOs,
    - legacy interfaces superseded by flattened resources,
    - duplicated or ad hoc hydration/mapping behavior.
- If behavior is deprecated:
    - Keep the replacement obvious.
    - Add or preserve a deprecation message.
    - Avoid introducing deprecations that make the PHPUnit suite fail unintentionally.
- Deprecation messages should name both the old API and the recommended replacement.

Preferred workflow for tasks:
1. Inspect the relevant files and nearby tests before editing.
2. Identify whether the change affects public API, docs, tests, or migration behavior.
3. Make the smallest coherent change.
4. Add or update tests for changed behavior.
5. Run targeted checks when possible:
    - Syntax: `composer lint`
    - Tests: `composer test` or targeted PHPUnit
    - Style: `composer cs:check`
    - Static analysis: `composer analyse`
6. For larger changes, run `composer review`.
7. Summarize what changed, what was tested, and any follow-up risks.
- Do not modify `vendor/` or Composer lock files unless dependency changes are explicitly requested.
- Prefer adding tests under `tests/` that mirror the affected source namespace or behavior.
- When editing docs, keep examples free of real credentials and avoid environment-specific local paths.

Code review priorities:
- Public API compatibility
- Correct Box API semantics
- Type safety
- Clear error handling
- No leaked secrets
- No real network calls in tests
- PSR-12 style
- Adequate tests and documentation
- Avoiding unnecessary architectural churn

## File Moves, Renames, and Git History

When moving or renaming files, especially during documentation reorganizations, preserve file history and ensure review clarity:
- Prefer `git mv old/path new/path` when moving tracked files.
- If files were already moved manually:
    - Stage the new file path with `git add`.
    - Stage the old removed path with `git rm old/path`.
- Verify rename detection with `git status` or `git diff --cached --summary`.
- Avoid leaving moved files as unrelated add/delete pairs when the intent is a rename.
- Check for stale links/references after moving documentation files.
- Avoid staging unrelated generated/cache/temp/vendor files.

## Code Style and Validation

Use Composer scripts as the source of truth for project validation and style checks. Do not use direct `vendor/bin/phpunit`, `vendor/bin/phpstan`, `vendor/bin/phpcs`, or `vendor/bin/phpcbf` as substitutes for Composer scripts unless explicitly requested or investigating a tooling issue.

Required validation commands:

- `composer test`
- `composer analyse`
- `composer cs:check`
- `composer lint`
- `composer review`
- `composer dump-autoload`

Required automatic code style fix command:

- `composer cs:fix`

If code style output says that PHPCBF can fix violations automatically, run `composer cs:fix`, then rerun `composer cs:check`.

Final summaries should report Composer commands and results. Direct `vendor/bin/phpcs` or `vendor/bin/phpcbf` usage is allowed only when explicitly requested by the user or when investigating a tool issue. Final reported validation must still include `composer cs:check`.

## Slice Workflow

For multi-slice initiatives such as Foundation Refinement:

- For every new step, segment, roadmap item, or initiative, begin with a tracker/plan review and refinement pass before implementation. This is required even for low-risk work. Confirm scope, slice order, dependencies, non-goals, validation expectations, and draft prompts are current before running the first implementation slice.
- Treat tracker-embedded prompts as drafts unless the user explicitly says otherwise.
- Refine each slice prompt immediately before execution.
- Execute one slice at a time.
- After a slice completes, provide the implementation output for review.
- Identify follow-ups before starting the next slice.
- If there are no follow-ups, commit the completed slice before starting the next slice.
- Do not proceed to the next slice automatically.
- Keep each slice focused and avoid pulling in unrelated future-slice scope.
- If a slice exposes a larger architectural issue, document it as follow-up unless it blocks the current slice.

## General Guidance

When uncertain:
- Ask for clarification before making large public API or architectural changes.
- Prefer preserving existing behavior.
- Prefer adding a small test that documents current expected behavior before refactoring.
- If Box API behavior is ambiguous, consult the official Box API documentation and keep SDK behavior conservative.