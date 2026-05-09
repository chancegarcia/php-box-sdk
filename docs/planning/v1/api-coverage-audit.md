# V1.0 API Coverage Audit

This document compares the current SDK resources and services against the [Box API Reference](https://developer.box.com/reference).

## 1. Current Resource Status

| Resource | Status | Notes |
| :--- | :--- | :--- |
| **Files** | Incomplete | Core metadata present. Missing: representations, watermarking. **Fold in File Versions** to `FileService`. |
| **Folders** | Incomplete | Core metadata present. Missing: watermarking. Fold in `PathCollection` (focused DTO) and `Permissions` (DTO). |
| **Users** | Incomplete | Core metadata present. Missing: Enterprise user management, user avatar. |
| **Groups** | Incomplete | Core metadata present. Fold in **Group Memberships**. |
| **Collaborations** | Incomplete | Core metadata present. Missing: Pending collaborations. Use `CollaborationRole` Enum. |
| **Events** | Partial | User events supported. Admin events present but need review. |
| **Shared Links** | Partial | Managed within Files/Folders. Should be a dedicated DTO/value object (not a top-level Resource). |
| **Metadata** | Missing/Partial | Use typed DTO envelopes with flexible `array<string, mixed>` custom values. |

## 2. Missing Box API Resources

The following resources are missing and should be planned for V1.0 or shortly after:

- **Sign Requests**: High priority for V1.0. (Service + Resources).
- **File Requests**: High priority.
- **Webhooks**: V2 Webhooks. High priority.
- **Metadata**: Support for metadata templates and instances. (Currently raw array).
- **Retention Policies**: Required for enterprise compliance.
- **Legal Holds**: Required for enterprise compliance.
- **Classifications**: Metadata-based classification.
- **Tasks & Task Assignments**: Missing.
- **Comments**: Missing.

## 3. Missing Services

- `SignRequestService`: To handle Box Sign operations.
- `WebhookService`: To manage webhooks.
- `RetentionPolicyService`: To manage retention.
- `LegalHoldService`: To manage legal holds.
- `TaskService`: To manage tasks and assignments.
- `CommentService`: To manage comments on files.
- `MetadataService`: To manage custom metadata templates and values.

## 4. Resource Relationships & Coupling

- **Files/Folders <-> Shared Links**: Tightly coupled. Shared link creation should remain in `FileService`/`FolderService` but returning a `SharedLink` DTO.
- **Users <-> Groups <-> Memberships**: These should be refactored together. `GroupMembership` should be a first-class resource.
- **Files <-> Versions**: Should be folded into the `FileService` refactor.
- **Folders <-> Items**: List items response should be a DTO with pagination info.
- **List Responses**: Paginated/list endpoints should return specific response DTOs that include a Doctrine Collection of entries plus typed pagination metadata. Avoid a single generic public collection response for all endpoints.
- **Metadata**: Metadata should not remain raw arrays everywhere. Use typed DTO envelopes for Box-defined metadata structures while allowing custom template values as `array<string, mixed>`.
- **SharedLink**: Model as DTO/value object, not a top-level Resource initially.

## 5. Enum Candidates

- `BoxItemType`: file, folder, user, group, etc.
- `CollaborationRole`: editor, viewer, previewer, etc.
- `UserStatus`: active, inactive, etc.
- `SharedLinkAccess`: open, company, collaborators.

## 6. Request DTO Candidates

- `CreateFolderRequest`
- `UpdateFileRequest`
- `CreateCollaborationRequest`
- `InviteUserRequest`

## 7. Deferrals

The following should be deferred until *after* the initial architectural refactor to reduce risk:
- **Search**: Extensive query parameters; implement after `Box\Dto` pattern is stable.
- **Enterprise Events**: Complex due to long-polling and filtering; focus on structural refactor first.
- **Shield / Governance**: High complexity enterprise features.

## 8. Safest Migration Order (Revised)

1.  **Base Layer & Infrastructure**: `Box\Http`, `Box\Mapper`, `Box\Base`.
2.  **Core Enums**: `BoxItemType`, `CollaborationRole`.
3.  **Users**: Simple resource, low dependency.
4.  **Groups & Memberships**: Builds on Users.
5.  **Files & Versions**: High complexity.
6.  **Folders & Shared Links**: Builds on Files.
7.  **Collaborations**: Links all together.
8.  **Sign Requests / Webhooks**: New V1.0 features.

## Response DTO Candidates

- `FolderItemsResponse`
- `GroupListResponse`
- `GroupMembershipListResponse`
- `FileVersionListResponse`
- `CollaborationListResponse`
- `EventListResponse`
- `CommentListResponse`
- `TaskListResponse`
- `MetadataInstancesResponse`
