# Box PHP SDK

PHP SDK to interact with the [Box.com API](https://developer.box.com/).

## Features

- OAuth2 Authentication (Authorization Code Flow)
- Token Management (Exchange and Refresh)
- Folder Operations (Create, List, Copy, etc.)
- File Operations (Upload, Retrieve, etc.)
- Modern PHP 8.4+ support (Forward-compatible with PHP 8.5)
- Symfony Console CLI harness for manual testing

## Requirements

- PHP 8.4 or higher
- `ext-curl`
- `ext-fileinfo`

## Installation

```bash
composer require chancegarcia/box-api-v2-sdk
```

## CLI Test Harness

The SDK includes a Symfony Console-based CLI tool for manual end-to-end testing of Box API flows.

### Setup

1. Copy `.env.dist` to `.env`:
   ```bash
   cp .env.dist .env
   ```
2. Fill in your Box application credentials in `.env`:
   - `BOX_CLIENT_ID`
   - `BOX_CLIENT_SECRET`
   - `BOX_REDIRECT_URI` (Optional)
   - `BOX_ACCESS_TOKEN` (Required for file upload)

### Available Commands

Run `bin/console list box` to see all available commands.

#### 1. Generate Authorization URL
Builds the URL to visit in your browser to start the OAuth2 flow.
```bash
bin/console box:auth:url
```

#### 2. Exchange Authorization Code
Exchange the code obtained after browser authorization for an access token.
```bash
bin/console box:auth:exchange-code <AUTH_CODE> --secrets-file=token.json
```

#### 3. Refresh Access Token
Refresh an expired access token using a refresh token.
```bash
bin/console box:auth:refresh-token --refresh-token=<REFRESH_TOKEN> --secrets-file=token.json
```

#### 4. Upload a File
Upload a local file to a Box folder. Requires `BOX_ACCESS_TOKEN` to be set in `.env`.
```bash
bin/console box:file:upload /path/to/local/file.txt --folder-id=0
```

### Secret Handling
- All sensitive tokens and secrets are **masked** in the console output by default.
- Use `--secrets-file=PATH` to export unmasked tokens to a JSON file.
- The command will ask for confirmation before writing secrets to a file unless `--force` is used.
- Use `--json` for machine-readable (but still masked) console output.

### Logging
The SDK uses Monolog for logging. By default, logs are written to the `var/log` directory:
- `var/log/box-sdk.log`: Contains `debug` and `info` messages.
- `var/log/box-sdk-warning.log`: Contains `warning` messages.
- `var/log/box-sdk-error.log`: Contains `error` messages.

Logs are rotated daily, and up to 5 old files are kept. Each log file has a maximum size of 100MB.

#### Logging Options
Every command supports the following logging overrides:
- `--log-config <path>`: Use a custom Monolog configuration file (overrides all default behavior).
- `--log-dir <dir>`: Change the directory where logs are stored.
- `--log-file <name>`: Change the base log file name. If used, **all** log levels will be written to this single file in the active log directory.

#### Custom Configuration
The default configuration is located in `config/monolog.php`. For fully custom logging behavior, create a new PHP file that returns a configuration array and pass it via `--log-config`.

### Custom JSON Formatting
You can override the default JSON output formatter by setting the `BOX_JSON_FORMATTER` environment variable in your `.env` file to a fully qualified class name.
```dotenv
BOX_JSON_FORMATTER="App\Service\CustomJsonFormatter"
```
The class must implement `Box\Contract\JsonFormatterInterface`.

## Programmatic Usage

### Client Setup
```php
use Box\Client;

$client = new Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
```

### Uploading a File
```php
$client->setToken($token); // \Box\Model\Connection\Token\Token object
$response = $client->uploadFileToBox('/path/to/file.txt');
```

## Development and Testing

### Running Tests
```bash
./vendor/bin/phpunit
```

### Bundle Compatibility
The CLI harness is designed with a service-oriented architecture (interfaces, constructor injection). The services in `Box\Service` can be easily registered in a Symfony Bundle container.

## License
MIT License. See [LICENSE](LICENSE) for details.