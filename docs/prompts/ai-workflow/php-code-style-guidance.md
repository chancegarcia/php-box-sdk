# PHP Code Style Guidance

This document outlines the preferred PHP code style and validation expectations for AI-assisted development. While tailored for PHP, these principles can be adapted for other languages.

## PHP Version and Compatibility

- **Target Version**: Always adhere to the project's declared PHP version (e.g., PHP 8.4).
- **Features**: Do not use language features or syntax introduced in versions newer than the project's supported version.
- **Strict Typing**: Prefer strict typing (`declare(strict_types=1);`) if the project uses it.

## Formatting

- **PSR Standards**: Follow PSR-12 (Extended Coding Style Guide) for all PHP code.
- **Indentation**: Use the project's configured indentation (typically 4 spaces).
- **Tooling**: Use the project's configured style tools (e.g., PHP_CodeSniffer) rather than ad hoc formatting.

## Arrays

- **Short Syntax**: Always use short array syntax `[]` for array literals.
- **Legacy Syntax**: Do not use the legacy `array(...)` syntax.

## Imports and `use` Statements

- **Clean Imports**: Keep `use` statements organized and sorted (typically alphabetically).
- **Unused Imports**: Remove unused imports only when it is 100% safe to do so.
- **PHPDoc Awareness**: Do NOT remove imports that are referenced in PHPDoc, attributes, annotations, or type comments. Search both the code and all comments before removing an import.
- **Automation**: Prefer using the project's automated tools (e.g., `phpcbf`) for import cleanup if they are configured to be PHPDoc-aware.

## Types

- **Explicit Types**: Prefer explicit parameter types, return types, and property types where compatible with the supported PHP version.
- **Avoid Mixed**: Avoid using the `mixed` type where a more specific type (or union type) is known.
- **Static Analysis**: Use PHPDoc array shapes and generics (e.g., `array<string, int>`) to improve static analysis (PHPStan/Psalm).
- **Public API Stability**: Do not change public method signatures or property types in a backward-incompatible way unless explicitly requested.

## JSON Encoding and Decoding

- **Always pass `JSON_THROW_ON_ERROR`** to both `json_encode()` and `json_decode()`. This converts silent failures (`false` / `null` + `json_last_error()`) into catchable `\JsonException`.
- **Never use `json_last_error()` checks** as an alternative to `JSON_THROW_ON_ERROR`. The flag is mandatory.
- **Catching at boundaries only**: Wrap `json_decode()` in `try/catch (\JsonException)` only at system boundaries where a fallback makes sense (e.g., parsing untrusted external input, exception constructors). Never swallow the exception silently — log or convert it to a domain exception.
- **`json_encode` failures are rarely expected**: In normal SDK code encoding well-typed arrays, a `JsonException` from `json_encode` is always a programming error — let it propagate.

## `@param` Tags

- **Omit redundant `@param` tags**: If a `@param` tag states only the type and variable name with no additional description or value, omit it. Modern PHP type hints make it redundant.
  - Omit: `@param string $name`, `@param int $limit`, `@param Folder $folder`
  - Keep if: the tag adds a description (`@param string $name The display name`), uses a generic/shape type not expressible in a native hint (`@param array<string, mixed> $options`), uses a PHPDoc-only construct (`@param T $value` with `@template T`), or narrows a union type for static analysis.
- **This applies to new and touched docblocks**: Do not add bare-type `@param` tags to new methods. When editing an existing docblock, strip bare-type tags at the same time.

## `@throws` Annotations

- **Full chain coverage**: Every method that throws or allows an exception to propagate uncaught must declare `@throws ExceptionClass` in its docblock. This includes callers that do not catch — the tag must bubble up through every public method in the chain until a catch boundary is reached.
- **Interfaces included**: If an implementing class method throws an exception, the corresponding interface method must also declare `@throws`.
- **All exception types**: Apply to all exceptions — `\JsonException`, `\RuntimeException`, domain exceptions, etc. — not only checked exceptions.
- **Example**: If `json_encode()` with `JSON_THROW_ON_ERROR` is called in a private helper, every public method that can reach that helper without a `catch` block must declare `@throws \JsonException`.

## `array` Parameter Documentation

- **Generic syntax required**: Any `array` parameter, property, or return type must be documented in PHPDoc using generics or array shape syntax. Never leave `array` bare in a docblock.
  - Sequential: `list<T>` or `array<int, T>` (prefer `list<T>` for indexed arrays from `array_map`, `array_values`, etc.)
  - Associative: `array<string, T>` or an inline array shape `array{key: T, ...}`
- **Applies everywhere**: `@param array`, `@return array`, and `@var array` must all be typed generically. This applies to new code and to any docblock being touched for other reasons.
- **Example**: Instead of `@param array $options` write `@param array<string, mixed> $options`.

## Class Strings

- **Symbolic References**: Prefer `SomeClass::class` over hard-coded fully qualified class-name strings (FQCN) when referring to PHP symbols (classes, interfaces, traits).
- **Literal Strings**: Do not apply this rule to protocol strings, API type values, external identifiers, or arbitrary user-facing text that happens to match a class name.

## Setters and Fluent APIs

- **Void Setters**: New model/DTO setters should generally return `void`.
- **No Chaining**: Avoid introducing fluent setter chains (`$obj->setA()->setB()`) in new code, tests, or documentation.
- **Immutability**: Immutable "with" methods (`public function withName(string $name): self`) are acceptable where the project follows an immutable DTO/Value Object pattern (e.g., PSR-7).

## Readability and Logic

- Avoid nested ternary operators. Use explicit branching or a named helper method when conditional logic becomes nested. Simple one-level ternaries are acceptable when they remain readable.
- Do not add custom PHPCS/PHPStan enforcement for nested ternaries unless explicitly requested; enforce this preference through review and project guidelines for now.
- Yoda Conditions: Use Yoda conditions (`if (null === $value)`) where they help prevent accidental assignments, but only if they do not significantly reduce readability.
- Early Returns: Prefer early returns to reduce nesting depth.
- Arrow functions: prefer `static fn` over `fn` when the closure does not reference `$this`, `self`, `static`, or `parent`. This avoids binding the enclosing object and carries a minor performance benefit.

## Logging and Debugging

- **No Ad Hoc Logging**: Do not use `echo`, `var_dump`, `print_r`, or `error_log` in library/SDK code.
- **Abstractions**: Use the project's logging abstractions (typically PSR-3 `LoggerInterface`).

## Validation

- **Canonical Commands**: Use the repository's native validation commands.
- **Composer Scripts**: For Composer-based projects, prefer these scripts if they exist:
    - `composer lint` (Syntax check)
    - `composer test` (PHPUnit)
    - `composer analyse` (Static analysis / PHPStan)
    - `composer cs:check` (Style check)
    - `composer cs:fix` (Style fix)
    - `composer review` (Full suite)
- **Direct Binaries**: Do not substitute direct `vendor/bin/*` commands (like `vendor/bin/phpunit`) for project scripts unless investigating a specific tooling issue.

## Adaptation Note

This document is a reusable starting point. It should be reviewed and updated to match the specific rules of the receiving project. Local project guidelines always take precedence.
