# Prompt Delivery Expectations

This document defines how an AI assistant should format and deliver implementation prompts and task lists to a human user or another AI agent.

## General Principles

- **Direct Answers First**: If the user asks a direct question, answer it directly and concisely.
- **Junie-Ready Prompts**: Ask if the user wants a "Junie-ready" (or agent-ready) prompt created only when appropriate.
- **Intent Detection**: Automatically generate a prompt if the user's request clearly involves implementation planning, creating a task list, or a goal-oriented execution request.
- **Pre-Flight Check**: Identify ambiguities, missing requirements, or potential blockers before generating a prompt.
- **Clarification**: If the request is unclear, ask for clarification. If the user insists on proceeding, include the concerns as "Assumptions" or "Risks" within the generated prompt.

## Prompt Formatting

- **Markdown**: Always use Markdown for generated prompts.
- **No Code Fences**: Do not wrap the entire prompt in triple backticks (fenced code blocks), as it makes it harder to copy/paste.
- **Boundaries**: Clearly mark the start and end of the prompt using plain text:
    - `START PROMPT`
    - `END PROMPT`

## Content Guidelines

- **Specificity**: Keep the prompt focused on the specific request. Avoid unrelated scope or "while you're at it" improvements.
- **Necessary Context**: Include only the context required to perform the task.
- **Goals and Deliverables**: Clearly state what must be achieved and what files/artifacts must be produced.
- **Constraints**: Include constraints such as public API stability, backward compatibility, and security/privacy requirements.
- **Validation**: Specify exact validation commands (e.g., `composer test`, `npm test`) that must pass.
- **Documentation**: Instruct the agent to check for documentation drift and update relevant docs, READMEs, or CHANGELOGs.
- **Agent Instructions**: Instruct the agent to inspect project-specific documentation (e.g., `.junie/guidelines.md`) before making changes.
- **No Automatic Execution**: AI assistants should generate prompts but **never** execute them automatically without human review and approval.

## Security and Privacy

- **Sanitization**: Ensure the prompt contains no real credentials, tokens, or private customer data.
- **Redaction**: Redact internal project names or proprietary details unless the task is explicitly for a private repository and the context is authorized.
- **Placeholders**: Use standard placeholders like `YOUR_CLIENT_ID` or `[REPO_NAME]`.

## Reuse and Adaptation

This document can be copied into other projects. When doing so:
- Update the preferred validation commands to match the project's tooling.
- Adjust the "Agent Instructions" to point to the project's local AI guidelines file.
- Tailor the security section to the project's specific compliance requirements.

## Example Prompt Structure

START PROMPT

### Goal
[Describe the specific outcome]

### Context
[Relevant background and files]

### Tasks
1. [Task 1]
2. [Task 2]

### Constraints
- [Constraint A]
- [Constraint B]

### Validation
- Run `[Command]` and ensure it passes.

END PROMPT
