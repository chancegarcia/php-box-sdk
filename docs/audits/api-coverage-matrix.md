# Box API Coverage Matrix

**Updated**: 2026-05-16
**Goal**: 90–100% parity with the current Box API reference
**Source**: https://developer.box.com/reference

**Legend**: ✅ Implemented | 📋 Planned | ⬜ Not Implemented | 🆕 New (Box API — added since last audit)

---

## Coverage Summary

| Endpoint Family | Implemented | Total Tracked | % |
|:---|---:|---:|---:|
| Files | 11 | 17 | 65% |
| File Versions | 0 | 4 | 0% |
| Chunked Uploads | 5 | 6 | 83% |
| Folders | 9 | 13 | 69% |
| Users | 3 | 9 | 33% |
| Groups | 7 | 10 | 70% |
| Collaborations | 5 | 9 | 56% |
| Events | 1 | 2 | 50% |
| Search | 1 | 2 | 50% |
| Comments | 0 | 5 | 0% |
| Tasks | 0 | 6 | 0% |
| Metadata | 0 | 6 | 0% |
| Webhooks | 0 | 5 | 0% |
| Collections | 0 | 4 | 0% |
| Web Links | 0 | 4 | 0% |
| Trash | 0 | 3 | 0% |
| Folder Locks | 0 | 3 | 0% |
| Watermarks | 0 | 4 | 0% |
| Zip Downloads | 0 | 1 | 0% |
| Shared Items | 1 | 1 | 100% |

> Row counts reflect endpoints tracked in this document, not the full Box API surface. Expand stubs as audit passes are completed.

---

## Files (`/files`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/files/content` | Upload new file | `FileService::uploadFile()` | ✅ |
| GET | `/files/{id}` | Get file details | `FileService::getFile()` | ✅ |
| PUT | `/files/{id}` | Update file metadata / name | `FileService::updateFile()` | ✅ |
| DELETE | `/files/{id}` | Delete file (to trash) | `FileService::deleteFile()` | ✅ |
| GET | `/files/{id}/content` | Download file content | `FileService::downloadFile()` | ✅ |
| PUT | `/files/{id}?fields=shared_link` | Add / update shared link | `FileService::createSharedLink()` | ✅ |
| POST | `/files/{id}/copy` | Copy a file | `FileService` — `copyFile()` (TBD) | ⬜ |
| GET | `/files/{id}/thumbnail/{ext}` | Get thumbnail | `FileService` — `getThumbnail()` (TBD) | ⬜ |
| GET | `/files/{id}/collaborations` | List file collaborations | `CollaborationService` — `getFileCollaborations()` (TBD) | ⬜ |
| GET | `/files/{id}/comments` | List file comments | `CommentService` (TBD) | ⬜ |
| GET | `/files/{id}/tasks` | List file tasks | `TaskService` (TBD) | ⬜ |
| GET | `/files/{id}/watermark` | Get watermark | `WatermarkService` (TBD) | ⬜ |
| PUT | `/files/{id}/watermark` | Apply watermark | `WatermarkService` (TBD) | ⬜ |
| DELETE | `/files/{id}/watermark` | Remove watermark | `WatermarkService` (TBD) | ⬜ |
| GET/POST/DELETE | `/files/{id}/metadata/{scope}/{key}` | File metadata | `MetadataService` (TBD) | ⬜ |
| GET | `/files/{id}/trash` | Get trashed file | `TrashService` (TBD) | ⬜ |
| DELETE | `/files/{id}/trash` | Permanently delete file | `TrashService` (TBD) | ⬜ |

---

## File Versions (`/files/{id}/versions`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/files/{id}/versions` | List file versions | `FileVersionService` (TBD) | ⬜ |
| GET | `/files/{id}/versions/{version_id}` | Get a file version | `FileVersionService` (TBD) | ⬜ |
| DELETE | `/files/{id}/versions/{version_id}` | Delete a file version | `FileVersionService` (TBD) | ⬜ |
| POST | `/files/{id}/versions/{version_id}/promote` | Promote a version to current | `FileVersionService` (TBD) | ⬜ |

---

## Chunked Uploads (`/files/upload-sessions`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/files/upload-sessions` | Create upload session | `FileService::createUploadSession()` | ✅ |
| PUT | `/files/upload-sessions/{id}` | Upload a part | `FileService::uploadPart()` | ✅ |
| GET | `/files/upload-sessions/{id}/parts` | List uploaded parts | `FileService::listUploadSessionParts()` | ✅ |
| POST | `/files/upload-sessions/{id}/commit` | Commit session | `FileService::commitUploadSession()` | ✅ |
| DELETE | `/files/upload-sessions/{id}` | Abort session | `FileService::abortUploadSession()` | ✅ |
| GET | `/files/upload-sessions/{id}` | Get upload session info | `FileService` — `getUploadSession()` (TBD) | ⬜ |

---

## Folders (`/folders`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/folders/{id}` | Get folder | `FolderService::getFolder()` | ✅ |
| POST | `/folders` | Create folder | `FolderService::createFolder()` | ✅ |
| PUT | `/folders/{id}` | Update folder | `FolderService::updateFolder()` | ✅ |
| DELETE | `/folders/{id}` | Delete folder | `FolderService::deleteFolder()` | ✅ |
| GET | `/folders/{id}/items` | List folder items | `FolderService::getFolderItems()` | ✅ |
| POST | `/folders/{id}/copy` | Copy folder | `FolderService::copyFolder()` | ✅ |
| GET | `/folders/{id}/collaborations` | List collaborations | `FolderService::getFolderCollaborations()` | ✅ |
| PUT | `/folders/{id}?fields=shared_link` | Add / update shared link | `FolderService::createSharedLink()` | ✅ |
| GET | `/folders/{id}/watermark` | Get watermark | `WatermarkService` (TBD) | ⬜ |
| PUT | `/folders/{id}/watermark` | Apply watermark | `WatermarkService` (TBD) | ⬜ |
| DELETE | `/folders/{id}/watermark` | Remove watermark | `WatermarkService` (TBD) | ⬜ |
| GET | `/folders/{id}/trash` | Get trashed folder | `TrashService` (TBD) | ⬜ |
| DELETE | `/folders/{id}/trash` | Permanently delete folder | `TrashService` (TBD) | ⬜ |

---

## Shared Items (`/shared_items`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/shared_items` | Resolve shared link → folder | `FolderService::getFolderBySharedUri()` | ✅ |

---

## Folder Locks (`/folder-locks`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/folder-locks` | List folder locks | `FolderLockService` (TBD) | ⬜ |
| POST | `/folder-locks` | Create folder lock | `FolderLockService` (TBD) | ⬜ |
| DELETE | `/folder-locks/{id}` | Delete folder lock | `FolderLockService` (TBD) | ⬜ |

---

## Users (`/users`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/users/me` | Get current user | `UserService::getCurrentUser()` | ✅ |
| GET | `/users/{id}` | Get user by ID | `UserService::getUser()` | ✅ |
| GET | `/users` | List enterprise users | `UserService::listUsers()` | ✅ |
| POST | `/users` | Create managed user | `UserService` — `createUser()` (TBD) | ⬜ |
| PUT | `/users/{id}` | Update user | `UserService` — `updateUser()` (TBD) | ⬜ |
| DELETE | `/users/{id}` | Delete user | `UserService` — `deleteUser()` (TBD) | ⬜ |
| GET | `/users/{id}/memberships` | User's group memberships | `GroupService` — `getUserMemberships()` (TBD) | ⬜ |
| GET/POST/DELETE | `/users/{id}/email-aliases/{alias_id}` | Email aliases | `UserService` — email alias methods (TBD) | ⬜ |
| GET/POST/DELETE | `/users/{id}/avatar` | Avatar | `UserService` — avatar methods (TBD) | ⬜ |

---

## Groups (`/groups`, `/group-memberships`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/groups` | List all groups | `GroupService::listGroups()` | ✅ |
| POST | `/groups` | Create group | `GroupService::createGroup()` | ✅ |
| GET | `/groups/{id}` | Get group | `GroupService::getGroup()` | ✅ |
| DELETE | `/groups/{id}` | Delete group | `GroupService::deleteGroup()` | ✅ |
| GET | `/groups/{id}/memberships` | List group members | `GroupService::getGroupMembershipList()` | ✅ |
| POST | `/group-memberships` | Add group member | `GroupService::addGroupMember()` | ✅ |
| DELETE | `/group-memberships/{id}` | Remove group member | `GroupService::removeGroupMember()` | ✅ |
| PUT | `/groups/{id}` | Update group | `GroupService` — `updateGroup()` (TBD) | ⬜ |
| GET | `/group-memberships/{id}` | Get membership details | `GroupService` — `getGroupMembership()` (TBD) | ⬜ |
| GET | `/groups/{id}/collaborations` | Group collaborations | `CollaborationService` — `getGroupCollaborations()` (TBD) | ⬜ |

---

## Collaborations (`/collaborations`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/collaborations` | Add collaboration (file or folder) | `CollaborationService::addCollaboration()` | ✅ |
| GET | `/collaborations/{id}` | Get collaboration | `CollaborationService::getCollaboration()` | ✅ |
| PUT | `/collaborations/{id}` | Update collaboration | `CollaborationService::updateCollaboration()` | ✅ |
| DELETE | `/collaborations/{id}` | Delete collaboration | `CollaborationService::deleteCollaboration()` | ✅ |
| GET | `/folders/{id}/collaborations` | Get folder collaborations | `CollaborationService::getFolderCollaborations()` | ✅ |
| GET | `/files/{id}/collaborations` | Get file collaborations | `CollaborationService` — `getFileCollaborations()` (TBD) | ⬜ |
| GET | `/collaborations` | List pending invites | `CollaborationService` — `listPendingCollaborations()` (TBD) | ⬜ |
| GET/POST/DELETE | `/collaboration-whitelist-entries` | Domain allowlist entries | `CollaborationAllowlistService` (TBD) | ⬜ |
| GET/POST/DELETE | `/collaboration-whitelist-exempt-targets` | Allowlist exemptions | `CollaborationAllowlistService` (TBD) | ⬜ |

---

## Events (`/events`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/events` | Get events (user stream) | `UserEventService::getEvents()` | ✅ |
| OPTIONS | `/events` | Long-poll URL | `UserEventService` — `getLongPollUrl()` (TBD) | ⬜ |

---

## Search (`/search`)

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/search` | Search (query, limit, offset, type) | `SearchService::search()` | ✅ |
| GET | `/search` (advanced) | content_types, ancestor scope, date ranges, metadata filters | `SearchService::search()` — advanced params (TBD) | ⬜ |

---

## Comments (`/comments`)

> Suggested service: `CommentService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/comments` | Create comment | `CommentService` (TBD) | ⬜ |
| GET | `/comments/{id}` | Get comment | `CommentService` (TBD) | ⬜ |
| PUT | `/comments/{id}` | Update comment | `CommentService` (TBD) | ⬜ |
| DELETE | `/comments/{id}` | Delete comment | `CommentService` (TBD) | ⬜ |
| GET | `/files/{id}/comments` | List file comments | `CommentService` (TBD) | ⬜ |

---

## Tasks (`/tasks`, `/task-assignments`)

> Suggested service: `TaskService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/tasks` | Create task | `TaskService` (TBD) | ⬜ |
| GET | `/tasks/{id}` | Get task | `TaskService` (TBD) | ⬜ |
| PUT | `/tasks/{id}` | Update task | `TaskService` (TBD) | ⬜ |
| DELETE | `/tasks/{id}` | Delete task | `TaskService` (TBD) | ⬜ |
| GET | `/files/{id}/tasks` | List file tasks | `TaskService` (TBD) | ⬜ |
| GET/POST/PUT/DELETE | `/task-assignments/{id}` | Task assignments | `TaskService` (TBD) | ⬜ |

---

## Metadata (`/metadata-templates`, `/files/{id}/metadata`, `/folders/{id}/metadata`)

> Suggested service: `MetadataService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/metadata-templates/schema` | List metadata templates | `MetadataService` (TBD) | ⬜ |
| POST | `/metadata-templates/schema` | Create metadata template | `MetadataService` (TBD) | ⬜ |
| GET | `/files/{id}/metadata/{scope}/{key}` | Get file metadata instance | `MetadataService` (TBD) | ⬜ |
| POST | `/files/{id}/metadata/{scope}/{key}` | Create file metadata instance | `MetadataService` (TBD) | ⬜ |
| GET | `/folders/{id}/metadata/{scope}/{key}` | Get folder metadata instance | `MetadataService` (TBD) | ⬜ |
| POST | `/folders/{id}/metadata/{scope}/{key}` | Create folder metadata instance | `MetadataService` (TBD) | ⬜ |

---

## Webhooks (`/webhooks`)

> Signature verification shipped in v1.0 (`WebhookVerifier`). CRUD management deferred.
> Suggested service: `WebhookService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/webhooks` | List webhooks | `WebhookService` (TBD) | ⬜ |
| POST | `/webhooks` | Create webhook | `WebhookService` (TBD) | ⬜ |
| GET | `/webhooks/{id}` | Get webhook | `WebhookService` (TBD) | ⬜ |
| PUT | `/webhooks/{id}` | Update webhook | `WebhookService` (TBD) | ⬜ |
| DELETE | `/webhooks/{id}` | Delete webhook | `WebhookService` (TBD) | ⬜ |

---

## Collections (`/collections`)

> Suggested service: `CollectionService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/collections` | List collections | `CollectionService` (TBD) | ⬜ |
| GET | `/collections/{id}/items` | List collection items | `CollectionService` (TBD) | ⬜ |
| PUT | `/files/{id}` (collections field) | Add file to collection | `CollectionService` (TBD) | ⬜ |
| PUT | `/folders/{id}` (collections field) | Add folder to collection | `CollectionService` (TBD) | ⬜ |

---

## Web Links (`/web-links`)

> Suggested service: `WebLinkService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/web-links` | Create web link | `WebLinkService` (TBD) | ⬜ |
| GET | `/web-links/{id}` | Get web link | `WebLinkService` (TBD) | ⬜ |
| PUT | `/web-links/{id}` | Update web link | `WebLinkService` (TBD) | ⬜ |
| DELETE | `/web-links/{id}` | Delete web link | `WebLinkService` (TBD) | ⬜ |

---

## Trash (`/trash`, `/files/{id}/trash`, `/folders/{id}/trash`)

> Suggested service: `TrashService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/folders/trash/items` | List all trashed items | `TrashService` (TBD) | ⬜ |
| GET/DELETE | `/files/{id}/trash` | Get / permanently delete trashed file | `TrashService` (TBD) | ⬜ |
| GET/DELETE | `/folders/{id}/trash` | Get / permanently delete trashed folder | `TrashService` (TBD) | ⬜ |

---

## Watermarks (`/files/{id}/watermark`, `/folders/{id}/watermark`)

> Suggested service: `WatermarkService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| GET | `/files/{id}/watermark` | Get file watermark | `WatermarkService` (TBD) | ⬜ |
| PUT | `/files/{id}/watermark` | Apply file watermark | `WatermarkService` (TBD) | ⬜ |
| DELETE | `/files/{id}/watermark` | Remove file watermark | `WatermarkService` (TBD) | ⬜ |
| GET | `/folders/{id}/watermark` | Get folder watermark | `WatermarkService` (TBD) | ⬜ |
| PUT | `/folders/{id}/watermark` | Apply folder watermark | `WatermarkService` (TBD) | ⬜ |
| DELETE | `/folders/{id}/watermark` | Remove folder watermark | `WatermarkService` (TBD) | ⬜ |

---

## Zip Downloads (`/zip-downloads`)

> Suggested service: `ZipDownloadService`

| Method | Path | Operation | Service / Method | Status |
|:---|:---|:---|:---|:---|
| POST | `/zip-downloads` | Create zip download | `ZipDownloadService` (TBD) | ⬜ |

---

## Notes

- **`(TBD)`** — service or method name is a suggestion based on Box API conventions; the final name may change when the endpoint is implemented.
- **Coverage summary** counts reflect endpoints tracked in this document. Expand each section by consulting the [Box API reference](https://developer.box.com/reference) before implementing a new family.
- **Watermark rows** appear under both Files and Folders sections above for completeness; the `WatermarkService` would handle both.
