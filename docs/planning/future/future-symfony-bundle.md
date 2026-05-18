# Future Symfony Bundle and Doctrine ORM Integration Planning

This document outlines the planning for a future Symfony bundle and Doctrine ORM integration for the Box SDK. These features are deferred to post-v1.0.0 and are not part of the core SDK scope.

**Note on CLI Tools**: The core SDK retains a lightweight CLI test harness for practical SDK verification (see `docs/user/cli-test-harness.md`). The future Symfony bundle may provide additional framework-aware console commands, but the essential verification harness remains in the core SDK repository to ensure it is always available for standalone SDK testing.

## Overview

The core SDK provides a framework-neutral token storage contract. A dedicated Symfony bundle will provide seamless integration with the Symfony framework and Doctrine ORM, allowing for automated configuration, service wiring, and persistent token storage using database entities.

## 1. Symfony Bundle Capabilities

### Service Wiring and Configuration
- **Automatic Service Registration**: Register `Client`, `AuthProvider`, and `Transport` as Symfony services.
- **Semantic Configuration**: Use `Configuration` and `Definition` to allow setting credentials, base URIs, and logging via `config/packages/box.yaml`.
- **Environment Variable Support**: Support for `BOX_CLIENT_ID`, `BOX_CLIENT_SECRET`, etc.
- **Autowiring**: Allow `Box\Client` or specific services like `Box\Service\UserService` to be autowired into application controllers and services.

### Framework Integration
- **Logging**: Integration with Monolog channels (e.g., `box` channel).
- **Secrets Management**: Integration with Symfony Secrets for sensitive credentials.
- **Cache**: Integration with Symfony Cache for short-lived token storage if needed.
- **Console Commands**: Custom commands for authentication testing or token management.
- **Profiler Integration**: Data collector and profiler panel to inspect Box API calls, latency, and token status.

## 2. Doctrine ORM Token Storage

### Persistence and Advanced Storage
- **Doctrine Token Entity**: A default `BoxToken` entity for persisting tokens.
- **Mapped Superclass**: Provide a `BaseBoxToken` mapped superclass to allow users to extend and customize the token entity (e.g., adding a relation to their `User` entity).
- **Storage Implementation**: A `DoctrineTokenStorage` that implements the core `TokenStorageInterface`.
- **Advanced Multi-Token Support**: Support features deferred from core v1.0, including:
    - Token history and audit logs.
    - Multiple active grants per same user/context.
    - Token profile labels and active/default token selection.
    - Enterprise/User/Tenant mapping.
    - Integration with Symfony Security user identifiers.
- **Enhanced Security**:
    - Symfony Secrets integration for credentials.
    - External secret manager integration (HashiCorp Vault, AWS Secrets Manager).
    - Encrypted Doctrine fields or app-specific encryption services.
- **Schema Tooling**: Doctrine migrations or schema commands for easy setup.
- **Cleanup**: App-specific token cleanup commands.

### Features
- **Repository Integration**: Use Doctrine repositories for token retrieval.
- **Transaction Safety**: Ensure token updates (especially during refresh-token rotation) are performed within database transactions.
- **Multi-tenancy**: Support mapping tokens to specific application users or tenants using the `TokenStorageContext`.
- **Encryption**: Optional integration with Doctrine extensions for encryption at rest for token values.

## 3. Boundary and Dependencies

- **Dependency Direction**: The Symfony bundle depends on the core SDK. The core SDK MUST NOT depend on Symfony or Doctrine.
- **Namespace**: `Box\Bridge\Symfony` or a separate repository `chancegarcia/box-api-v2-symfony-bundle`.
- **Interoperability**: The bundle must implement core SDK interfaces (`TokenStorageInterface`, `AuthProviderInterface`) to ensure compatibility.

## 4. Future Configuration Format Support

YAML/XML configuration support is deferred from the core SDK.

A future Symfony bundle or framework integration package may provide YAML, XML, or PHP configuration using Symfony’s configuration conventions.

The core SDK should remain framework-neutral and expose PHP-native configuration objects, factories, and dependency injection entry points rather than parsing YAML/XML directly.

### Configuration Concepts to Preserve

When implementing YAML/XML support in a framework integration layer:
- **Loader Layer**: Introduce a `ConfigLoaderInterface` with format-specific implementations (e.g., `YamlConfigLoader`, `XmlConfigLoader`).
- **Schema Validation**: Regardless of input format, configurations must be normalized (e.g., via a `ConfigNormalizer`) to ensure they match internal SDK schemas.
- **CLI Integration**: Framework-aware CLI commands can use these loaders to detect configuration file extensions and load them accordingly.
- **Type Coercion**: Ensure that numeric and boolean values are correctly cast from strings, especially when reading from XML.
- **Feature Coverage**: Configuration should cover:
    - Log handlers (type, path, level, rotation settings).
    - Top-level overrides (log directory, file names).
    - Service configuration and transport settings.

## 5. Roadmap and Status

- **Status**: **Deferred**
- **Priority**: Low for v1.0.0 core; High for post-v1.0.0 ecosystem growth.
- **Target Version**: v1.x or separate package release.

## 6. Open Questions for Bundle

- Should the bundle live in the same mono-repo or a separate repository?
- Should we provide a default UI/Controller for the OAuth2 callback?
- How should we handle multi-account support in configuration?
