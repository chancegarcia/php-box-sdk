# AI Workflow Documentation

This folder contains reusable AI-assisted development workflow templates and guidance.
These documents are designed to be copied into other projects to establish a consistent,
task-based development process.

## Folder Contents

- [Single Repository Workflow](single-repository-workflow.md)
  For projects where planning, implementation, tests, and documentation happen in one
  repository.
- [Coordinating Repository Workflow](coordinating-repository-workflow.md)
  For complex initiatives where one repository manages a roadmap across multiple
  downstream implementation repositories.
- [Handoff Summary Template](handoff-summary-template.md)
  Session handoff files: what happened, what's next, and human review notes.
- [Plan Communication and Execution](prompt-delivery-expectations.md)
  How the AI describes plans in chat and executes them using the
  describe-approve-execute pattern.
- [PHP Code Style Guidance](php-code-style-guidance.md)
  Language-specific reference for PHP projects. Copy to the project level and adapt;
  not part of the generic workflow core.

## Template Version

`2.0` — Single-agent (Claude Code) workflow with Jira-aligned terminology, two-file
handoff, describe-approve-execute pattern, and language-agnostic validation.

When copying to a project, add the following block to your `CLAUDE.md`:

```markdown
## Workflow Template
- Version: 2.0
- Copied: [date]
- Customizations: [brief description of project-specific changes, or "none"]
```

This makes it easy to identify what version a project was based on and what has
diverged when template updates are available.

## Reuse Model

These documents are **reusable templates**. To use them in a new project:

1. Copy this folder into your project (e.g., `docs/ai-workflow/`).
2. If copied via Git, remove any `.git` metadata from the copied folder to detach it
   from the template source.
3. **Adapt and tailor**: These templates are not rigid rules. Review and update them
   to match your project's:
   - Programming language and frameworks.
   - Tooling and validation commands.
   - Repository structure.
   - Security and privacy requirements.
   - Release process.
4. **Local config is authoritative**: The project's `CLAUDE.md` always overrides these
   generic templates.

## Draft Plans and Trackers

All plan patterns in these documents are **drafts**. Refine them before use to include
the specific context of the current task, repository, and work item.

**Mandatory review**: For every new epic, story, or task, begin with a tracker review
and refinement pass before implementation — even for low-risk work. Implementation
must not begin until the tracker has been reviewed and accepted by the human reviewer.

## Original Templates

The `original/` folder is empty by default. When copying this template into a project,
manually copy the template files you intend to customize into `original/` before
making any project-specific changes. These copies serve as your baseline.

**Why**: The originals let you see exactly what you changed (diff against them) and
make it easy to identify improvements worth contributing back to the template source.

**Before customizing a file**:
1. Copy it to `original/` (e.g., `cp single-repository-workflow.md original/`)
2. Edit the main file for your project's needs

**Contributing improvements back**: If a project-level customization proves generally
useful, update the corresponding file in `original/` to reflect the improved version
and note the project it came from. That becomes the candidate to backport.

**Annotating project customizations**: When editing template files for project-specific
needs, add a comment at the start of each modified section:

```
<!-- customized: [project-name], template-2.0, [brief description] -->
```

## Retired Documents

The following documents were retired as part of the workflow evolution from a
two-agent model (Claude as prompt generator + Junie as executor) to a single-agent
model (Claude Code as direct executor).

- `multi-agent-collaboration.md` — Defined the two-agent planning and execution
  workflow. Archived at commit `[852eac751562c94a3a6b6bb5837c5af818c3ed98]`.
- `multi-agent-topic-handoff-template.md` — Topic handoff template for the two-agent
  model. Archived at commit `[852eac751562c94a3a6b6bb5837c5af818c3ed98]`.

These files no longer exist in the working tree but remain in the repository history
and can be retrieved with `git show <hash>:<filename>` if needed.
