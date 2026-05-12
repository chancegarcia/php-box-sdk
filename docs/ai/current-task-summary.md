### Summary
- Completed discovery and inventory for **Auth Lifecycle/Auth Provider Extraction (Step 13)**.
- Identified significant legacy coupling to curl-specific behavior in `Connection` and `Client`.
- Verified that Token Storage (Step 12) boundaries remain intact and passive.
- Produced a detailed audit and refined implementation plan in `docs/audits/13-auth-lifecycle-provider-extraction-audit.md`.

### Changes
- Created `docs/audits/13-auth-lifecycle-provider-extraction-audit.md` with detailed inventory of auth lifecycle, connection auth, and transport coupling.
- Classified `Client` responsibilities to prepare for god-object reduction.
- Proposed a 7-slice implementation plan for Step 13, including a dedicated documentation cleanup pass.
- Recommended strengthening the **v1 Release Readiness (Step 17)** modernization gate.

### Verification
- Documentation inspection of `Client.php`, `Connection.php`, and `ConnectionInterface.php`.
- Verified working tree is clean and roadmap is reconciled.
- Search-based inventory of `CURLOPT_`, `Authorization: Bearer`, and auth-lifecycle methods.

### Follow-ups
- Perform **Roadmap Step Naming and Documentation Drift Cleanup (Step 13.1)**.
- Proceed to Step 13.2: Guzzle Default Transport Cleanup.
