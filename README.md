# Box PHP SDK

A modern PHP SDK for interacting with the [Box.com API](https://developer.box.com/).

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
$token = $client->getAccessToken(); // Returns a \Box\Model\Connection\Token\Token object
```

### 3. File Upload
Once you have an active token, you can upload files:
```php
$client->setToken($token);
$response = $client->uploadFileToBox('/path/to/local/file.txt', '0'); // '0' is the root folder ID
```

## Advanced Documentation

For in-depth architectural guidance, library integration patterns, and advanced usage, see the [Programmatic Usage Guide](docs/programmatic-usage.md).

## CLI Test Harness

The SDK includes a Symfony Console-based CLI tool for manual testing and exploring the API.

For detailed setup instructions, available commands, and logging options, see the [CLI Test Harness Guide](docs/cli-test-harness.md).

---

**See also:**
- [Programmatic Usage Guide](docs/programmatic-usage.md)
- [CLI Test Harness Guide](docs/cli-test-harness.md)
- [Project Roadmap](docs/roadmap.md)

## Development and Testing

Run the test suite:
```bash
./vendor/bin/phpunit
```


## License

MIT License. See [LICENSE](LICENSE) for details.