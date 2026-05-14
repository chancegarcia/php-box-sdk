<?php

declare(strict_types=1);

namespace Box;

use Box\Exception\BoxException;
use Box\Contract\ConfigProviderInterface;

class ClientConfig implements ConfigProviderInterface
{
    protected string $oAuth2ClientId = '';
    protected string $oAuth2ClientSecret = '';
    protected ?string $oAuth2RedirectUri = null;
    protected ?string $oAuth2AuthCode = null;
    protected ?string $deviceId = null;
    protected ?string $deviceName = null;
    protected ?string $oAuth2State = null;

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

    public function getOAuth2ClientId(): string
    {
        return $this->oAuth2ClientId;
    }

    public function setOAuth2ClientId(string $oAuth2ClientId = ''): void
    {
        $this->oAuth2ClientId = $oAuth2ClientId;
    }

    public function getOAuth2ClientSecret(): string
    {
        return $this->oAuth2ClientSecret;
    }

    public function setOAuth2ClientSecret(string $oAuth2Secret = ''): void
    {
        $this->oAuth2ClientSecret = $oAuth2Secret;
    }

    public function getOAuth2RedirectUri(): ?string
    {
        return $this->oAuth2RedirectUri;
    }

    public function setOAuth2RedirectUri(?string $oAuth2RedirectUri): void
    {
        $this->oAuth2RedirectUri = $oAuth2RedirectUri;
    }

    public function getOAuth2AuthCode(): ?string
    {
        return $this->oAuth2AuthCode;
    }

    public function setoAuth2AuthCode(?string $oAuth2AuthCode): void
    {
        $this->oAuth2AuthCode = $oAuth2AuthCode;
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

    public function getOAuth2State(): ?string
    {
        return $this->oAuth2State;
    }

    public function setOAuth2State(?string $state): void
    {
        $this->oAuth2State = $state;
    }

    public function getOAuth2RefreshToken(): ?string
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

    public function getOAuth2AccessToken(): ?string
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

    public function getStorageFilePath(): ?string
    {
        return null;
    }
}
