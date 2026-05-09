# Roadmap

## Project Vision

Build a reliable, developer-friendly PHP SDK for working with Box-related functionality. v0.11.0 is a **functional transition release** that modernization the codebase and introduces v1.0 architecture patterns while preserving backward compatibility.

The CLI exists mainly as a quick, practical test tool for verifying SDK behavior without needing to wire the package into an existing Composer project during every iteration.

## Release Strategy

### v0.11.0 (Functional Transition)
- **Goal**: Bridge v0.10.x and v1.0, stabilize core behavior, and modernize for PHP 8.4.
- **Status**: Current Focus / Transition Release.
- **Key Features**:
    - PHP 8.4+ requirement.
    - Flattened namespaces with legacy aliases.
    - Recursive Hydration layer.
    - Integration with Doctrine Collections.
    - Transition layer for nested model fields (supporting both arrays and objects).
    - Introduction of DTOs for complex payloads.
    - Pluggable HTTP transports (Guzzle/Curl).
    - Improved `FileStream` for uploads.
- **Release Tasks**: See [v0.11 Release Task List](release-task-lists.md#v011-release-task-list).

### v1.0 (Design Perfection)
- **Goal**: Full implementation of the future architecture with no legacy baggage.
- **Key Focus**: See [v1.0 Planning](v1-planning.md) and [v1.0 Strategy](v1-planning/v1-strategy-and-contracts.md) for detailed technical goals.
- **Planned Changes**:
    - **Client as Facade**: `Client` will become a lightweight facade over focused services (e.g., `FileService`, `UserService`).
    - **Service-First Architecture**: High-level operations move to focused services.
    - **Direct Transport API**: Supported advanced extension point for raw PSR-7 requests; supports both PSR-oriented `send()` and ergonomic `request()` methods.
    - **No Legacy Baggage**: Remove all legacy aliases and deprecated namespaces.
    - **Strict Typing**: Enforce object-only types for nested model fields; standardize IDs as `string` and dates as `DateTimeImmutable`.
    - **Modern Auth Boundary**: Decouple `Connection` into `Transport` and `AuthProvider`.
    - **Clean Connection**: Decouple `Connection` from `Model` inheritance; make it a raw request/response layer.
    - **Service Consistency**: Services will return mapped model objects or typed DTOs consistently.
    - **Modern DI**: Replace class-string setters with constructor injection or factories.
    - **Expanded Coverage**: Implement high-priority endpoints like Metadata; Sign Requests and Webhooks deferred to v1.1.0 (with v1.0.0 direct transport fallback).
    - **PSR Compliance**: Achieve full PSR-12 compliance and align with PSR-3, PSR-7, PSR-17, and PSR-18.
    - **Resilience**: Optional, disabled-by-default retry behavior with `Retry-After` support.
- **Release Tasks**: See [v1.0 Release Task List](release-task-lists.md#v10-release-task-list).

## Current Focus Areas

### 1. Core SDK Stability
- Strengthen the main client and service layer.
- Improve connection handling and response parsing.
- Keep DTOs, models, and storage abstractions consistent.
- Improve `Retry-After` handling in the service layer.

### 2. Authentication Workflows
- Finalize and harden authentication-related flows.
- Support token refresh and authorization URL generation.
- Improve error handling for auth failures and edge cases.

### 3. File Upload and Streaming
- Support robust file uploads including streaming via `FileStream`.
- Improve handling of local files, temporary files, and remote responses.
- Validate upload success/failure states thoroughly.

### 4. CLI as a Verification Tool
- Keep the CLI lightweight and focused on SDK verification.
- Use CLI commands to validate SDK functionality quickly during development.
- Avoid over-investing in CLI complexity; focus on the SDK as the primary product.

### 5. Testing and Quality
- Expand PHPUnit coverage across commands, services, and client behavior.
- Add tests for edge cases and failure scenarios.
- Cover critical hydration and mapping paths.

### 6. PSR Compliance
- Achieve full PSR-12 compliance before v1.0.
- Decouple HTTP layer to align with PSR-7/18.
- Use PSR-17 factories for requests and streams.

### 7. Documentation
- Improve project documentation for setup and usage
- Add practical examples for authentication and upload flows
- Document command-line options and environment variables
- Keep README and docs aligned with actual behavior

### 8. Configuration Format Future Planning
- Track possible YAML/XML configuration integration here: [yaml-xml.md](yaml-xml.md)
- Treat YAML/XML support as a future consideration, not a current requirement
- If adopted later, it may be implemented in a separate Symfony bundle project instead of being supported directly in this repository

## Short-Term Goals

- Clean up and prioritize existing TODO items
- Stabilize authentication and refresh flows
- Improve upload command reliability
- Add or refine tests around the most used paths
- Document installation and first-run setup more clearly
- Keep the CLI useful as a fast SDK validation path

## Mid-Term Goals

- Expand SDK coverage for additional Box-related operations
- Improve internal abstraction boundaries
- Add more consistent response and error handling
- Keep the CLI lean while preserving its usefulness for manual testing
- Improve developer tooling and project conventions

## Long-Term Goals

- Provide a polished SDK-first experience suitable for real-world use
- Keep the codebase modular, testable, and easy to evolve
- Support a broader set of Box workflows without sacrificing simplicity
- Establish strong documentation and maintainability standards
- Revisit YAML/XML support only if it becomes clearly valuable for a future Symfony bundle

## Success Criteria

- SDK behavior is reliable, predictable, and well-tested
- CLI commands remain useful as a fast way to verify SDK functionality
- Authentication flows are robust and well-tested
- Upload operations are stable and clearly reported
- Documentation matches actual behavior
- The test suite provides confidence for future changes

## Open Questions

- Which Box features should be prioritized next?
- Which SDK capabilities matter most for the next phase?
- What is the right balance between SDK depth and CLI convenience?
- Should YAML/XML configuration support live here, or in a future Symfony bundle?

## Maintenance Notes

- Keep roadmap aligned with the current TODO list
- Update priorities as features are completed
- Revisit goals after major releases or structural changes
- Reassess the YAML/XML integration path if the future Symfony bundle becomes the preferred home for it

---

**See also:**
- [README.md](../README.md)
- [Programmatic Usage Guide](programmatic-usage.md)
- [CLI Test Harness Guide](cli-test-harness.md)
- [PSR Compliance Assessment](psr-compliance-assessment.md)