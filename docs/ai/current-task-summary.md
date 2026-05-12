### Summary
- Finalized the v1 passive token storage contract and introduced the `TokenStorageContext` DTO.
- Renamed `BaseTokenStorageInterface` to `TokenStorageInterface` and updated all internal references.
- Refactored `TokenStorageContainer`, `TokenStorageException`, and `Service` to align with the new context-aware contract.

### Changes
- Created `Box\Dto\TokenStorageContext` for formal identification of tokens in storage.
- Renamed `Box\Storage\Token\BaseTokenStorageInterface` to `Box\Storage\Token\TokenStorageInterface`.
- Updated `TokenStorageInterface` with strict types and new method signatures using `TokenStorageContext`.
- Refactored `Box\Storage\Token\Container\TokenStorageContainer` to support multiple tokens via context.
- Refactored `Box\Exception\TokenStorageException` to include context and remove obsolete/unredacted fields.
- Updated `Box\Service\Service` and `Box\Service\ServiceInterface` to correctly orchestrate with the new storage contract.
- Applied minimal compatibility fixes to `Box\Storage\Token\Pdo\TokenStorage` to maintain compilation.
- Cleaned up `phpstan-baseline.neon` by removing obsolete ignored errors.

### Verification
- Added `tests/Storage/TokenStorageContractTest.php` to verify context and container behavior.
- Ran `composer test tests/Storage/TokenStorageContractTest.php` (All 4 tests passed, 12 assertions).
- Ran `composer lint` (All files passed).
- Ran `composer cs:check` and `composer cs:fix` (Style verified).
- Ran `composer analyse` (All checks passed, baseline cleaned).

### Notes
- **In-memory storage**: Full context support implemented in `TokenStorageContainer`.
- **PDO storage**: Minimal interface alignment performed; functional rewrite remains deferred to Slice 12.3.
- **Service cleanup**: `Box\Service\Service` still manages some storage orchestration; broad removal of storage dependencies from services is deferred to Slice 12.4.
- **Follow-ups**: Address remaining service type-safety and storage-independence gaps in subsequent Step 12 slices.
