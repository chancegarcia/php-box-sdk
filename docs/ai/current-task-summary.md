### Summary
- Completed **Guzzle Default Transport Cleanup (Step 13.2)**.
- Guzzle is now the default and only bundled transport for v1.
- The legacy `CurlTransport` and its selection path have been removed.

### Changes
- **Connection**: Updated `Connection::getTransport()` to default to `GuzzleTransport` and removed the selection logic for `CurlTransport`.
- **Transport**: Deleted `src/Http/Transport/CurlTransport.php` and its associated test.
- **CLI**: Updated `AbstractBoxCommand` to only allow `guzzle` as a transport option.
- **Tests**: Updated `TransportOptionTest`, `ConnectionUploadCompatibilityTest`, and `ConnectionTest` to reflect Guzzle defaulting and the removal of Curl selection.
- **Docs**: Updated `docs/audits/13-auth-lifecycle-provider-extraction-audit.md` and `docs/planning/v1-release-roadmap.md` to reflect completion of Step 13.2.

### Verification
- Ran `composer review`, which includes:
    - `composer lint`: Passed.
    - `composer test`: Passed (263 tests, 689 assertions).
    - `composer cs:check`: Passed.
    - `composer analyse`: Passed (No errors).

### Follow-ups
- **Connection Interface Modernization (Step 13.3)**: Fully remove curl-specific methods from `ConnectionInterface` and modernize `Connection` implementation.
