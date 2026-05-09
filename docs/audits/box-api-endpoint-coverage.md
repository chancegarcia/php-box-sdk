# Box API Endpoint Coverage Report

This document evaluates the current SDK support for the official Box API endpoints as of v0.11.0.

Official Box API Reference: [https://developer.box.com/reference](https://developer.box.com/reference)

## Summary Table

| Category | Official Reference URL | SDK Support Status | Relevant SDK Class/Service | Notes |
|---|---|---|---|---|
| Authorization | [Link](https://developer.box.com/reference/resources/oauth2/) | Implemented | `Box\Client` | OAuth2 supported. |
| Files | [Link](https://developer.box.com/reference/resources/file/) | Partially Implemented | `Box\Client`, `Box\File\File` | Basic operations supported; many advanced options missing. |
| Folders | [Link](https://developer.box.com/reference/resources/folder/) | Partially Implemented | `Box\Client`, `Box\Folder\Folder` | Basic operations supported. |
| Users | [Link](https://developer.box.com/reference/resources/user/) | Partially Implemented | `Box\Client`, `Box\User\User` | Basic user info supported. |
| Groups | [Link](https://developer.box.com/reference/resources/group/) | Partially Implemented | `Box\Client`, `Box\Group\Group` | Memberships partially supported. |
| Collaborations | [Link](https://developer.box.com/reference/resources/collaboration/) | Partially Implemented | `Box\Client`, `Box\Collaboration\Collaboration` | Basic collaboration management. |
| Shared Links | [Link](https://developer.box.com/reference/resources/shared-link/) | Partially Implemented | `Box\Client`, `Box\Item\SharedLink\SharedLink` | Supported as part of file/folder. |
| Search | [Link](https://developer.box.com/reference/get-search/) | Partially Implemented | `Box\Client::search` | Basic search functionality. |
| Events | [Link](https://developer.box.com/reference/resources/event/) | Partially Implemented | `Box\Service\Event\UserEventService` | User events supported. |
| File Versions | [Link](https://developer.box.com/reference/resources/file-version/) | Missing | - | Need to implement in v1.0. |
| File Requests | [Link](https://developer.box.com/reference/resources/file-request/) | Missing | - | Need to implement in v1.0. |
| Collections | [Link](https://developer.box.com/reference/resources/collection/) | Missing | - | Need to implement in v1.0. |
| Metadata | [Link](https://developer.box.com/reference/resources/metadata-template/) | Missing | - | Need to implement in v1.0. |
| Comments | [Link](https://developer.box.com/reference/resources/comment/) | Missing | - | Need to implement in v1.0. |
| Tasks | [Link](https://developer.box.com/reference/resources/task/) | Missing | - | Need to implement in v1.0. |
| Webhooks | [Link](https://developer.box.com/reference/resources/webhook/) | Missing | - | Need to implement in v1.0. |
| Sign Requests | [Link](https://developer.box.com/reference/resources/sign-request/) | Missing | - | Need to implement in v1.0. |
| Retention Policies | [Link](https://developer.box.com/reference/resources/retention-policy/) | Missing | - | Need to implement in v1.0. |
| Legal Holds | [Link](https://developer.box.com/reference/resources/legal-hold-policy/) | Missing | - | Need to implement in v1.0. |
| Classifications | [Link](https://developer.box.com/reference/resources/classification/) | Missing | - | Need to implement in v1.0. |

## Detailed Review

### Files & Folders
The SDK currently provides a `Box\Client` which acts as a "God object" for many file/folder operations.
- **Implemented**: Get file/folder, upload, create folder, copy folder, update folder, shared links.
- **Missing**: File version management, watermarking, locking, representations.

### Users & Groups
- **Implemented**: Basic user management and group membership listing.
- **Missing**: Enterprise user management, user invitation, group management (create/delete/update).

### Mapping Approach Evaluation
The current `ModelMapper` handles snake_case to camelCase conversion and basic hydration/serialization.
- **Strengths**: Simple, low-overhead, handles basic Box API response shapes.
- **Weaknesses**: 
    - No native support for nested object instantiation (currently relies on manual setter logic if implemented).
    - No explicit collection mapping (models currently store arrays of data).
    - No support for Box resource `type` based factory in the mapper itself.

## Recommendations for v1.0

### Missing Endpoints to Implement
1. **Collections**: Essential for many user-facing features.
2. **Comments & Tasks**: Critical for collaboration workflows.
3. **Metadata**: Important for enterprise data management.
4. **Webhooks**: Required for real-time integrations.
5. **Sign Requests**: High-value feature for many Box users.

### Missing Model Properties to Implement
- **File**: `is_externally_owned`, `allowed_invite_roles`, `has_collaborations`, `metadata`.
- **User**: `timezone`, `is_external_collab_restricted`, `is_exempt_from_device_limits`.
- **Folder**: `folder_upload_email`, `can_non_owners_invite`.

### Architecture Improvements
- **Service Layer**: Move logic out of `Box\Client` into dedicated service classes (e.g., `FileService`, `UserService`).
- **Hydrator**: Improve `ModelMapper` to support recursive hydration of nested models and collections.
- **Enums**: Replace string constants/literals for `role`, `status`, `type` with PHP Enums.
- **DTOs**: Use typed DTOs for complex request/response bodies that don't map directly to a single resource.
