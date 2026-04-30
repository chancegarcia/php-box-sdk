# Client Robustness and Implementation Notes

## Purpose

This document provides internal implementation notes and design rationale for the Box PHP SDK. It serves as a guide for contributors and identifies both current v0.11.x improvements and the long-term v1.0 vision.

## Current v0.11.x Design Patterns

### 1. Robust Client Wrappers
The `Box\Client` acts as the primary entry point for common workflows. High-level methods like `uploadFileToBox()` wrap lower-level `Connection` calls to provide a more intuitive API.

- **Completed:** Added `parentId` support to `Client::uploadFileToBox()`.
- **Completed:** Added `exchangeAuthorizationCodeForToken()` as a descriptive alias for the code-to-token exchange.

### 2. ID Handling
Box IDs are handled as `string|int` throughout the v0.11.x branch. This ensures compatibility with large numeric IDs that might exceed 32-bit integer limits or contain leading zeros while still allowing convenient integer usage for legacy code.

### 3. File Streaming
The `Box\Http\FileStream` abstraction allows the SDK to handle file content from various sources (strings, resources, local paths) uniformly. This is particularly useful for serverless environments or memory-constrained integrations where writing to disk is undesirable.

### 4. Model Validation
Models use `Box\Model\BaseModelTrait` and `Box\Model\ModelTrait` for shared logic. Validation in v0.11.x has been improved to avoid side effects during class verification.

## Future v1.0 Breaking Improvements (Planned)

### 1. Service-Oriented Architecture
Redesign `Client` as a lightweight facade that delegates to specialized services (e.g., `FilesService`, `FoldersService`).

### 2. Dependency Injection and Factories
Replace mutable class-string configuration (setters like `setConnectionClass`) with standard constructor injection or dedicated factory patterns.

### 3. Strict Typing
Transition to strict `string` typing for all Box IDs to align with Box's API standards and reduce ambiguity.

---
*For user-facing usage instructions, please see the [Programmatic Usage Guide](programmatic-usage.md).*

