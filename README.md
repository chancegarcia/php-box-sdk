# Box PHP SDK

[![CI](https://github.com/chancegarcia/box.net-v2api-sdk/actions/workflows/ci.yml/badge.svg)](https://github.com/chancegarcia/box.net-v2api-sdk/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/v/stable)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)
[![PHP Version Require](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/require/php)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)
[![License](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/license)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)
[![Total Downloads](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/downloads)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)

A modern PHP SDK for interacting with the [Box.com API](https://developer.box.com/).

**Status:** v1.0.0 released.

This library is designed as a boundary layer for Box API access, suitable for standalone use or integration into frameworks like Symfony.

## Requirements

- PHP 8.4 or higher
- `ext-fileinfo`

## Installation

```bash
composer require chancegarcia/box-api-v2-sdk
```

## Quickstart

### OAuth2 Workflow

The recommended setup uses `BoxClientFactory` with an `EnvConfigProvider`, which reads credentials from environment variables (`BOX_OAUTH_CLIENT_ID`, `BOX_OAUTH_CLIENT_SECRET`, etc.):

```php
use Box\Service\BoxClientFactory;
use Box\Service\EnvConfigProvider;

$factory = new BoxClientFactory(new EnvConfigProvider());
$client  = $factory->createClient();
```

Generate the authorization URL and redirect the user:

```php
$authUrl = $client->buildAuthorizationUrl();
// Redirect user to $authUrl
```

After the user authorizes and is redirected back with a `code`, exchange it for a token:

```php
$client->setAuthorizationCode($_GET['code']);
$token = $client->exchangeAuthorizationCodeForToken();
```

### JWT / Server-to-Server

For server-to-server integrations (no browser redirect needed), set `BOX_AUTH_MODE=jwt` and the `BOX_JWT_*` environment variables, then:

```php
use Box\Service\BoxClientFactory;
use Box\Service\EnvConfigProvider;

$configProvider = new EnvConfigProvider();
$factory        = new BoxClientFactory($configProvider);
$client         = $factory->createClientForCurrentMode(); // JWT client when BOX_AUTH_MODE=jwt
```

See the [Programmatic Usage Guide](docs/user/programmatic-usage.md) for enterprise and app-user token exchange examples.

### File and Folder Operations

```php
use Box\Http\FileStream;

$client->setToken($token);

// Upload a local file
$result = $client->uploadFileToBox('/path/to/file.txt', '12345');

// Upload via stream (no local file needed)
$stream = FileStream::fromString('Hello World', 'hello.txt');
$result = $client->uploadFileToBox($stream, '0'); // '0' is the root folder ID

// Get a folder
$folder = $client->getFolder('12345');
```

## Advanced Documentation

For in-depth architectural guidance, library integration patterns, and advanced usage:

- [Programmatic Usage Guide](docs/user/programmatic-usage.md) — OAuth2, JWT/S2S, token storage, error handling, file streaming
- [CLI Test Harness Guide](docs/user/cli-test-harness.md) — `bin/box-sdk` commands for manual verification
- [v1.0 Migration Guide](docs/migration/upgrading-0.11-to-1.0.md) — upgrading from v0.11

## Development and Quality Checks

```bash
composer review    # Run all checks (recommended before pushing)
composer test      # PHPUnit
composer analyse   # PHPStan
composer cs:check  # PHP_CodeSniffer
composer cs:fix    # Fix code style automatically
composer lint      # PHP syntax check
```

---

**See also:**
- [Changelog](CHANGELOG.md)
- [Documentation Index](docs/README.md)

## License

MIT License. See [LICENSE](LICENSE) for details.
