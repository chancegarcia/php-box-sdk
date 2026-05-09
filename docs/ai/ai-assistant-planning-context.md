# V1.0 Refactor Planning Continuation Notes

This document preserves the important planning decisions for the Box PHP SDK V1.0 refactor so future AI Assistant or Junie sessions can continue without losing context.

## Primary V1.0 Refactor Goals

The V1.0 refactor has two main architectural goals:

1. Remove over-engineered model/resource interfaces.
2. Eliminate `src/Model` as a god-abstraction layer.

The desired V1.0 architecture should be simpler, more concrete, easier to test, and easier to extend for missing Box API resources.

## Core Architectural Direction

V1.0 should move toward this conceptual structure:

- `Box\Resource` for Box API resource objects.
- `Box\Dto` for request DTOs, response DTOs, nested value objects, and payload shapes.
- `Box\Enum` for fixed API value sets.
- `Box\Service` for API operations.
- `Box\Http` for raw HTTP/transport concerns.
- `Box\Contract` for true extension-point interfaces only.
- `Box\Base` only if a small, explicit shared base class remains justified.
- `Box\Trait` only if a small, cohesive trait remains justified.

The `Box\Model` namespace should ideally disappear completely in V1.0.

## Interface Policy

Interfaces must earn their existence.

Keep interfaces only when they represent a real extension boundary, such as:

- HTTP transport.
- Request/response factories.
- Hydration or mapping extension points.
- Token storage.
- Logging integration.
- Cache integration, if added.
- Clock/time provider, if needed for tests.
- Other user-replaceable services.

Remove interfaces when they:

- Merely mirror one concrete resource model.
- Contain mostly getters and setters.
- Contain endpoint URI constants.
- Have exactly one implementation and no clear user-extension purpose.
- Exist primarily for architectural neatness rather than practical substitutability.

Examples of interfaces likely to remove unless the audit proves otherwise:

- `FileInterface`
- `FolderInterface`
- `UserInterface`
- `GroupInterface`
- `CollaborationInterface`
- `CommentInterface`
- `TaskInterface`
- Other model/resource-specific interfaces

A small generic interface like `IdentifiableResourceInterface` should be treated as uncertain and only kept if real code needs it. Do not preserve it just because it feels elegant.

## Resource and Model Rules

Resources should represent Box API data. They should not act as services, endpoint registries, request builders, hydration layers, or generic god-class abstractions.

Resource classes may contain:

- Typed properties.
- Constructors.
- Getters/setters if needed by current style.
- Simple resource-local behavior only if it is not API-operation logic.
- Typed nested resources and DTOs.

Resource classes must not contain:

- Endpoint URI constants.
- URL-building behavior.
- HTTP method knowledge.
- Connection/client/service calls.
- Pagination URL construction.
- Generic hydration logic.
- Generic arbitrary data stores.
- Operation-specific request-payload construction, unless deliberately modeled as a request DTO instead of a resource.

Endpoint URI constants should live in the relevant service or a narrowly scoped endpoint helper if truly shared. Prefer service-local constants unless there is a strong reason to share them.

## Service Rules

Services should own API operations.

Services should:

- Return concrete resources or DTOs.
- Accept scalar IDs, request DTOs, enums, or value objects as inputs.
- Use `string` for all Box IDs.
- Use `DateTimeImmutable` for date/time fields.
- Own endpoint construction and pagination behavior.
- Use hydrators/mappers to convert raw API responses into resources/DTOs.

Services should not return generic model interfaces in V1.0.

For create/update operations, prefer focused request DTOs over passing full resource objects.

Example conceptual direction:

- `CreateFolderRequest`
- `UpdateFolderRequest`
- `CreateCollaborationRequest`
- `UpdateUserRequest`

## Client Rule

`Client` should become a lightweight facade over focused services.

The preferred V1.0 direction is:

- `Client::files()` returns `FileService`.
- `Client::folders()` returns `FolderService`.
- `Client::users()` returns `UserService`.
- `Client::groups()` returns `GroupService`.
- `Client::collaborations()` returns `CollaborationService`.

The client may keep only minimal convenience methods if clearly justified.

Avoid allowing `Client` to remain or become a god object.

## Typing Rules

V1.0 typing decisions:

- All Box IDs should be `string` or `?string`.
- Dates should be `DateTimeImmutable` or `?DateTimeImmutable`.
- Nested resources should be object-only.
- Remove transition-layer array support for nested model/resource fields.
- Use PHP 8.4 enums for fixed value sets, such as roles, statuses, types, item statuses, and similar API-defined finite values.
- Use Doctrine Collections selectively for list response entry sets where a standard in-memory collection API adds value. Do not force every array-shaped field into a Doctrine Collection.
- V1.0 should prefer specific response DTOs that wrap a Doctrine Collection plus pagination metadata, rather than exposing one generic collection response for all endpoints.
- Avoid forcing small value objects, simple enum lists, shared link permissions, and dynamic metadata values into Doctrine Collections unless there is a clear benefit.
- Custom SDK collection classes should generally be removed unless they provide specific behavior that Doctrine Collections and response DTOs cannot provide cleanly.

## Hydration and Mapping Rules

Hydration should be handled by dedicated hydrator/mapper components.

Resources should not know how to hydrate themselves from raw arrays unless there is a narrow, explicitly justified factory.

Generic hydration methods should not live in resources, model traits, or base model god abstractions.

A valid contract may exist for hydration if it is a real extension point.

## Trait and Base-Class Rules

Traits should be aggressively audited.

A trait is allowed only if it provides small, stateless, cohesive behavior that cannot be expressed more clearly through composition.

Model-related traits should be removed or reduced if they contain:

- Generic data storage.
- Magic getters/setters.
- Hydration behavior.
- Array conversion.
- Endpoint logic.
- Dynamic property behavior.
- Hidden coupling between unrelated resources.

Base classes should also be questioned. A little explicit duplication in resource classes may be better than a clever shared abstraction.

If a shared base remains, it must have a small and explicit responsibility.

## Box API Reference Requirement

All V1.0 architecture planning and resource-specific refactors should use the current Box API reference as a source of truth:

https://developer.box.com/reference

Use the Box API reference to verify:

- Current Box API resources.
- Endpoint groupings.
- Request payloads.
- Response resource shapes.
- Nested resource relationships.
- Enum-like fields.
- Pagination/list response structures.
- Which services should exist.
- Which missing resources should be planned for V1.0.

The Box API reference should influence resource shape and service boundaries.

However, do not expand every refactor step into a broad API coverage implementation.

Use this recurring rule:

Use the Box API reference for planning and correctness. Do not expand implementation scope unless the missing resource is tightly coupled to the current refactor step.

## Missing Resource Planning Rule

A missing Box API resource may be folded into the current refactor step if:

- It is directly nested in an existing resource response.
- It is needed to correctly type an existing property.
- It replaces a raw array currently used by an existing model/resource.
- It is required for a current service method to have a clean V1.0 signature.
- It is small and low-risk.
- It prevents a bad abstraction from being created.

A missing resource should be deferred if:

- It requires a large new service area.
- It has many endpoints.
- It introduces new auth or transport concerns.
- It is not needed by existing resource cleanup.
- It would distract from eliminating the old model/interface architecture.

Likely fold-in candidates, depending on audit findings:

- `Permissions` DTO.
- `PathCollection` DTO.
- `GroupMembership` resource or DTO.
- `FileVersion` resource.
- `SharedLink` DTO/resource.
- Metadata-related DTOs if current File/Folder resources already expose metadata.

Likely deferred candidates, depending on audit findings:

- Webhooks.
- Sign requests.
- Retention policies.
- Legal holds.
- Classifications.
- Full metadata template lifecycle, unless needed for current metadata fields.

## API Areas That Need Special Attention

When auditing or planning V1.0 coverage, pay special attention to:

- Files.
- File versions.
- Folders.
- Users.
- Groups.
- Group memberships.
- Collaborations.
- Collections.
- Comments.
- Tasks.
- Metadata.
- Webhooks.
- Sign requests.
- File requests.
- Shared links.
- Retention policies.
- Legal holds.
- Classifications.

## Recommended New Audit Documents

The initial Junie task should create or update:

- `docs/planning/v1/interface-and-model-audit.md`
- `docs/planning/v1/architecture-rules.md`

If API coverage findings are large, create:

- `docs/planning/v1/api-coverage-audit.md`

## Suggested `docs/planning/v1/interface-and-model-audit.md` Contents

This document should include:

- Interface inventory.
- Trait inventory.
- Abstract class inventory.
- `src/Model` class inventory.
- Current responsibility of each interface/class/trait.
- Current usage summary.
- Keep/remove/replace/uncertain classification.
- Proposed V1.0 destination namespace.
- Whether each model/resource contains endpoint constants.
- Whether each model/resource contains URL-building behavior.
- Whether each model/resource contains service/client/connection behavior.
- Whether each model/resource contains hydration behavior.
- Whether each abstraction is a god abstraction.
- Proposed migration order.

## Suggested `docs/planning/v1/architecture-rules.md` Contents

This document should include rules for:

- When interfaces are allowed.
- When interfaces should be removed.
- Resource vs DTO vs enum vs service vs HTTP vs contract namespaces.
- Where endpoint URI constants should live.
- What resources are allowed to contain.
- What resources must not contain.
- ID typing.
- Date/time typing.
- Nested resource typing.
- Service return types.
- Service input types.
- Hydration responsibility.
- Trait/base-class policy.
- Client facade policy.
- Box API reference alignment.

## Suggested `docs/planning/v1/api-coverage-audit.md` Contents

If created, this document should include:

# V1 API Coverage Audit

Reference: https://developer.box.com/reference

## Current SDK Coverage

| Box API Area | Current SDK Resource | Current SDK Service | Status | Notes |
|---|---|---|---|---|

## Missing High-Priority Resources

| Box API Resource | Proposed Resource Class | Proposed Service | Priority | Fold Into Refactor? | Notes |
|---|---|---|---|---|---|

## Resource Relationships That Affect Refactor

| Existing Resource | Related Missing Resource | Relationship | Refactor Impact |
|---|---|---|---|

## Enum Candidates

| API Field | Resource | Proposed Enum | Notes |
|---|---|---|---|

## Request DTO Candidates

| API Operation | Proposed DTO | Notes |
|---|---|---|

## Deferred Resources

| Box API Area | Reason Deferred | Suggested Future Phase |
|---|---|---|

## Expected First Junie Task

The first Junie task should be audit and documentation only.

It should not modify PHP source files, tests, Composer configuration, or perform migrations.

Expected outputs:

1. `docs/planning/v1/interface-and-model-audit.md`
2. `docs/planning/v1/architecture-rules.md`
3. Optional `docs/planning/v1/api-coverage-audit.md`
4. Proposed safest resource-by-resource migration order.
5. Classification of uncertain decisions.

## Refined Master Junie Prompt

Use this prompt to start the process:

We are beginning the V1.0 architecture refactor for this Box PHP SDK.

The two main goals are:

1. Remove over-engineered model/resource interfaces.
2. Eliminate src/Model as a god-abstraction layer.

V1.0 architecture rules:

- Interfaces are allowed only for real extension boundaries such as HTTP transport, hydration, token storage, factories, or other user-replaceable services.
- Interfaces that merely mirror one concrete resource model’s getters/setters/constants should be removed.
- API resource objects should live under Box\Resource.
- Request/response/value DTOs should live under Box\Dto.
- Fixed API values should use PHP enums under Box\Enum.
- API operations should live in focused services under Box\Service.
- Raw HTTP concerns should live under Box\Http.
- True extension contracts should live under Box\Contract.
- Endpoint URI constants must not live in resource models or model interfaces.
- Resource classes must not build URLs, perform HTTP calls, call services, or own hydration logic.
- Services should return concrete resource classes or DTOs.
- Service inputs should be scalar IDs, request DTOs, enums, or value objects.
- IDs must be string typed.
- Dates must use DateTimeImmutable.
- Nested resources should be object-only in V1.0.
- Client should become a lightweight facade over focused services.

Box API reference requirement:

Use the current Box API reference at https://developer.box.com/reference as the source of truth for Box resources, endpoint groupings, request payloads, response resources, enum-like fields, and relationships between resources.

During the audit, compare the current SDK resources/services against the current Box API reference and identify:

1. Current SDK resources that map cleanly to Box API resources.
2. Current SDK resources that are incomplete or shaped incorrectly compared to the current Box API.
3. Missing Box API resources that should be planned for V1.0.
4. Missing services that should eventually exist for those resources.
5. Resource relationships that should influence the refactor now.
6. Existing resources that should be adjusted during this refactor to avoid blocking future endpoint coverage.
7. Related missing resources that should be folded into the current refactor because they are tightly coupled to an existing resource.
8. Missing resources that should be deferred until after the architectural refactor because adding them now would increase risk.

Examples of resources/endpoints to pay special attention to include, but are not limited to:

- Files
- File versions
- Folders
- Users
- Groups
- Group memberships
- Collaborations
- Collections
- Comments
- Tasks
- Metadata
- Webhooks
- Sign requests
- File requests
- Shared links
- Retention policies
- Legal holds
- Classifications

This first task is audit and documentation only.

Do not rename, move, delete, or modify PHP source files.
Do not change tests.
Do not change composer configuration.
Do not perform the actual refactor yet.
Do not implement missing Box API resources yet.

Please:

1. Audit all interfaces, traits, abstract classes, and classes under src/Model.
2. Create docs/planning/v1/interface-and-model-audit.md.
3. Create or update docs/planning/v1/architecture-rules.md.
4. Create docs/planning/v1/api-coverage-audit.md if the Box API coverage findings are large enough to deserve a separate document.
5. Identify interfaces that only mirror a single concrete model/resource.
6. Identify model classes or interfaces that contain endpoint constants, URL-building behavior, service behavior, connection behavior, hydration behavior, or generic god-abstraction behavior.
7. Classify each interface as:
    - keep
    - remove
    - replace
    - uncertain
8. Classify each src/Model class/trait/abstract class as:
    - move to Box\Resource
    - move to Box\Dto
    - move to Box\Enum
    - move to Box\Contract
    - move to Box\Service
    - move to Box\Http
    - move to Box\Base
    - move to Box\Trait
    - remove
    - uncertain
9. Compare the current SDK resources and services to the current Box API reference.
10. Identify missing Box API resources and services that should be planned for V1.0.
11. Identify any missing resources that are tightly coupled to current resources and should be folded into the refactor.
12. Identify missing resources that should be deferred until after the architectural refactor.
13. Propose the safest resource-by-resource migration order.
14. For each proposed migration step, include:
- goal
- files likely affected
- related Box API reference areas
- whether related missing resources should be folded in or deferred
- risk level
- validation commands
- dependencies on earlier steps

After completing the audit, stop.

Do not implement any migration steps in this task.

## Template for Later Resource-Specific Junie Prompts

Use this template after the initial audit, one resource family at a time:

Please migrate only the [RESOURCE FAMILY] resource family toward the V1.0 architecture.

Box API reference requirement:

Use https://developer.box.com/reference to verify the current API shape for this resource family before changing code.

Apply the reference only to this focused migration.

If a missing related resource is tightly coupled to this migration and would prevent a clean V1.0 design, include it or document why it should be included next.

If a missing related resource is not required for this migration, document it as deferred.

Do not broaden this task into general API coverage implementation.

Migration rules:

1. Replace removed model/resource interface usage with concrete resource classes wherever practical.
2. Move concrete resources to Box\Resource if they are not already there.
3. Move request/response/value payload objects to Box\Dto where appropriate.
4. Move fixed value sets to Box\Enum where appropriate.
5. Remove endpoint URI constants from models/interfaces and place them in the relevant service.
6. Move URL construction and API-operation behavior out of resources and into services.
7. Standardize IDs to string or ?string.
8. Standardize dates to DateTimeImmutable or ?DateTimeImmutable.
9. Remove nested array transition support for this resource family where safe.
10. Ensure services return concrete resources or DTOs.
11. Use request DTOs for create/update operations where clearer than passing full resources.
12. Remove the resource-specific interface if it only duplicates the concrete resource API.
13. Update hydration/mapping, services, tests, and docs affected by this migration.
14. Add or update V1.0 upgrade notes for breaking changes.
15. Do not perform broad unrelated cleanup.

Validation commands:

- composer test
- composer analyse
- composer cs:check

Report any failures and likely fixes.

## Recommended Migration Order Before Audit

This was the preliminary suggested order before Junie performs the real audit:

1. Audit interfaces and `src/Model`.
2. Write V1 architecture rules.
3. Create API coverage audit against the current Box API reference.
4. Create namespace skeleton if needed.
5. Migrate simple value DTOs first.
6. Migrate Users.
7. Migrate Groups, with GroupMembership planning or implementation if tightly coupled.
8. Migrate Folders, with PathCollection and Permissions DTO cleanup.
9. Migrate Files, with FileVersion, SharedLink, and Metadata planning.
10. Migrate Collaborations, with CollaborationRole enum if appropriate.
11. Migrate Comments.
12. Migrate Tasks.
13. Migrate Collections.
14. Refactor services and client facade.
15. Collapse/remove model interfaces, base model, and model trait once dependents are gone.
16. Remove `Box\Model` namespace.
17. Implement deferred missing resources in focused V1.0 coverage phases.

This order should be revised after the audit.

## What to Bring Back to AI Assistant After Junie Runs

After Junie completes the first audit-only task, bring back:

1. Interface classifications.
2. `src/Model` classifications.
3. Trait and base-class classifications.
4. Proposed resource-by-resource migration order.
5. API coverage audit summary.
6. Missing resources Junie recommends folding into the refactor.
7. Missing resources Junie recommends deferring.
8. Uncertain decisions.
9. Any accidental source changes, if they occurred.

The AI Assistant should then generate refined, narrow Junie prompts for each migration step.

## Validation and Code Style Tooling Policy

For V1.0 refactor tasks, Composer scripts are the source of truth for validation and code style.

Use:

- `composer dump-autoload`
- `composer test`
- `composer analyse`
- `composer cs:check`

For automatic code style fixes, use:

- `composer cs:fix`

Do not replace `composer cs:check` with ad hoc commands like `vendor/bin/phpcs --standard=PSR12 ...`, because those may bypass project-specific configuration in `phpcs.xml.dist`.

If style output reports that PHPCBF can automatically fix violations, run `composer cs:fix`, then re-run `composer cs:check`.

Direct PHPCS/PHPCBF commands should only be used when explicitly requested or for investigation. Final validation for implementation tasks must use the Composer scripts.

## Resolved V1.0 Design Decisions

| Decision | Resolution |
|---|---|
| Generic resource interface | Do not keep initially; use concrete resources unless a real use case appears. |
| SharedLink | Model as a DTO/value object, not a top-level Resource initially. |
| Collection responses | Use specific response DTOs that wrap Doctrine Collections for entries and expose pagination metadata. |
| Metadata | Use typed metadata DTO envelopes with flexible `array<string, mixed>` custom values. |
| Resource factory interfaces | Remove per-resource factory interfaces; use hydrator/mapper as the construction boundary. |
| Raw API payloads | Optional debug capture only, disabled by default. Prefer a separate debug payload store (e.g. WeakMap-backed) if feasible. |
| Raw payload usage | Raw payloads must not be logged, serialized, or treated as the primary SDK API. |
| Doctrine Collections | Use selectively for list response entries; do not force into all array-shaped fields. |
| Doctrine Collections scope | Doctrine Collections do not require Doctrine ORM or database mapping; they operate in memory on plain PHP objects. |
| Doctrine Collections filtering | Filtering/searching only applies to already-fetched entries and is not a replacement for Box API search or server-side filtering. |
| Value objects in Collections | Do not force small value-object arrays, enum lists, permissions, or dynamic metadata values into Doctrine Collections without a clear benefit. |
| Factory interface policy | Per-resource factory interfaces should be removed unless a specific public extension-point need is discovered. |

## Definition of Done for the V1.0 Refactor

The architectural refactor is done when:

- No model-specific interfaces remain unless explicitly justified.
- `src/Model` and `Box\Model` are deleted or no longer part of the public API.
- Resources live under `Box\Resource`.
- DTOs live under `Box\Dto`.
- Enums live under `Box\Enum`.
- Endpoint constants live in services or narrowly scoped endpoint helpers.
- Resources do not build URLs.
- Resources do not call services, clients, connections, or hydrators.
- Resources do not own generic hydration logic.
- Services return concrete resources or DTOs.
- Create/update operations use focused request DTOs where appropriate.
- IDs are consistently typed as `string`.
- Dates are consistently typed as `DateTimeImmutable`.
- Nested resources are object-only.
- Use Doctrine Collections selectively for list response entry sets where a standard in-memory collection API adds value. Do not force every array-shaped field into a Doctrine Collection.
- `Client` is a facade, not a god object.
- Missing Box API resources are either implemented in focused phases or documented as deferred.
- `composer test` passes.
- `composer analyse` passes.
- `composer cs:check` passes.
- `composer review` passes.
- V1.0 upgrade documentation exists.