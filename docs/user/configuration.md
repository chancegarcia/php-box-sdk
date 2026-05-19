# Environment Variable Reference

All SDK environment variables are loaded from `$_ENV` with a `$_SERVER` fallback. Copy `.env.dist` to `.env` and populate values for your integration type.

## Auth Mode

| Variable | Required | Default | Description |
|---|---|---|---|
| `BOX_AUTH_MODE` | No | `oauth2` | Auth strategy: `oauth2` or `jwt` |

## OAuth2 Credentials

Used when `BOX_AUTH_MODE=oauth2` (the default).

| Variable | Required | Default | Description |
|---|---|---|---|
| `BOX_OAUTH_CLIENT_ID` | Yes | — | OAuth2 application client ID |
| `BOX_OAUTH_CLIENT_SECRET` | Yes | — | OAuth2 application client secret |
| `BOX_OAUTH_REDIRECT_URI` | No | — | Must match the redirect URI registered in your Box App settings |
| `BOX_OAUTH_STATE` | No | — | CSRF state parameter for the authorization URL |
| `BOX_OAUTH_AUTH_CODE` | No | — | Authorization code; CLI shortcut instead of passing it as an argument |
| `BOX_OAUTH_REFRESH_TOKEN` | No | — | Refresh token; CLI shortcut for `box:auth:refresh-token` |
| `BOX_OAUTH_ACCESS_TOKEN` | No | — | Access token; CLI shortcut for commands that require an active token |

## JWT / Server-to-Server Credentials

Used when `BOX_AUTH_MODE=jwt`. All variables in this group are required for JWT mode.

| Variable | Required | Default | Description |
|---|---|---|---|
| `BOX_JWT_CLIENT_ID` | Yes (JWT) | — | JWT application client ID |
| `BOX_JWT_CLIENT_SECRET` | Yes (JWT) | — | JWT application client secret |
| `BOX_JWT_ENTERPRISE_ID` | Yes (JWT) | — | Box enterprise ID |
| `BOX_JWT_PUBLIC_KEY_ID` | Yes (JWT) | — | Key ID of the registered RSA public key |
| `BOX_JWT_PRIVATE_KEY_PATH` | Yes (JWT) | — | Absolute path to the PEM private key file; the SDK reads the file contents into memory — the path is never stored |
| `BOX_JWT_PRIVATE_KEY_PASSPHRASE` | No | — | Passphrase for an encrypted private key |

## Token Storage

| Variable | Required | Default | Description |
|---|---|---|---|
| `BOX_STORAGE_FILE_PATH` | No | `var/tmp/box-sdk/tokens.json` | Path to the filesystem token store |
| `BOX_STORAGE_PDO_DSN` | No | — | PDO DSN for the database token store (e.g. `mysql:host=localhost;dbname=myapp`) |
| `BOX_STORAGE_PDO_USER` | No | — | PDO username |
| `BOX_STORAGE_PDO_PASS` | No | — | PDO password |

## File Operations

These variables provide default argument values for `box:file:upload` and are otherwise unused by the SDK library.

| Variable | Required | Default | Description |
|---|---|---|---|
| `BOX_UPLOAD_FILE_PATH` | No | — | Default local file path for the upload command |
| `BOX_UPLOAD_FOLDER_ID` | No | `0` | Default Box folder ID for the upload command; `0` is the root folder |

## Miscellaneous

| Variable | Required | Default | Description |
|---|---|---|---|
| `BOX_SUBDOMAIN` | No | — | Custom Box account subdomain (e.g. `acme` for `acme.app.box.com`); used when generating Box web URLs |
| `BOX_JSON_FORMATTER` | No | — | FQCN of a class implementing `Box\Contract\JsonFormatterInterface`; used by the CLI to format JSON output |

---

**See also:**
- [Programmatic Usage Guide](programmatic-usage.md)
- [CLI Test Harness Guide](cli-test-harness.md)
