### Summary
- Completed **Connection Interface Modernization (Step 13.3)**.
- Fully removed curl-specific public SDK surface from `ConnectionInterface` and implementation details from `Connection`.
- Updated the AI workflow documentation to require Final Documentation Status Reconciliation for all future implementation slices.

### Changes
- **ConnectionInterface**: Removed 7 curl-specific methods and type hints (`CurlHandle`, `CURLFile`).
- **Connection**: Flattened the class by removing all `curl_*` implementation logic, constants, and the `setCurlOpts`/`getCurlOpts` methods.
- **Connection::postFile**: Refactored to use transport-neutral Guzzle-compatible multipart options.
- **SDK Surface**: Removed `CurlHandle` and `CURLFile` usages from the public API.
- **Client/Service/Factory**: Updated to use `addHeader()` instead of `setCurlOpts()` for authentication and custom headers.
- **Workflow Docs**: Updated `docs/prompts/ai-workflow/single-repository-workflow.md` with a new standard requirement for documentation status reconciliation.

### Verification
- Ran `composer review`, which includes:
    - `composer lint`: Passed.
    - `composer test`: Passed (259 tests, 688 assertions).
    - `composer cs:check`: Passed.
    - `composer analyse`: Passed (No errors).
- Confirmed that `ClientTest`, `ConnectionTest`, and all other tests are green.

### Token Storage Boundary Verification
- Token storage remains passive persistence only.
- Services remain storage-independent.
- Client ownership of token storage hooks remains intact.

### Follow-ups
- **Authenticated Request Boundary Cleanup (Step 13.4)**: Centralize bearer token application in `Connection` and remove manual header pushing from `Client` and `Service`.
