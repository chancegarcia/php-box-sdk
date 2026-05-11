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

## Class Strings

- **Symbolic References**: Prefer `SomeClass::class` over hard-coded fully qualified class-name strings (FQCN) when referring to PHP symbols (classes, interfaces, traits).
- **Literal Strings**: Do not apply this rule to protocol strings, API type values, external identifiers, or arbitrary user-facing text that happens to match a class name.

## Setters and Fluent APIs

- **Void Setters**: New model/DTO setters should generally return `void`.
- **No Chaining**: Avoid introducing fluent setter chains (`$obj->setA()->setB()`) in new code, tests, or documentation.
- **Immutability**: Immutable "with" methods (`public function withName(string $name): self`) are acceptable where the project follows an immutable DTO/Value Object pattern (e.g., PSR-7).

## Readability and Logic

- **Temporary Variables**: Inline trivial temporary variables that only store a value for immediate return and do not improve readability. Keep the temporary variable when it improves clarity, documents intent, avoids duplicate work, or supports debugging.
    ```php
    // before
    public function foo(): array
    {
        $items = [];

        return $items;
    }

    // after
    public function foo(): array
    {
        return [];
    }
    ```
- Avoid nested ternary operators. Use explicit branching or a named helper method when conditional logic becomes nested. Simple one-level ternaries are acceptable when they remain readable.
- Do not add custom PHPCS/PHPStan enforcement for nested ternaries unless explicitly requested; enforce this preference through review and project guidelines for now.
- Yoda Conditions: Use Yoda conditions (`if (null === $value)`) where they help prevent accidental assignments, but only if they do not significantly reduce readability.
- Early Returns: Prefer early returns to reduce nesting depth.

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
