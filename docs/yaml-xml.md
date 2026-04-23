# YAML and XML Configuration Support

This document explains why YAML and XML support for logging configuration is not yet implemented and how it should be implemented in the future.

## Current Status
Currently, the logging configuration is defined in a PHP file (`config/monolog.php`). This provides a simple and efficient way to manage configuration without adding extra dependencies for parsing other formats.

## Why not now?
Implementing YAML and XML parsing now would require:
1. Adding dependencies like `symfony/yaml` or a dedicated XML parser.
2. Building a mapping layer to convert these formats into the normalized PHP structure.
3. Handling format-specific edge cases (e.g., type coercion in XML).

Given the current scope, the PHP-based declarative configuration is sufficient and provides the best performance and clarity.

## Future Implementation Path

To add YAML or XML support, a new "Loader" layer should be introduced:

1. **Loader Interface**: Define a `ConfigLoaderInterface` with a `load(string $path): array` method.
2. **Format-specific Loaders**:
   - `YamlConfigLoader`: Uses a YAML parser to read a `.yaml` or `.yml` file.
   - `XmlConfigLoader`: Uses a standard XML parser to read a `.xml` file.
3. **Normalization**: Regardless of the input format, the resulting array must be passed through the `Box\Logger\ConfigNormalizer` to ensure it matches the internal schema.

### Schema Preservation
Future loaders must maintain the same semantics as the current PHP config:
- Support for `handlers` with `type`, `path`, `level`, `max_files`, `max_file_size`, and `levels`.
- Top-level `log_dir` and `log_file_basename` for convenience overrides.
- Type coercion: Ensure that numeric values (like `max_files`) are correctly cast to integers, especially when reading from XML.

### Integration
Once loaders are implemented, the `AbstractBoxCommand::initialize` method can be updated to detect the file extension of the `--log-config` path and use the appropriate loader.

```php
// Future pseudo-code in AbstractBoxCommand
$loader = $this->loaderFactory->getLoader($logConfigPath);
$config = $loader->load($logConfigPath);
```

---

**See also:**
- [Project Roadmap](roadmap.md)
