# CLAUDE.md — box-api-v2-sdk

## Project
PHP 8.4+ SDK for the Box API v2. Repository: `chancegarcia/box-api-v2-sdk`.
Namespace: `Box\` from `src/`; tests: `Box\Tests\` from `tests/`.
v1.0.0 complete. Package/repo rename pending (human-managed).

## Workflow Template
- Version: 2.0
- Copied: 2026-05-16
- Customizations: PHP 8.4 SDK conventions, Box API architecture decisions, timestamp locale

## Workflow
This project uses **Claude Code CLI** for implementation (describe-approve-execute).

- **Claude's role**: Describe the plan in chat → human approves → Claude executes directly.
- **Human's role**: Review the plan, approve, review diffs, commit completed tasks, approve story transitions.
- **All commits** are made by the human reviewer, never by Claude.
- **No prompt MD files**: Plans are described in chat and executed immediately. Prompt files are not written to disk unless explicitly requested.
- Story/task transition requires explicit human approval — do not begin a new story or task without it.

## Key Docs to Read First
- `docs/ai/current-handoff-summary.md` — current state and active task
- `docs/planning/v1-release-roadmap.md` — story and task tracker with decisions
- `docs/ai-workflow/single-repository-workflow.md` — workflow rules

## Validation Commands
```
composer test          # PHPUnit
composer analyse       # PHPStan (level 0)
composer cs:check      # PHP_CodeSniffer
composer lint          # PHP syntax check
composer review        # all of the above
```
Always run `composer review` after any implementation task before confirming completion.

## Timestamps
Use `America/Indiana/Indianapolis` local time for all doc timestamps.
```bash
TZ="America/Indiana/Indianapolis" date "+%Y-%m-%d %H:%M:%S"
```

## Current Status (as of 2026-05-17)
- **v1.0.0 ready to tag** — all stories and tasks complete (through Task 22)
- **Test baseline**: 372 tests, 1002 assertions

## Key Architectural Decisions
- **Auth providers**: `OAuth2Provider` and `JwtProvider` both implement `AuthProviderInterface`.
- **Env vars**: `BOX_OAUTH_*` for OAuth2, `BOX_JWT_*` for JWT, `BOX_AUTH_MODE` for mode selection.
- **Config provider methods**: Named with provider prefix — `getOAuth2ClientId()`, `getJwtClientId()`, etc.
- **Private key handling**: `EnvConfigProvider` reads the PEM file; `JwtAuthConfig::$privateKey` is always PEM content, never a path.
- **CLI transport**: `--transport` option removed (Guzzle is the only transport). `ConnectionInterface` transport methods retained for programmatic use.
- **CLI storage**: `--storage-type pdo` or (after 15.4.1) `--storage-type filesystem` with `--storage-path`. `memory` removed from CLI.
- **No DI container**: Commands wired manually in `bin/box-sdk`.
- **No plan mode**: Use default mode for all tasks.
- **Webhook verification**: `Box\Webhook\WebhookVerifier`; signing formula `base64(HMAC-SHA256(body + timestamp, key))`; webhook CRUD management deferred to post-v1.
- **Namespace pattern**: Primary resources use flat `Box\Resource\{Name}` (e.g., `Box\Resource\File`); never double-nested primaries (e.g., `Box\Resource\File\File`). Sub-namespaces only for subordinate types (collections, entries, permissions).
- **Box IDs**: Treat as `string|int` where the Box API supports both — Box IDs may be large numeric strings.
- **Hydration**: Centralized in mapper/hydrator classes; do not duplicate mapping logic across models.
- **CLI**: Keep commands thin; move SDK logic into services. Mask tokens and credentials in all command output.
