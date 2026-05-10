# Box PHP SDK Documentation

Welcome to the documentation for the Box PHP SDK.

## [User Documentation](user/)
- [Programmatic Usage](user/programmatic-usage.md): Core SDK usage patterns and examples.
- [CLI Test Harness](user/cli-test-harness.md): Documentation for the `bin/box-sdk` CLI tool.

## [Migration Guides](migration/)
- [Upgrading from 0.10.x to 0.11.0](migration/upgrading-0.10-to-0.11.md): Guide for the transition release.
- [Model Namespace Migration Decisions](migration/model-namespace-migration-decisions.md): Background on namespace flattening.

## [Roadmap and Planning](planning/)
- [Roadmap](planning/roadmap.md): High-level project roadmap.
- [Release Task Lists](planning/release-task-lists.md): Specific tasks for upcoming releases.

### [v1.0 Planning](planning/v1/)
The v1.0 release focuses on architectural hardening and PSR compliance.

- **[Strategy and Contracts](planning/v1/strategy-and-contracts.md) (Canonical)**: Core v1.0 architecture and strategy.
- [v1.0 Overview](planning/v1/overview.md): High-level goals and typing standards.
- [Implementation Checklist](planning/v1/implementation-checklist.md): Tracking progress of v1.0 work.
- [Decision Index](planning/v1/decision-index.md): Central log of architectural decisions.
- [Architecture Rules](planning/v1/architecture-rules.md): Coding and structure rules for v1.0.
- [Test Coverage Plan](planning/v1/test-coverage-plan.md): Quality and testing strategy.
- [API Coverage Audit](planning/v1/api-coverage-audit.md): Assessment of Box API endpoint coverage.
- [Interface and Model Audit](planning/v1/interface-and-model-audit.md): Review of existing models for v1.0 alignment.
- [Package Rename Plan](planning/v1/package-rename-plan.md): Planning for the move to `box/sdk`.
- [Documentation Gap Inventory](planning/v1/documentation-gap-inventory.md): Tracking documentation needs.

**Current Foundation Status:**
- **Step 10 / Resource Namespace and Interface Rationalization**: In Progress.
- **CLI Test Harness Scope**: Clarified and documented as a retained practical verification tool.
- **User Migration Alignment**: Verified and documented in the [Interface and Model Audit](planning/v1/interface-and-model-audit.md).

### [Future Integrations](planning/future/)
- [Future Symfony Bundle](planning/future/future-symfony-bundle.md): Planning for Symfony/Doctrine/Configuration integration (Deferred).

## [Audits and Assessments](audits/)
- [Documentation Audit](audits/documentation-audit.md): Evaluation of current documentation quality.
- [PSR Compliance Assessment](audits/psr-compliance-assessment.md): Evaluating alignment with PSR standards.
- [Box API Endpoint Coverage](audits/box-api-endpoint-coverage.md): Detailed endpoint support list.
- [Model Signature Audit](audits/model-signature-audit.md): Technical review of model method signatures.
- [Model Typing Decisions](audits/model-typing-decisions.md): Decisions on strict typing for model properties.

## [Prompts](prompts/)
- [Changelog Prompt](prompts/changelog-prompt.md): Instructions for updating `CHANGELOG.md`.
- [AI Chat Prompt Format](prompts/generic-phpstorm-ai-chat-prompt-delivery-format.md): Internal AI assistant delivery format.

## [AI and Internal Planning](ai/)
- [AI Assistant Planning Context](ai/ai-assistant-planning-context.md): Context for AI-driven development.
