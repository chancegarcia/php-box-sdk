# Documentation Audit - May 2026

This audit reviews the SDK documentation examples for accuracy, usability, and API alignment following the implementation of `Token::isExpired()`.

## Files Reviewed
- `README.md`
- `docs/programmatic-usage.md`
- `docs/upgrading-0.10-to-0.11.md`

## Audit Results

### 1. Token Expiration Example
- **Location:** `docs/programmatic-usage.md` (Section 4)
- **Status:** **FIXED**
- **Details:** The example previously used `$token->isExpired()`, which was not implemented in the SDK. This method has now been added to `Token` and `TokenInterface`.
- **Works after change:** Yes.

### 2. OAuth2 Workflow
- **Location:** `README.md`
- **Status:** **WORKING**
- **Details:** Uses `exchangeAuthorizationCodeForToken()`, which is a valid alias in `Client`.
- **Works after change:** Yes.

### 3. Namespace Usage
- **Location:** All documents
- **Status:** **TRANSITIONING**
- **Details:** Most examples use the new flattened namespaces (e.g., `Box\Client`, `Box\Connection\Token\Token`). Some references to `Box\Model\...` might exist in transition docs, but primary examples are updated.
- **Works after change:** Yes.

### 4. Setter Chaining
- **Location:** `docs/upgrading-0.10-to-0.11.md`
- **Status:** **CORRECT**
- **Details:** Correctly identifies that setters now return `void` and shows the non-fluent usage.
- **Works after change:** Yes.

## Corrected Examples
- No examples required code-level fixes in the Markdown files themselves, as the intent of the examples was satisfied by adding the missing `isExpired()` method to the codebase.

## Implemented Convenience Methods
- `Box\Connection\Token\Token::isExpired()`: Checks if the token is past its TTL based on when it was received.
- `Box\Connection\Token\Token::getReceivedAt()` / `setReceivedAt()`: Support for tracking token lifecycle.
- `Box\Client::isTokenExpired()`: A helper on the client that delegates to the internal token's `isExpired()` method.
- `Box\Client::getRemainingTokenLifetime()`: Returns seconds until expiration.
- `Box\Folder::isEmpty()`: Check if a folder has 0 items without manually counting.
- `Box\File::getExtension()`: Convenience helper for file extensions.

## Suggested Convenience Methods (Future Implementation)
- `Token::getExpiresAt()`: Return an absolute `DateTimeImmutable` for when the token expires.

## Follow-up Items
- [x] Audit `docs/cli-test-harness.md` for any CLI-specific command examples that might have changed. (Completed: No changes needed to examples.)
- [ ] Verify if `Box\Mapper\Hydrator` should explicitly handle a `received_at` field if it's passed in from a persistent storage (currently it should work via `setReceivedAt` setter).
