<?php

declare(strict_types=1);

namespace Box;

use Box\Exception\BoxException;

class ClientConfig
{
    public function __construct(
        private string $oAuth2ClientId = '',
        private string $oAuth2ClientSecret = '',
        private ?string $oAuth2RedirectUri = null,
        private ?string $oAuth2AuthCode = null,
        private ?string $oAuth2State = null,
    ) {
    }

    public static function fromArray(array $options): self
    {
        $allowed = ['oAuth2ClientId', 'oAuth2ClientSecret', 'oAuth2RedirectUri', 'oAuth2AuthCode', 'oAuth2State'];
        foreach (array_keys($options) as $key) {
            if (!in_array($key, $allowed, true)) {
                throw new BoxException(sprintf('Unknown configuration option: %s', $key));
            }
        }

        return new self(
            oAuth2ClientId: $options['oAuth2ClientId'] ?? '',
            oAuth2ClientSecret: $options['oAuth2ClientSecret'] ?? '',
            oAuth2RedirectUri: $options['oAuth2RedirectUri'] ?? null,
            oAuth2AuthCode: $options['oAuth2AuthCode'] ?? null,
            oAuth2State: $options['oAuth2State'] ?? null,
        );
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

    public function getOAuth2State(): ?string
    {
        return $this->oAuth2State;
    }

    public function setOAuth2State(?string $state): void
    {
        $this->oAuth2State = $state;
    }
}
