# PSR Compliance Assessment

**Status**: Historical / Context Only (Assessed 2026-04-30). See [v1.0 Planning](v1-planning.md) for current implementation goals.

- **Date of Assessment**: 2026-04-30
- **Project**: Box PHP SDK (chancegarcia/box-api-v2-sdk)
- **Assessor**: Junie (Autonomous Agent)

## Executive Summary
The codebase is currently in a transition phase (v0.11) towards a modernized v1.0 architecture. While it follows several PSR standards (notably PSR-4 and PSR-3), it has significant technical debt regarding PSR-12 coding style. Interoperability with HTTP-related PSRs (7, 17, 18) is partially present but requires further decoupling to be fully compliant and useful for users who wish to bring their own implementations.

## Scope of Assessment
- **Directories**: `src/`, `tests/`, `bin/`, `config/`
- **Standards**: PSR-1, PSR-3, PSR-4, PSR-7, PSR-11, PSR-12, PSR-17, PSR-18, PSR-20

## Tooling Status
| Tool | Status | Note |
|------|--------|------|
| PHP_CodeSniffer | Added | Configured with `phpcs.xml.dist` using PSR-12 |
| PHP-CS-Fixer | Not Added | PHP_CodeSniffer (phpcbf) is preferred for now |
| Composer Scripts | Added | `cs:check` and `cs:fix` available |

## PSR-12 Compliance Status
- **Current Status**: Non-compliant
- **Findings**: 665 errors found across 88 files.
- **Top Issues**:
    - Header block spacing and formatting.
    - Indentation (mix of tabs/spaces or inconsistent space counts).
    - Control structure spacing (e.g., space after `if`).
    - Visibility modifiers (missing on some constants in older code).
    - Side effects in files (some files declaring classes and also having logic, though rare).
- **Remediation**: 99% of issues are automatically fixable via `composer cs:fix`.

## Accepted PSR Standards Audit Table

| PSR | Standard | Status | Evidence / Notes | Recommendation for v1.0 |
|-----|----------|--------|------------------|-------------------------|
| PSR-1 | Basic Coding Standard | Partially Compliant | Follows naming conventions mostly; some files may have side effects. | **Must** |
| PSR-3 | Logger Interface | Already Compliant | Uses `Psr\Log\LoggerInterface` via Monolog and `LoggerAwareTrait`. | **Must** (Maintain with redaction) |
| PSR-4 | Autoloading Standard | Already Compliant | Configured in `composer.json` (`Box\` -> `src/`). | **Must** (Maintain) |
| PSR-7 | HTTP Message Interface | Already Compliant | `BoxResponseInterface` extends `PsrResponseInterface`. Requests move to PSR-7 in v1. Internal transport uses PSR-7. v1 replaces `BoxResponse` with a thin PSR-7 wrapper, removing Symfony inheritance. | **Must** |
| PSR-11 | Container Interface | Not Applicable | No DI container usage currently; v1.0 may benefit from it. | **Deferred** |
| PSR-12 | Extended Coding Style | Partially Compliant | Base standard adopted; many style violations exist. | **Must** |
| PSR-17 | HTTP Factories | Not Applicable | Not currently used; v1 uses for PSR-7 messages. | **Must** |
| PSR-18 | HTTP Client | Already Compliant | `TransportInterface` aligns with PSR-18; supports `send(RequestInterface $request)`. | **Must** |
| PSR-20 | Clock | Not Applicable | No explicit clock abstraction; v1.0 uses `DateTimeImmutable`. | **Could** |

## Detailed Findings by PSR

### PSR-12 (Coding Style)
The codebase has legacy style patterns (e.g., closing braces on same line as next control structure, inconsistent spacing in arrays). While functional, it deviates from modern PHP standards.

### PSR-7, 17, 18 (HTTP Interoperability)
The SDK is moving to full PSR-18 for the transport layer, allowing users to inject any compliant client. `BoxResponse` is being **replaced** by a thin PSR-7 wrapper that removes legacy Symfony inheritance. PSR-17 factories are used for request/stream creation. Upload progress is deferred to v1.1.0 as it's not standardized in PSR-18.

### PSR-3 (Logging)
Well-integrated. The `LoggerFactory` and `LoggerAwareTrait` provide a solid foundation for logging throughout the SDK and CLI.

## Recommended Coding-Standard Tooling
- **Primary**: `squizlabs/php_codesniffer`
- **Configuration**: `phpcs.xml.dist`
- **Automation**: GitHub Actions (or similar) should run `composer cs:check` on every PR.

## Proposed Composer Scripts
- `composer cs:check`: Runs `phpcs` to audit style.
- `composer cs:fix`: Runs `phpcbf` to automatically fix most style issues.

## Exclusions from Style Checks
- `vendor/`: Third-party code.
- `var/`: Logs, cache, and temporary files.
- `generated/`: Any future generated code (if applicable).

## v1.0 Compliance Path
1. **Automated Fix**: Run `composer cs:fix` to resolve 99% of PSR-12 issues.
2. **Manual Cleanup**: Resolve remaining PSR-12 issues (e.g., line length, side effects).
3. **HTTP Decoupling**: Refactor `TransportInterface` to align with PSR-18.
4. **Type Safety**: Use PHP 8.4 features (property hooks, asymmetric visibility if applicable) while maintaining PSR-12.

## Prioritized Action Plan

### Must (High Priority)
- [x] Fix all PSR-12 violations before v1.0 release.
- [ ] Enforce `cs:check` in CI.
- [x] Ensure all new code adheres to PSR-12.

### Should (Medium Priority)
- [ ] Full PSR-7 request/response support in `Connection`.
- [ ] Implement PSR-18 compliant transport.
- [ ] Use PSR-17 factories for creating requests/streams.

### Could (Low Priority)
- [ ] Introduce PSR-11 for service management in v1.0.
- [ ] PSR-20 Clock for better testability of time-sensitive operations (e.g., token expiration).

## Risks and Non-Goals
- **Risk**: Automated fixes might occasionally break complex docblocks or multi-line strings.
- **Non-Goal**: Refactoring the entire SDK logic just for "interoperability" when it's not requested by the architecture.
- **Non-Goal**: 100% PSR-7 compliance in v0.11 (target v1.0).

## Definition of Done for PSR Compliance
- `composer cs:check` returns no errors on `src/` and `tests/`.
- `composer.json` autoloading is validated.
- Logging successfully uses `Psr\Log\LoggerInterface` in all services.

## Maintenance Plan
- Coding standard checks integrated into the development workflow.
- Regular updates to `phpcs` and rulesets.
- Documentation updates whenever a new PSR is adopted.
