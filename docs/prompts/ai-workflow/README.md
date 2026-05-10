# AI Workflow Documentation

This folder contains reusable AI-assisted development workflow templates and guidance. These documents are designed to be copied into other projects to establish a consistent, slice-based development process.

## Folder Contents

- [Single Repository Workflow](single-repository-workflow.md)
  - For projects where planning, implementation, tests, and documentation happen in one repository.
- [Coordinating Repository Workflow](coordinating-repository-workflow.md)
  - For complex initiatives where one repository manages a roadmap across multiple downstream implementation repositories.
- [PHP Code Style Guidance](php-code-style-guidance.md)
  - Reusable PHP code-style preferences and validation expectations.
- [Prompt Delivery Expectations](prompt-delivery-expectations.md)
  - Guidance on how AI assistants should format and deliver implementation prompts.
- [Handoff Summary Template](handoff-summary-template.md)
  - A reusable template for periodic handoff summaries to preserve context across sessions.

## Reuse Model

These documents are **reusable templates**. To use them in a new project:

1. Copy the `docs/prompts/ai-workflow/` folder from the template repository into your project's `docs/prompts/` directory.
2. If copied via Git, remove any `.git` metadata from the copied folder to detach it from the template source.
3. **Adapt and Tailor**: These templates are not rigid rules. Review and update them to match your project's specific:
    - Programming languages and frameworks.
    - Tooling and validation commands (e.g., test runners, linters).
    - Repository structure.
    - Security and privacy requirements.
    - Release processes.
4. **Authoritative Guidelines**: Local project-specific guidelines (e.g., in `.junie/guidelines.md` or a root `README.md`) always override these generic templates.

## Draft Prompts and Planning

All prompt patterns included in these documents are **drafts**. They must be refined before execution to include the specific context of the current task, repository, and roadmap item.

**Mandatory Review**: For every new step, segment, roadmap item, or initiative, begin with a tracker/plan review and refinement pass before implementation. This is required even for low-risk work. Confirm scope, slice order, dependencies, non-goals, validation expectations, and draft prompts are current before running the first implementation slice. Implementation should not begin until the current tracker/plan has been reviewed and accepted by the human reviewer.
