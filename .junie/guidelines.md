# Junie Project Guidelines

You are working on `chancegarcia/box-api-v2-sdk`, a PHP SDK for Box API v2024.0 workflows. Treat this project as a library/SDK boundary layer, not an application. Changes should preserve public API stability where practical, improve type safety, and support the ongoing v0.11-to-v1.0 transition.

Project context:
- When updating `CHANGELOG.md`, always follow the instructions in `docs/changelog-prompt.md`. Treat that file as the source of truth for changelog structure, tone, comparison-base selection, and content-selection rules. Do not hand-edit changelog entries using ad hoc rules unless the user explicitly overrides the changelog prompt.
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
- Prefer strict, explicit types for parameters, return values, and properties.
- Preserve backward compatibility unless the task explicitly asks for a breaking change.
- When changing public behavior, update or add tests.
- Avoid broad rewrites. Make focused, incremental changes.
- Do not introduce global state unless absolutely necessary.
- Do not commit generated files, caches, logs, temp files, or vendor changes.
- Never hard-code real credentials, tokens, auth codes, file paths containing user-specific secrets, or Box account data. Use placeholders in examples and tests.
- Prefer small, composable services and interfaces over large procedural additions.

Architecture guidelines:
- Prefer the newer flattened namespace structure for new code, such as:
    - `Box\Client`
    - `Box\File\...`
    - `Box\Folder\...`
    - `Box\User\...`
    - `Box\Group\...`
    - `Box\Event\...`
    - `Box\Connection\...`
    - `Box\Mapper\...`
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

Static analysis and style:
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

Deprecation policy:
- New code should prefer the v0.11/v1.0-style APIs.
- Do not remove legacy compatibility unless explicitly requested.
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

When uncertain:
- Ask for clarification before making large public API or architectural changes.
- Prefer preserving existing behavior.
- Prefer adding a small test that documents current expected behavior before refactoring.
- If Box API behavior is ambiguous, consult the official Box API documentation and keep SDK behavior conservative.