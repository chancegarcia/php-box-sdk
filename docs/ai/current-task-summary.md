### Summary
Implemented Slice 15.4.1: FilesystemTokenStorage CLI Support. Added a JSON-file-backed token
storage class, wired it into `AbstractBoxCommand` as `--storage-type filesystem`, and extended
`ConfigProviderInterface`/`EnvConfigProvider` with `getStorageFilePath()`.

### Changes
- **src/Storage/Token/Filesystem/FilesystemTokenStorage.php** (new): Implements
  `TokenStorageInterface`. Stores tokens as a JSON map on disk keyed by
  `TokenStorageContext::getCanonicalKey()`. Private `loadMap()`/`saveMap()` helpers; throws
  `TokenStorageException` on bad JSON or write failure. No locking (single-user CLI use case).
- **src/Contract/ConfigProviderInterface.php**: Added `getStorageFilePath(): ?string`.
- **src/Service/EnvConfigProvider.php**: Implemented `getStorageFilePath()` reading
  `BOX_STORAGE_FILE_PATH`.
- **src/ClientConfig.php**: Added stub `getStorageFilePath()` returning null (cleanup in 15.4.4).
- **src/Command/AbstractBoxCommand.php**: Added `--storage-path` option; updated
  `--storage-type` description to list `pdo, filesystem`; replaced inline PDO block with
  `match` dispatch to `buildPdoStorage()` / `buildFilesystemStorage()` private helpers.
  Added `FilesystemTokenStorage` import.
- **.env.dist** / **.env**: Added `BOX_STORAGE_FILE_PATH=`.
- **tests/Storage/Filesystem/FilesystemTokenStorageTest.php** (new): 10 test methods covering
  store, retrieve, update upsert, remove, clear, multi-context isolation, bad JSON, and
  persistence across instances.
- **tests/Service/EnvConfigProviderTest.php**: Added 2 tests for `getStorageFilePath()`.
- **tests/Command/AuthStorageIntegrationTest.php**: Added 1 command integration test asserting
  `FilesystemTokenStorage` is injected when `--storage-type filesystem` is passed.

### Verification
- `composer review` passed: 292 tests, 761 assertions (up from 279/739).
- PHPStan level 0: no errors.
- PHP_CodeSniffer: no errors.
- PHP lint: no syntax errors.

### Notes
- `ClientConfig::getStorageFilePath()` is a stub returning null. It will be removed in Slice
  15.4.4 when `ClientConfig` stops implementing `ConfigProviderInterface`.
- No file locking is implemented; this is intentional for single-user CLI use.
- Workflow change: Claude Code CLI now executes code directly. Junie is no longer used.
