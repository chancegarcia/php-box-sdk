# V1.0 API Coverage Audit

This document compares the current SDK resources and services against the [Box API Reference](https://developer.box.com/reference).

## 1. Current Resource Status

| Resource | Status | Notes |
| :--- | :--- | :--- |
| **Files** | Incomplete | Core metadata present. Missing: versions, representations, watermarking. |
| **Folders** | Incomplete | Core metadata present. Missing: items list pagination details, watermarking. |
| **Users** | Incomplete | Core metadata present. Missing: Enterprise user management, user avatar. |
| **Groups** | Incomplete | Core metadata present. Missing: Group memberships (partially present). |
| **Collaborations** | Incomplete | Core metadata present. Missing: Pending collaborations. |
| **Events** | Partial | User events supported. Enterprise events need more coverage. |
| **Shared Links** | Partial | Managed within Files/Folders. Should be a dedicated DTO/Resource. |

## 2. Missing Box API Resources

The following resources are missing and should be planned for V1.0 or shortly after:

- **Sign Requests**: High priority for V1.0.
- **File Requests**: High priority.
- **Metadata**: Support for metadata templates and instances.
- **Webhooks**: V2 Webhooks.
- **Retention Policies**: Required for enterprise compliance workflows.
- **Legal Holds**: Required for enterprise compliance workflows.
- **Classifications**: Metadata-based classification.
- **Collections**: (e.g., Favorites) - partially referenced in SDK but not fully implemented as a service.
- **Tasks & Task Assignments**: Missing.
- **Comments**: Missing.

## 3. Missing Services

- `SignRequestService`: To handle Box Sign operations.
- `MetadataService`: To handle custom metadata templates and values.
- `WebhookService`: To manage webhooks.
- `RetentionPolicyService`: To manage retention.
- `LegalHoldService`: To manage legal holds.
- `TaskService`: To manage tasks and assignments.
- `CommentService`: To manage comments on files.

## 4. Resource Relationships & Coupling

- **Files/Folders <-> Shared Links**: Tightly coupled. Shared link creation should be moved to a `SharedLinkService` or remain in `FileService`/`FolderService` but returning a `SharedLink` DTO/Resource.
- **Users <-> Groups <-> Memberships**: These should be refactored together to ensure IDs and relationships are correctly typed as `string`.
- **Files <-> Versions**: Should be folded into the `FileService` refactor.

## 5. Deferrals

The following should be deferred until *after* the initial architectural refactor to reduce risk:
- **Enterprise Events**: Complex due to long-polling and filtering; focus on structural refactor first.
- **Search**: Extensive query parameters; implement after `Box\Dto` pattern is stable.

## 6. Safest Migration Order (Proposed)

1.  **Base Layer**: `Box\Trait`, `Box\Base`, `Box\Http` (Hydration/Response).
2.  **Enums**: Define core `BoxItemType`, `CollaborationRole`, etc.
3.  **Users**: Simple resource, low dependency.
4.  **Groups & Memberships**: Builds on Users.
5.  **Files**: High complexity, core resource. Fold in **File Versions**.
6.  **Folders**: Similar to Files.
7.  **Collaborations**: Links Users/Groups to Files/Folders.
8.  **Sign Requests**: Add as new V1.0 service.
9.  **Webhooks**: Add as new V1.0 service.

### Migration Step Example: Users
- **Goal**: Move `Box\User\User` to `Box\Resource\User`.
- **Files Affected**: `src/User/User.php`, `src/User/UserInterface.php` (remove).
- **Risk**: Low.
- **Validation**: `composer test`, `composer analyse`.
