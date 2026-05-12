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
