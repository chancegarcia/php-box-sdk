# Box SDK Model Signature Audit

**Status**: Historical / Research Reference. See [v1.0 Planning](../planning/v1/overview.md) for current implementation goals and typing rules.

## Summary
The purpose of this audit is to evaluate the current type specificity of models and class signatures within the `box-sdk` PHP project. This SDK is currently targeting v0.11.0 with PHP 8.4 as the baseline. The eventual goal for v1 is to move towards stronger model typing, particularly for date/time fields and identifiers.

This audit identifies areas where types are overly broad (e.g., `mixed`, untyped properties) and provides recommendations for transitional typing in v0.11.0 and stricter typing in v1.

## Audit Methodology
The following directories and classes were inspected:
- `src/File`
- `src/Folder`
- `src/User`
- `src/Group`
- `src/Collaboration`
- `src/Collection`
- `src/Event`
- `src/Item`

Box API documentation (https://developer.box.com/reference/) was used to compare the current implementation against the official schemas.

Types were classified as:
- **Too Broad**: Use of `mixed` or missing types where a specific type is known.
- **Transitional**: Union types that maintain backward compatibility (e.g., `DateTimeInterface|string|null`).
- **v1-Ready**: Strict types recommended for the next major version.

## Recommended Transitional Typing Rules for v0.11.0
- **Date/Time Fields**: Should accept `\DateTimeInterface|string|null` in PHPDoc and parameters to allow both existing raw strings and PHP date objects.
- **Box IDs**: Should remain `string|int|null` or `mixed` where existing logic requires it, to preserve compatibility with numeric string IDs.
- **Setters**: Must remain non-fluent and return `void`. Existing incorrect PHPDoc suggesting fluent returns should be fixed.
- **Arrays**: Use PHPDoc to specify item types (e.g., `array<string, mixed>` or `User[]`).
- **Nullability**: Explicitly mark nullable fields with `?` or `|null`.

## Recommended v1 Typing Rules
- **Date/Time Fields**: Normalize to `\DateTimeImmutable` internally. Getters should return `\DateTimeImmutable`.
- **Box IDs**: Consistently use `string` for all IDs as per Box API standards (even if they look like numbers, they are identifiers).
- **Value Objects/Enums**: Use PHP Enums for fields with fixed value sets (e.g., `status`, `role`, `type`).
- **DTOs**: Replace broad arrays with typed DTOs or Collections.
- **Native Types**: Use native PHP 8.4 type hints for all properties, parameters, and return types.

## Findings by Class

### Box\File\File
- **Path**: `src/File/File.php`
- **Issue**: Properties like `createdAt`, `modifiedAt`, `purgedAt`, `trashedAt`, `contentCreatedAt`, `contentModifiedAt` are typed as `mixed`.
- **Transitional Type**: `\DateTimeInterface|string|null`
- **v1 Type**: `\DateTimeImmutable|null`
- **Box API URL**: [https://developer.box.com/reference/resources/file/](https://developer.box.com/reference/resources/file/)
- **Confidence**: High

### Box\Folder\Folder
- **Path**: `src/Folder/Folder.php`
- **Issue**: Methods like `getCreatedAt`, `getModifiedAt` have no type hints or return `mixed`.
- **Transitional Type**: `\DateTimeInterface|string|null`
- **v1 Type**: `\DateTimeImmutable|null`
- **Box API URL**: [https://developer.box.com/reference/resources/folder/](https://developer.box.com/reference/resources/folder/)
- **Confidence**: High

### Box\User\User
- **Path**: `src/User/User.php`
- **Issue**: Extensive use of `mixed` for properties and missing types for methods (e.g., `getAddress`, `getJobTitle`, `getLanguage`).
- **Transitional Type**: Specific scalars (`string`, `int`, `bool`) or `null`.
- **v1 Type**: Native type hints.
- **Box API URL**: [https://developer.box.com/reference/resources/user/](https://developer.box.com/reference/resources/user/)
- **Confidence**: High

### Box\Collaboration\Collaboration
- **Path**: `src/Collaboration/Collaboration.php`
- **Issue**: `expiresAt`, `acknowledgedAt`, `createdAt`, `modifiedAt` are `mixed`.
- **Transitional Type**: `\DateTimeInterface|string|null`
- **v1 Type**: `\DateTimeImmutable|null`
- **Box API URL**: [https://developer.box.com/reference/resources/collaboration/](https://developer.box.com/reference/resources/collaboration/)
- **Confidence**: High

## Date/time field inventory
| Class | File | Field/Method | Current Type | Suggested v0.11.0 | Suggested v1 | Normalization Today? | Documentation URL |
|---|---|---|---|---|---|---|---|
| File | src/File/File.php | createdAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/file/) |
| File | src/File/File.php | modifiedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/file/) |
| File | src/File/File.php | contentCreatedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/file/) |
| File | src/File/File.php | contentModifiedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/file/) |
| File | src/File/File.php | trashedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/file/) |
| File | src/File/File.php | purgedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/file/) |
| Folder | src/Folder/Folder.php | createdAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/folder/) |
| Folder | src/Folder/Folder.php | modifiedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/folder/) |
| User | src/User/User.php | createdAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/user/) |
| User | src/User/User.php | modifiedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/user/) |
| Collaboration | src/Collaboration/Collaboration.php | createdAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/collaboration/) |
| Collaboration | src/Collaboration/Collaboration.php | modifiedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/collaboration/) |
| Collaboration | src/Collaboration/Collaboration.php | expiresAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/collaboration/) |
| Collaboration | src/Collaboration/Collaboration.php | acknowledgedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/collaboration/) |
| SharedLink | src/Item/SharedLink/SharedLink.php | unsharedAt | mixed | `\DateTimeInterface\|string\|null` | `\DateTimeImmutable\|null` | No | [Link](https://developer.box.com/reference/resources/shared-link/) |

## Mixed/unknown type inventory
| Class | File | Member/Method | Current Type | Suggested Type | Rationale | Confidence |
|---|---|---|---|---|---|---|
| File | src/File/File.php | id | mixed | `string\|int\|null` | Box IDs are identifiers. | High |
| File | src/File/File.php | size | mixed | `int\|null` | File size is an integer. | High |
| File | src/File/File.php | name | mixed | `string\|null` | File name is a string. | High |
| User | src/User/User.php | login | mixed | `string\|null` | User login is usually an email. | High |
| User | src/User/User.php | maxUploadSize | mixed | `float\|int\|null` | Large integers may be floats in PHP. | High |
| Group | src/Group/Group.php | name | mixed | `string\|null` | Group name is a string. | High |

## Box API Compatibility Observations
- **SharedLink**: Current implementation uses `unsharedAt` while documentation refers to it in some contexts as `unshared_at` (RFC3339).
- **User**: Many fields like `timezone`, `job_title`, `phone` are present and mapped but typed as `mixed`.
- **Enums**: Fields like `item_status` (active, trashed, deleted) and `role` (editor, viewer, etc.) are currently just strings. v1 should use Enums.

## Proposed Implementation Plan

### Safe v0.11.0 Changes
- Fix setter PHPDoc to return `void` instead of claiming to return `$this` or the class type.
- Add `\DateTimeInterface|string|null` to all date-related PHPDocs.
- Add specific type hints to PHPDoc for scalar fields (e.g., `string $name`, `int $size`).
- Add item types for arrays in PHPDoc (e.g., `Collection\ArrayCollectionInterface<int, File>`).

### Optional v0.11.x Improvements
- Add internal normalization in setters to convert strings to `\DateTime` if desired, while still accepting strings.

### v1 Breaking Changes
- Replace `mixed` properties with native type hints.
- Change Box IDs to `string` only.
- Change date fields to `\DateTimeImmutable` only.
- Implement Enums for status and role fields.

## Open Questions
- **DateTimeInterface vs DateTimeImmutable**: v1 should prefer `DateTimeImmutable` for safety.
- **Setters for Date Fields**: Should they continue to accept strings in v1? Recommendation: No, require objects or provide a dedicated `setFromRawString` method.
- **Box IDs**: Should they stay `string|int`? Recommendation: Box API officially uses strings for IDs; the SDK should follow this in v1.
