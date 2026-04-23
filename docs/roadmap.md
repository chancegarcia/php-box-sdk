# Roadmap

## Project Vision

Build a reliable, developer-friendly PHP SDK for working with Box-related functionality. The CLI exists mainly as a quick, practical test tool for verifying SDK behavior without needing to wire the package into an existing Composer project during every iteration.

The CLI can also support native/manual tasks when helpful, but that is secondary to the SDK-first purpose of the project.

## Current Focus Areas

### 1. Core SDK Stability
- Strengthen the main client and service layer
- Improve connection handling and response parsing
- Keep DTOs, models, and storage abstractions consistent
- Ensure the SDK is easy to extend and test

### 2. Authentication Workflows
- Finalize and harden authentication-related flows
- Support token refresh and authorization URL generation
- Make environment-based configuration predictable and safe
- Improve error handling for auth failures and edge cases

### 3. File Upload Support
- Continue refining upload behavior
- Improve handling of local files, temporary files, and remote responses
- Make feedback clear and actionable
- Validate upload success/failure states thoroughly

### 4. CLI as a Verification Tool
- Keep the CLI lightweight and focused on SDK verification
- Use CLI commands to validate SDK functionality quickly during development
- Support manual/native tasks where useful, without treating them as the primary goal
- Avoid over-investing in CLI complexity that does not directly help SDK testing

### 5. Testing and Quality
- Expand PHPUnit coverage across commands, services, and client behavior
- Add tests for edge cases and failure scenarios
- Keep test fixtures and temporary test data organized
- Reduce regressions by covering critical flows first

### 6. Documentation
- Improve project documentation for setup and usage
- Add practical examples for authentication and upload flows
- Document command-line options and environment variables
- Keep README and docs aligned with actual behavior

### 7. Configuration Format Future Planning
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