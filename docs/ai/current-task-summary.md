### Summary
- Decoupled the service layer from token storage orchestration.
- Services are now storage-independent, deferring persistence to the future orchestration layer (Client/Auth Provider).
- Cleaned up the `Service` and `ServiceInterface` by removing storage-related properties and methods.

### Changes
- **src/Service/ServiceInterface.php**: Removed `getTokenStorage`, `setTokenStorage`, `getTokenStorageContext`, and `setTokenStorageContext` methods and their imports.
- **src/Service/Service.php**:
    - Removed `tokenStorage` and `tokenStorageContext` properties.
    - Removed storage-related getters and setters.
    - Updated `refreshConnection()` to remove token storage update logic.
    - Updated `destroyToken()` to remove token storage removal logic.
    - Cleaned up `@throws` annotations for `TokenStorageException`.
- **tests/Service/ServiceAuthTest.php**: Removed storage-related mocks and setup.
- **tests/Service/ServiceErrorTest.php**: Removed storage-related mocks and assertions in 401 retry flow tests.

### Verification
- Ran `composer test tests/Service`: Passed (56 tests, 124 assertions).
- Ran `composer test tests/Storage/TokenStorageContractTest.php`: Passed (9 tests, 27 assertions).
- Ran `composer test tests/Storage/Pdo/TokenStorageTest.php`: Passed (9 tests, 20 assertions).
- Ran `composer lint`: All files passed.
- Ran `composer cs:check`: Passed.
- Ran `composer analyse`: Passed (163/163 files).

### Notes
- `TokenStorageException` is retained in the codebase as it is used by the `TokenStorage` implementations, but it is no longer thrown by the `Service` layer itself.
- Token refresh mechanics remain in the `Service` layer for now to preserve functionality, but they no longer coordinate persistent storage.

### Follow-ups
- 12.5 Client integration hooks for token storage.
- 12.6 CLI/auth harness storage integration.
- Post-Step-12: Dedicated Auth Lifecycle/Auth Provider extraction.
