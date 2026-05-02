# Box PHP SDK

[![CI](https://github.com/chancegarcia/box.net-v2api-sdk/actions/workflows/ci.yml/badge.svg)](https://github.com/chancegarcia/box.net-v2api-sdk/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/v/stable)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)
[![PHP Version Require](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/require/php)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)
[![License](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/license)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)
[![Total Downloads](https://poser.pugx.org/chancegarcia/box-api-v2-sdk/downloads)](https://packagist.org/packages/chancegarcia/box-api-v2-sdk)

A modern PHP SDK for interacting with the [Box.com API](https://developer.box.com/).

**Version Note:** v0.11.0 is a **functional transition release** bridging the gap between legacy v0.10.x and the upcoming v1.0 architecture. It introduces v1.0-style infrastructure (Hydrators, DTOs, flattened namespaces) while maintaining reasonable backward compatibility where practical.

This library is designed as a boundary layer for Box API access, suitable for standalone use or integration into frameworks like Symfony.

## Requirements

- PHP 8.4 or higher
- `ext-curl`
- `ext-fileinfo`

## Installation

```bash
composer require chancegarcia/box-api-v2-sdk
```

## Quickstart

This section covers the essentials for getting started with the SDK.

### 1. Setup the Client
```php
use Box\Client;

$client = new Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
```

### 2. OAuth2 Workflow
To start the OAuth2 flow, generate the authorization URL:
```php
$authUrl = $client->buildAuthQuery();
// Redirect user to $authUrl
```

After the user authorizes and is redirected back to your site with a `code`, exchange it for a token:
```php
$client->setAuthorizationCode($_GET['code']);
$token = $client->exchangeAuthorizationCodeForToken(); // Recommended alias for getAccessToken()
```

### 3. File and Folder Operations
Once you have an active token, you can interact with Box resources:
```php
use Box\Http\FileStream;

$client->setToken($token);

// Upload a local file to a specific folder
$response = $client->uploadFileToBox('/path/to/local/file.txt', '12345'); 

// Upload via stream (no local file needed)
$stream = FileStream::fromString('Hello World', 'hello.txt');
$response = $client->uploadFileToBox($stream, '0'); // '0' is the root folder ID

// Get root folder
$rootFolder = $client->getFolder();
```

## v0.11.0 Transition & Compatibility
- **Typed Models & DTOs**: v0.11 introduces recursive hydration into typed objects. Some nested fields now accept both objects and legacy arrays as a transition layer.
- **Flattened Namespaces**: Primary classes are now found in shorter namespaces (e.g., `Box\Client`).
- **Non-Fluent Setters**: Setters now return `void`. Chained setter calls are no longer supported.

## Advanced Documentation

For in-depth architectural guidance, library integration patterns, and advanced usage, see the [Programmatic Usage Guide](docs/programmatic-usage.md).

## CLI Test Harness

The SDK includes a Symfony Console-based CLI tool for manual testing and exploring the API.

For detailed setup instructions, available commands, and logging options, see the [CLI Test Harness Guide](docs/cli-test-harness.md).

---

**See also:**
- [Changelog](CHANGELOG.md)
- [Upgrading from 0.10.x to 0.11.0](docs/upgrading-0.10-to-0.11.md)
- [Programmatic Usage Guide](docs/programmatic-usage.md)
- [CLI Test Harness Guide](docs/cli-test-harness.md)
- [Project Roadmap](docs/roadmap.md)

## Development and Quality Checks

The following commands are available for local development and quality assurance:

- **Run all checks (recommended before pushing):**
  ```bash
  composer review
  ```
- **Run tests:**
  ```bash
  composer test
  ```
- **Static analysis (PHPStan):**
  ```bash
  composer analyse
  ```
- **Check code style:**
  ```bash
  composer cs:check
  ```
- **Fix code style automatically:**
  ```bash
  composer cs:fix
  ```
- **Lint PHP syntax:**
  ```bash
  composer lint
  ```

## License

MIT License. See [LICENSE](LICENSE) for details.