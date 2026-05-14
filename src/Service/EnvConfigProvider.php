<?php

namespace Box\Service;

use Box\Contract\ConfigProviderInterface;
use Box\Exception\BoxException;
use RuntimeException;

class EnvConfigProvider implements ConfigProviderInterface
{
    public function getOAuth2ClientId(): string
    {
        return $this->getRequiredEnv('BOX_OAUTH_CLIENT_ID');
    }

    public function getOAuth2ClientSecret(): string
    {
        return $this->getRequiredEnv('BOX_OAUTH_CLIENT_SECRET');
    }

    public function getOAuth2RedirectUri(): ?string
    {
        return $_ENV['BOX_OAUTH_REDIRECT_URI'] ?? $_SERVER['BOX_OAUTH_REDIRECT_URI'] ?? null;
    }

    public function getOAuth2State(): ?string
    {
        return $_ENV['BOX_OAUTH_STATE'] ?? $_SERVER['BOX_OAUTH_STATE'] ?? null;
    }

    public function getOAuth2AuthCode(): ?string
    {
        return $_ENV['BOX_OAUTH_AUTH_CODE'] ?? $_SERVER['BOX_OAUTH_AUTH_CODE'] ?? null;
    }

    public function getOAuth2RefreshToken(): ?string
    {
        return $_ENV['BOX_OAUTH_REFRESH_TOKEN'] ?? $_SERVER['BOX_OAUTH_REFRESH_TOKEN'] ?? null;
    }

    public function getUploadFilePath(): ?string
    {
        return $_ENV['BOX_UPLOAD_FILE_PATH'] ?? $_SERVER['BOX_UPLOAD_FILE_PATH'] ?? null;
    }

    public function getUploadFolderId(): string
    {
        return $_ENV['BOX_UPLOAD_FOLDER_ID'] ?? $_SERVER['BOX_UPLOAD_FOLDER_ID'] ?? '0';
    }

    public function getOAuth2AccessToken(): ?string
    {
        return $_ENV['BOX_OAUTH_ACCESS_TOKEN'] ?? $_SERVER['BOX_OAUTH_ACCESS_TOKEN'] ?? null;
    }

    public function getStoragePdoDsn(): ?string
    {
        return $_ENV['BOX_STORAGE_PDO_DSN'] ?? $_SERVER['BOX_STORAGE_PDO_DSN'] ?? null;
    }

    public function getStoragePdoUser(): ?string
    {
        return $_ENV['BOX_STORAGE_PDO_USER'] ?? $_SERVER['BOX_STORAGE_PDO_USER'] ?? null;
    }

    public function getStoragePdoPassword(): ?string
    {
        return $_ENV['BOX_STORAGE_PDO_PASS'] ?? $_SERVER['BOX_STORAGE_PDO_PASS'] ?? null;
    }

    public function getStorageFilePath(): ?string
    {
        return $_ENV['BOX_STORAGE_FILE_PATH'] ?? $_SERVER['BOX_STORAGE_FILE_PATH'] ?? null;
    }

    public function getJsonFormatterClass(): ?string
    {
        return $_ENV['BOX_JSON_FORMATTER'] ?? $_SERVER['BOX_JSON_FORMATTER'] ?? null;
    }

    public function getAuthMode(): string
    {
        $mode = $_ENV['BOX_AUTH_MODE'] ?? $_SERVER['BOX_AUTH_MODE'] ?? 'oauth2';
        return '' !== $mode ? $mode : 'oauth2';
    }

    public function getJwtClientId(): string
    {
        return $this->getRequiredJwtEnv('BOX_JWT_CLIENT_ID');
    }

    public function getJwtClientSecret(): string
    {
        return $this->getRequiredJwtEnv('BOX_JWT_CLIENT_SECRET');
    }

    public function getJwtEnterpriseId(): string
    {
        return $this->getRequiredJwtEnv('BOX_JWT_ENTERPRISE_ID');
    }

    public function getJwtPublicKeyId(): string
    {
        return $this->getRequiredJwtEnv('BOX_JWT_PUBLIC_KEY_ID');
    }

    public function getJwtPrivateKey(): string
    {
        $path = $this->getRequiredJwtEnv('BOX_JWT_PRIVATE_KEY_PATH');

        if (!file_exists($path) || !is_readable($path)) {
            throw new BoxException(sprintf('JWT private key file "%s" does not exist or is not readable', $path));
        }

        $content = file_get_contents($path);
        if (false === $content) {
            throw new BoxException(sprintf('Failed to read JWT private key file "%s"', $path));
        }

        return $content;
    }

    public function getJwtPrivateKeyPassphrase(): ?string
    {
        $passphrase = $_ENV['BOX_JWT_PRIVATE_KEY_PASSPHRASE'] ?? $_SERVER['BOX_JWT_PRIVATE_KEY_PASSPHRASE'] ?? null;
        return (null === $passphrase || '' === $passphrase) ? null : (string) $passphrase;
    }

    private function getRequiredJwtEnv(string $key): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        if (null === $value || '' === $value) {
            throw new BoxException(sprintf('Environment variable "%s" is required for JWT auth mode', $key));
        }

        return (string) $value;
    }

    private function getRequiredEnv(string $key): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        if (null === $value || '' === $value) {
            throw new RuntimeException(sprintf('Environment variable "%s" is required', $key));
        }

        return (string) $value;
    }
}
