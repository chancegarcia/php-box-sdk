<?php

declare(strict_types=1);

namespace Box;

use Box\Exception\BoxException;
use Box\Contract\ConfigProviderInterface;

class ClientConfig implements ConfigProviderInterface
{
    protected string $clientId = '';
    protected string $clientSecret = '';
    protected ?string $redirectUri = null;
    protected ?string $authorizationCode = null;
    protected ?string $deviceId = null;
    protected ?string $deviceName = null;
    protected ?string $state = null;

    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new BoxException(sprintf('Unknown configuration option: %s', $key));
            }
        }
    }

    public static function fromArray(array $options): self
    {
        return new self($options);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId = ''): void
    {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret = ''): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(?string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function getAuthCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function setAuthorizationCode(?string $authorizationCode): void
    {
        $this->authorizationCode = $authorizationCode;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function setDeviceId(?string $deviceId): void
    {
        $this->deviceId = $deviceId;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function setDeviceName(?string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getRefreshToken(): ?string
    {
        return null;
    }

    public function getUploadFilePath(): ?string
    {
        return null;
    }

    public function getUploadFolderId(): string
    {
        return '0';
    }

    public function getAccessToken(): ?string
    {
        return null;
    }

    public function getAuthMode(): string
    {
        return 'oauth2';
    }

    public function getJwtClientId(): string
    {
        return '';
    }

    public function getJwtClientSecret(): string
    {
        return '';
    }

    public function getJwtEnterpriseId(): string
    {
        return '';
    }

    public function getJwtPublicKeyId(): string
    {
        return '';
    }

    public function getJwtPrivateKey(): string
    {
        return '';
    }

    public function getJwtPrivateKeyPassphrase(): ?string
    {
        return null;
    }

    public function getStoragePdoDsn(): ?string
    {
        return null;
    }

    public function getStoragePdoUser(): ?string
    {
        return null;
    }

    public function getStoragePdoPassword(): ?string
    {
        return null;
    }
}
