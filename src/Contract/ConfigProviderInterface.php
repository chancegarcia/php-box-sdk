<?php

namespace Box\Contract;

interface ConfigProviderInterface
{
    public function getClientId(): string;
    public function getClientSecret(): string;
    public function getRedirectUri(): ?string;
    public function getState(): ?string;
    public function getAuthCode(): ?string;
    public function getRefreshToken(): ?string;
    public function getUploadFilePath(): ?string;
    public function getUploadFolderId(): string;
    public function getAccessToken(): ?string;

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
}
