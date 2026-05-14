<?php

namespace Box\Contract;

interface ConfigProviderInterface
{
    public function getOAuth2ClientId(): string;
    public function getOAuth2ClientSecret(): string;
    public function getOAuth2RedirectUri(): ?string;
    public function getOAuth2State(): ?string;
    public function getOAuth2AuthCode(): ?string;
    public function getOAuth2RefreshToken(): ?string;
    public function getUploadFilePath(): ?string;
    public function getUploadFolderId(): string;
    public function getOAuth2AccessToken(): ?string;

    // Returns 'oauth2' (default) or 'jwt'
    public function getAuthMode(): string;

    // JWT-specific getters
    public function getJwtClientId(): string;
    public function getJwtClientSecret(): string;
    public function getJwtEnterpriseId(): string;
    public function getJwtPublicKeyId(): string;

    // Returns PEM content read from the file at BOX_JWT_PRIVATE_KEY_PATH
    public function getJwtPrivateKey(): string;

    // Returns passphrase or null if not set
    public function getJwtPrivateKeyPassphrase(): ?string;

    /**
     * @return string|null
     */
    public function getStoragePdoDsn(): ?string;

    /**
     * @return string|null
     */
    public function getStoragePdoUser(): ?string;

    /**
     * @return string|null
     */
    public function getStoragePdoPassword(): ?string;

    /**
     * @return string|null
     */
    public function getStorageFilePath(): ?string;

    /**
     * Box account subdomain (e.g. "acme" for acme.app.box.com).
     * Used to construct direct web URLs for files and folders.
     *
     * @return string|null
     */
    public function getBoxSubdomain(): ?string;
}
