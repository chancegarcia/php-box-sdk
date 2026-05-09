# CLI Test Harness Guide

This guide provides detailed information on using the Symfony Console-based CLI tool included with the SDK. This tool is designed for manual testing, exploring the Box API, and verifying your configuration.

## 1. Purpose and Role

The CLI harness is a practical SDK verification tool designed for manual testing, exploring the Box API, and verifying your configuration. It serves as a reference implementation of the SDK's services.

### Core Role
- **Verification Harness**: Practical manual verification for SDK behavior without requiring a downstream application.
- **Sanity Confirmation**: Quickly confirm auth flows, transport behavior, and service calls during development.
- **Reference Implementation**: Demonstrates how to use SDK services in a real PHP application.

### Design Boundary
- **Lightweight**: Commands are kept thin.
- **Logic Separation**: Reusable behavior belongs in SDK services/classes, not command bodies. The CLI exercises SDK behavior rather than owning SDK logic.
- **Core-Only**: The CLI is a test harness for the core SDK. It does not provide framework-specific integrations (like Symfony bundle features or Doctrine ORM support), which are deferred to future integration packages.

### Scope and Retention
- The CLI is retained in the core SDK repository as a primary verification tool.
- All current CLI commands are retained unless explicitly replaced or proven obsolete.
- It is NOT intended to become a full-featured "Box CLI" product or a framework integration.

Developers can use the CLI to:
- Interactively explore API endpoints.
- Test OAuth2 and JWT/S2S (v1.0+) flows without writing application code.
- Verify environment configuration and network connectivity.
- Inspect raw and redacted API responses.

## 2. Setup

### Environment Configuration
1. Copy the template:
   ```bash
   cp .env.dist .env
   ```
2. Configure your Box credentials in `.env`:
   - `BOX_CLIENT_ID`: Your application's client ID.
   - `BOX_CLIENT_SECRET`: Your application's client secret.
   - `BOX_REDIRECT_URI`: (Optional) Must match your Box App settings.
   - `BOX_ACCESS_TOKEN`: (Required for file operations) Can be manually set or obtained via exchange commands.

### Discovery
Run the following to list all available Box-specific commands:
```bash
composer box:list
```
Or via the binary directly:
```bash
bin/box-sdk list box
```

## 3. Available Commands

### OAuth2 Workflow

#### Generate Authorization URL
Builds the URL required to start the OAuth2 flow in a browser.
```bash
bin/box-sdk box:auth:url
```

#### Exchange Authorization Code
Exchanges the `code` obtained from the redirect for a full token payload.
```bash
bin/box-sdk box:auth:exchange-code <AUTH_CODE> --secrets-file=token.json
```

#### Refresh Access Token
Refreshes an expired access token using a stored refresh token.
```bash
bin/box-sdk box:auth:refresh-token --refresh-token=<REFRESH_TOKEN> --secrets-file=token.json
```

### File Operations

#### Upload a File
Uploads a local file to a specific Box folder.
```bash
bin/box-sdk box:file:upload /path/to/local/file.txt --folder-id=0
```

## 4. Secret Handling and Output

The CLI is designed to be safe for screen sharing and logging:
- **Masking:** Sensitive values (tokens, secrets) are masked in the console by default.
- **Secrets File:** Use the `--secrets-file=PATH` option to save unmasked tokens to a JSON file.
- **JSON Output:** Use `--json` for machine-readable output.

## 5. Logging and Observability

The CLI uses the SDK's internal logging services, configured via Monolog.

### Default Log Locations
- `var/log/box-sdk.log`: Debug and info level events.
- `var/log/box-sdk-warning.log`: Warnings.
- `var/log/box-sdk-error.log`: Errors and exceptions.

### Runtime Overrides
Every command supports the following flags for logging and transport:
- `--log-dir <dir>`: Change the target log directory.
- `--log-file <name>`: Consolidate all levels into a single file.
- `--log-config <path>`: Provide a custom PHP-based Monolog configuration.
- `--transport <type>`: Choose the HTTP transport (`curl` or `guzzle`). `curl` is the default.

Example using a specific transport:
```bash
bin/box-sdk box:file:upload /path/to/local/file.txt --transport=guzzle
```

## 6. Customization

### JSON Formatter
You can inject a custom JSON formatter by setting `BOX_JSON_FORMATTER` in your `.env` to a class implementing `Box\Contract\JsonFormatterInterface`.

---

**See also:**
- [README.md](../README.md)
- [Programmatic Usage Guide](programmatic-usage.md)
- [Project Roadmap](../planning/roadmap.md)