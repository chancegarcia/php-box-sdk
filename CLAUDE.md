# CLAUDE.md — box-api-v2-sdk

## Project
PHP 8.4+ SDK for the Box API v2. Repository: `chancegarcia/box-api-v2-sdk`.
Currently working toward the **v1.0.0 release** on branch `release-v1.0.0`.

## Workflow
This project uses **Claude Code CLI** for implementation.

- **Claude's role**: Describe the plan in chat → human approves → Claude executes directly.
- **Human's role**: Review the plan, approve, review diffs, commit completed slices, approve step transitions.
- **All commits** are made by the human reviewer, never by Claude.
- **No prompt MD files**: Plans are described in chat and executed immediately. Prompt files are not written to disk unless explicitly requested.
- Step transition requires explicit human approval — do not begin a new step or slice without it.

## Key Docs to Read First
- `docs/ai/current-handoff-summary.md` — current state and active slice
- `docs/planning/v1-release-roadmap.md` — step and slice tracker with decisions
- `docs/prompts/ai-workflow/single-repository-workflow.md` — workflow rules

## Validation Commands
```
composer test          # PHPUnit
composer analyse       # PHPStan (level 0)
composer cs:check      # PHP_CodeSniffer
composer lint          # PHP syntax check
composer review        # all of the above
```
Always run `composer review` after any implementation slice before confirming completion.

## Timestamps
Use `America/Indiana/Indianapolis` local time for all doc timestamps.
```bash
TZ="America/Indiana/Indianapolis" date "+%Y-%m-%d %H:%M:%S"
```

## Current Status (as of 2026-05-14)
- **Slices complete**: 15.1, 15.2, 15.3, 15.4, 15.4.1, 15.4.2, 15.4.3, 15.4.4, 15.5, 15.6, 16
- **Next slice**: 17 — v1 Release Readiness
- **Upcoming**: 17, 18 (docs cleanup)
- **Test baseline**: 330 tests, 898 assertions

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
