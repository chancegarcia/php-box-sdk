<?php

namespace Box\Service;

use Box\Contract\ConfigProviderInterface;
use RuntimeException;

class EnvConfigProvider implements ConfigProviderInterface
{
    public function getClientId(): string
    {
        return $this->getRequiredEnv('BOX_CLIENT_ID');
    }

    public function getClientSecret(): string
    {
        return $this->getRequiredEnv('BOX_CLIENT_SECRET');
    }

    public function getRedirectUri(): ?string
    {
        return $_ENV['BOX_REDIRECT_URI'] ?? $_SERVER['BOX_REDIRECT_URI'] ?? null;
    }

    public function getState(): ?string
    {
        return $_ENV['BOX_STATE'] ?? $_SERVER['BOX_STATE'] ?? null;
    }

    public function getAuthCode(): ?string
    {
        return $_ENV['BOX_AUTH_CODE'] ?? $_SERVER['BOX_AUTH_CODE'] ?? null;
    }

    public function getRefreshToken(): ?string
    {
        return $_ENV['BOX_REFRESH_TOKEN'] ?? $_SERVER['BOX_REFRESH_TOKEN'] ?? null;
    }

    public function getUploadFilePath(): ?string
    {
        return $_ENV['BOX_UPLOAD_FILE_PATH'] ?? $_SERVER['BOX_UPLOAD_FILE_PATH'] ?? null;
    }

    public function getUploadFolderId(): string
    {
        return $_ENV['BOX_UPLOAD_FOLDER_ID'] ?? $_SERVER['BOX_UPLOAD_FOLDER_ID'] ?? '0';
    }

    public function getAccessToken(): ?string
    {
        return $_ENV['BOX_ACCESS_TOKEN'] ?? $_SERVER['BOX_ACCESS_TOKEN'] ?? null;
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
