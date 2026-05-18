# API Coverage

This document lists every Box API endpoint supported by the SDK, grouped by service class. Method signatures are as implemented; return types reflect actual v1.0 behavior.

Deferred endpoints (file versions, metadata, tasks, comments, webhooks CRUD, etc.) are tracked in [API Coverage Matrix](../audits/api-coverage-matrix.md).

---

## FileService (`Box\Service\File\FileService`)

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `getFile(string $id): File` | GET | `GET /2.0/files/{id}` | |
| `updateFile(File $file): File` | PUT | `PUT /2.0/files/{id}` | Updates `name` and `description` |
| `deleteFile(string $id): void` | DELETE | `DELETE /2.0/files/{id}` | |
| `downloadFile(string $id): string` | GET | `GET /2.0/files/{id}/content` | Returns raw file bytes as a string |
| `createSharedLink(File $file, SharedLink\|CreateSharedLinkRequest\|array\|null $sharedLink): File` | PUT | `PUT /2.0/files/{id}` | Pass `null` to create a default shared link |
| `uploadFile(string\|FileStream $file, string\|int $parentId): File` | POST | `POST upload.box.com/api/2.0/files/content` | Use for files under ~50 MB |
| `chunkedUpload(string\|FileStream $file, string\|int $parentId, ?int $partSize): File` | — | (orchestrates chunked upload session API) | Manages session creation, part uploads, and commit; aborts on failure; see low-level methods below |

### Chunked Upload — Low-Level Session API

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `createUploadSession(string\|int $parentId, string $filename, int $fileSize): UploadSession` | POST | `POST upload.box.com/api/2.0/files/upload-sessions` | Returns session ID, recommended part size, and upload URL |
| `uploadPart(string $sessionId, string $data, int $offset, int $totalSize): UploadPart` | PUT | `PUT upload.box.com/api/2.0/files/upload-sessions/{id}` | Sends `Content-Range` and SHA1 `Digest` header automatically |
| `listUploadSessionParts(string $sessionId): UploadPart[]` | GET | `GET upload.box.com/api/2.0/files/upload-sessions/{id}/parts` | Use for resumable upload orchestration |
| `commitUploadSession(string $sessionId, array $parts, string $fileSha1): File` | POST | `POST upload.box.com/api/2.0/files/upload-sessions/{id}/commit` | Finalizes the session and returns the committed `File` |
| `abortUploadSession(string $sessionId): void` | DELETE | `DELETE upload.box.com/api/2.0/files/upload-sessions/{id}` | Called automatically by `chunkedUpload()` on failure |

---

## FolderService (`Box\Service\Folder\FolderService`)

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `getFolder(string\|int $id): Folder` | GET | `GET /2.0/folders/{id}` | |
| `getFolderItems(string\|int $id, int $limit, int $offset): Folder` | GET | `GET /2.0/folders/{id}/items` | Returns `Folder` with `item_collection` populated; defaults: limit=100, offset=0 |
| `getFolderBySharedUri(string $sharedUri): Folder\|false` | GET | `GET /2.0/shared_items` | Passes the shared link URL in the `BoxApi` request header |
| `createFolder(string $name, string\|int $parentId, array $options): Folder` | POST | `POST /2.0/folders` | `$parentId` defaults to `0` (root) |
| `updateFolder(Folder $folder, string\|bool\|null $ifMatch): Folder` | PUT | `PUT /2.0/folders/{id}` | Pass `$ifMatch = true` to use the folder's ETag for optimistic locking |
| `deleteFolder(string $id, bool $recursive): void` | DELETE | `DELETE /2.0/folders/{id}` | Pass `$recursive = true` to delete non-empty folders |
| `createSharedLink(Folder $folder, ?array $params): Folder` | PUT | `PUT /2.0/folders/{id}` | Defaults to `collaborators` access if `$params` is null |
| `copyFolder(Folder $originalFolder, Folder $parent, ?string $name): Folder` | POST | `POST /2.0/folders/{id}/copy` | |

---

## UserService (`Box\Service\UserService`)

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `getCurrentUser(): User` | GET | `GET /2.0/users/me` | Returns the authenticated user's details |
| `getUser(string $userId): User` | GET | `GET /2.0/users/{id}` | Requires admin or JWT S2S auth when accessing other users |
| `listUsers(int $limit, int $offset): PagedResult<User>` | GET | `GET /2.0/users` | Enterprise-only; requires admin auth |

---

## SearchService (`Box\Service\SearchService`)

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `search(?string $query, int\|string\|null $limit, int\|string\|null $offset, ?string $type): array` | GET | `GET /2.0/search` | `$query` is required; `$type` filters by `file`, `folder`, etc.; returns raw response array — Box returns heterogeneous entries (files, folders, web links) that cannot be strongly typed without a discriminated union; typed return deferred to a future minor release |

---

## CollaborationService (`Box\Service\Collaboration\CollaborationService`)

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `getCollaboration(string $id): Collaboration` | GET | `GET /2.0/collaborations/{id}` | |
| `addCollaboration(Folder\|File\|string\|int $item, mixed $collaborator, string $role): Collaboration` | POST | `POST /2.0/collaborations` | `$role` defaults to `editor` |
| `updateCollaboration(Collaboration $collaboration): Collaboration` | PUT | `PUT /2.0/collaborations/{id}` | Updates `role` and `status` fields |
| `deleteCollaboration(string $id): void` | DELETE | `DELETE /2.0/collaborations/{id}` | |
| `getFolderCollaborations(Folder $folder): PagedResult<Collaboration>` | GET | `GET /2.0/folders/{id}/collaborations` | |

---

## GroupService (`Box\Service\Group\GroupService`)

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `listGroups(int $limit, int $offset): PagedResult<Group>` | GET | `GET /2.0/groups` | Enterprise-only |
| `getGroup(string $id): Group` | GET | `GET /2.0/groups/{id}` | |
| `createGroup(string $name, array $options): Group` | POST | `POST /2.0/groups` | |
| `deleteGroup(string $id): void` | DELETE | `DELETE /2.0/groups/{id}` | |
| `getGroupMembershipList(string\|int $groupId, int\|string $limit, int\|string $offset): PagedResult<GroupMembership>` | GET | `GET /2.0/groups/{id}/memberships` | |
| `addGroupMember(string $groupId, string $userId, string $role): GroupMembership` | POST | `POST /2.0/group_memberships` | `$role` defaults to `member` |
| `removeGroupMember(string $membershipId): void` | DELETE | `DELETE /2.0/group_memberships/{id}` | `$membershipId` is the membership record ID, not the user ID |

---

## UserEventService (`Box\Service\Event\UserEventService`)

Configure stream parameters before calling `getEvents()`.

| Method | HTTP | Box Endpoint | Notes |
|---|---|---|---|
| `getEvents(): EventResponse` | GET | `GET /2.0/events` | Returns `EventResponse` DTO with event entries and next stream position |

### Stream Configuration

| Setter | Default | Valid Values |
|---|---|---|
| `setStreamType(string $type)` | `all` | `all`, `changes`, `sync` |
| `setStreamPosition(string\|int $position)` | `now` | `now` or a numeric stream position |
| `setLimit(string\|int $limit)` | `100` | 1–500 |
