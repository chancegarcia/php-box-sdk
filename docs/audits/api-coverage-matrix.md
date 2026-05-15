# Box API Coverage Matrix

**Updated**: 2026-05-15
**Branch**: release-v1.0.0

---

## Methodology

Compared each service against the Box API reference (source: `https://developer.box.com/llms.txt`).
Legend: ✅ Implemented | ⏸ Deferred (documented below)

---

## FileService (`/files`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| POST | `/files/content` | Upload new file | ✅ `uploadFile()` |
| GET | `/files/{id}` | Get file details | ✅ `getFile()` |
| PUT | `/files/{id}` | Update file metadata/name | ✅ `updateFile()` |
| DELETE | `/files/{id}` | Delete file (to trash) | ✅ `deleteFile()` |
| GET | `/files/{id}/content` | Download file content | ✅ `downloadFile()` |
| PUT | `/files/{id}?fields=shared_link` | Add/update shared link | ✅ `createSharedLink()` |
| POST | `/files/{id}/content` | Upload new version | ⏸ Deferred |
| POST | `/files/{id}/copy` | Copy a file | ⏸ Deferred |
| GET | `/files/{id}/versions` | List file versions | ⏸ Deferred |
| GET | `/files/{id}/thumbnail/{ext}` | Get thumbnail | ⏸ Deferred |
| POST | `/files/upload-sessions` | Begin chunked upload | ⏸ Deferred |
| GET | `/files/{id}/collaborations` | List file collaborations | ⏸ Deferred |
| GET | `/files/{id}/comments` | List file comments | ⏸ Deferred |
| GET | `/files/{id}/tasks` | List file tasks | ⏸ Deferred |
| GET | `/files/{id}/watermark` | Get watermark | ⏸ Deferred |
| GET/POST/DELETE | `/files/{id}/metadata/...` | Metadata | ⏸ Deferred |
| GET/POST/DELETE | `/files/{id}/trash` | Trash management | ⏸ Deferred |

---

## FolderService (`/folders`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| GET | `/folders/{id}` | Get folder | ✅ `getFolder()` |
| POST | `/folders` | Create folder | ✅ `createFolder()` |
| PUT | `/folders/{id}` | Update folder | ✅ `updateFolder()` |
| DELETE | `/folders/{id}` | Delete folder | ✅ `deleteFolder()` |
| GET | `/folders/{id}/items` | List folder items | ✅ `getFolderItems()` |
| POST | `/folders/{id}/copy` | Copy folder | ✅ `copyFolder()` |
| GET | `/folders/{id}/collaborations` | List collaborations | ✅ `getFolderCollaborations()` |
| PUT | `/folders/{id}?fields=shared_link` | Add/update shared link | ✅ `createSharedLink()` |
| GET | `/shared_items` | Resolve shared link | ✅ `getFolderBySharedUri()` |
| GET/DELETE | `/folders/{id}/trash` | Trash management | ⏸ Deferred |
| POST/GET/DELETE | `/folder-locks` | Folder locks | ⏸ Deferred |
| GET/POST/PUT/DELETE | `/folders/{id}/metadata/...` | Metadata | ⏸ Deferred |
| GET/PUT/DELETE | `/folders/{id}/watermark` | Watermark | ⏸ Deferred |

---

## UserService (`/users`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| GET | `/users/me` | Get current user | ✅ `getCurrentUser()` |
| GET | `/users/{id}` | Get user by ID | ✅ `getUser()` |
| GET | `/users` | List enterprise users | ✅ `listUsers()` |
| POST | `/users` | Create managed user | ⏸ Deferred (admin) |
| PUT | `/users/{id}` | Update user | ⏸ Deferred |
| DELETE | `/users/{id}` | Delete user | ⏸ Deferred |
| GET/POST/DELETE | `/users/{id}/email-aliases/...` | Email aliases | ⏸ Deferred |
| GET/POST/DELETE | `/users/{id}/avatar` | Avatar | ⏸ Deferred |
| GET | `/users/{id}/memberships` | User's group memberships | ⏸ Deferred |

---

## GroupService (`/groups`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| GET | `/groups/{id}/memberships` | List group members | ✅ `getGroupMembershipList()` |
| GET | `/groups` | List all groups | ✅ `listGroups()` |
| POST | `/groups` | Create group | ✅ `createGroup()` |
| GET | `/groups/{id}` | Get group | ✅ `getGroup()` |
| DELETE | `/groups/{id}` | Delete group | ✅ `deleteGroup()` |
| POST | `/group-memberships` | Add group member | ✅ `addGroupMember()` |
| DELETE | `/group-memberships/{id}` | Remove group member | ✅ `removeGroupMember()` |
| PUT | `/groups/{id}` | Update group | ⏸ Deferred |
| GET | `/group-memberships/{id}` | Get membership details | ⏸ Deferred |
| GET | `/groups/{id}/collaborations` | Group collaborations | ⏸ Deferred |

---

## CollaborationService (`/collaborations`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| POST | `/collaborations` | Add collaboration (folder) | ✅ `addCollaboration()` (folders only) |
| GET | `/folders/{id}/collaborations` | Get folder collabs | ✅ `getFolderCollaborations()` |
| GET | `/collaborations/{id}` | Get collaboration | ✅ `getCollaboration()` |
| PUT | `/collaborations/{id}` | Update collaboration | ✅ `updateCollaboration()` |
| DELETE | `/collaborations/{id}` | Delete collaboration | ✅ `deleteCollaboration()` |
| POST | `/collaborations` | Add collaboration (file) | ✅ `addCollaboration()` (files supported) |
| GET | `/files/{id}/collaborations` | Get file collabs | ⏸ Deferred |
| GET | `/collaborations` | List pending invites | ⏸ Deferred |
| POST/GET/DELETE | `/collaboration-whitelist-*` | Domain allowlist | ⏸ Deferred |

---

## UserEventService (`/events`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| GET | `/events` | Get events (stream_type, position, limit) | ✅ `getEvents()` |
| OPTIONS | `/events` | Long-poll URL | ⏸ Deferred |

---

## SearchService (`/search`)

| Method | Path | Operation | Status |
|--------|------|-----------|--------|
| GET | `/search` | Search (query, limit, offset, type) | ✅ `search()` |
| — | advanced params | content_types, folder scope, date ranges | ⏸ Deferred |

---

## Service Base Changes (v1.0.0)

Changes completed during the v1.0.0 development cycle:

| Item | Action |
|------|--------|
| `$clientId`, `$clientSecret` properties | Removed — credentials live on `Client` and `AuthProvider` |
| `getClientId()`, `setClientId()` | Removed from `Service` and `ServiceInterface` |
| `getClientSecret()`, `setClientSecret()` | Removed from `Service` and `ServiceInterface` |
| `refreshConnection()` | Removed — 401 handling now inlines `throw $bre` |
| `sendDeleteToBox(string $uri): void` | Added — standard DELETE helper for void operations |
| `Client::configureService()` | Removed `setClientId`/`setClientSecret` calls on services |
| `queryBox()`, `putIntoBox()`, `getFromBox()`, `sendUpdateToBox()` | Removed from `ServiceInterface` and `Service` in v1.0.0 |
| `handleResponseContent()` | Removed from `ServiceInterface` and `Service` in v1.0.0 |
| `ServiceInterface::TOKEN_URI`, `REVOKE_URI` | Removed (vestigial constants, never used) |

---

## Deferred Endpoint Families (Post-v1.0)

The following Box API families are explicitly out of scope for v1.0:

- **Comments** (`/comments`) — file commenting
- **Tasks** (`/tasks`, `/task-assignments`) — task/review workflows
- **Metadata** (`/metadata-templates`, `/files/{id}/metadata`) — structured metadata
- **Webhooks** (`/webhooks`) — webhook management CRUD (signature verification shipped in v1.0)
- **Collections** (`/collections`) — starred items / favorites
- **Web Links** (`/web-links`) — Box web link objects
- **Zip Downloads** (`/zip-downloads`) — multi-file download
- **Trash Management** (`/files/{id}/trash`, `/folders/{id}/trash`) — explicit trash CRUD
- **Chunked Upload** (`/files/upload-sessions`) — multipart upload sessions
- **File Versions** (`/files/{id}/versions`) — version history management
- **Folder Locks** (`/folder-locks`) — lock management
- **Watermarks** — file/folder watermarking
- **User admin operations** — create/update/delete users, email aliases, avatar
- **Group update** — PUT `/groups/{id}`
- **Enterprise events** — admin log stream (only user event stream implemented)
- **Search advanced params** — content_types, ancestor_folder_ids, date ranges, metadata filters
- **Collaboration allowlist** — domain restriction management
- **Long-poll events** — OPTIONS `/events`
- **App item associations**, **recent items** — low priority for SDK use cases
