<?php

namespace Box\Contract;

/**
 * Provides SDK configuration values from the host application's environment.
 *
 * Method requirements are conditional on auth mode:
 *   - getAuthMode() is always required.
 *   - OAuth2 methods are required when getAuthMode() === 'oauth2' (the default).
 *   - JWT methods are required when getAuthMode() === 'jwt'.
 *   - Storage and utility methods are always optional.
 */
interface ConfigProviderInterface
{
    // -------------------------------------------------------------------------
    // Always required
    // -------------------------------------------------------------------------

    // Returns 'oauth2' (default) or 'jwt'
    public function getAuthMode(): string;

    // -------------------------------------------------------------------------
    // Required when getAuthMode() === 'oauth2'
    // -------------------------------------------------------------------------

    public function getOAuth2ClientId(): string;
    public function getOAuth2ClientSecret(): string;

    // Optional within OAuth2 flow — only required when using redirect / PKCE
    public function getOAuth2RedirectUri(): ?string;
    public function getOAuth2State(): ?string;
    public function getOAuth2AuthCode(): ?string;

    // Optional within OAuth2 flow — pre-populated access/refresh tokens
    public function getOAuth2AccessToken(): ?string;
    public function getOAuth2RefreshToken(): ?string;

    // -------------------------------------------------------------------------
    // Required when getAuthMode() === 'jwt'
    // -------------------------------------------------------------------------

    public function getJwtClientId(): string;
    public function getJwtClientSecret(): string;
    public function getJwtEnterpriseId(): string;
    public function getJwtPublicKeyId(): string;

    // Returns PEM content (not a file path) — implementations must read the file
    public function getJwtPrivateKey(): string;

    // Returns passphrase or null if the private key is unencrypted
    public function getJwtPrivateKeyPassphrase(): ?string;

    // -------------------------------------------------------------------------
    // Optional — token storage (PDO backend)
    // -------------------------------------------------------------------------

    public function getStoragePdoDsn(): ?string;
    public function getStoragePdoUser(): ?string;
    public function getStoragePdoPassword(): ?string;

    // -------------------------------------------------------------------------
    // Optional — token storage (filesystem backend)
    // -------------------------------------------------------------------------

    public function getStorageFilePath(): ?string;

    // -------------------------------------------------------------------------
    // Optional — general
    // -------------------------------------------------------------------------

    // Upload helpers
    public function getUploadFilePath(): ?string;
    public function getUploadFolderId(): string;

    /**
     * Box account subdomain (e.g. "acme" for acme.app.box.com).
     * Used to construct direct web URLs for files and folders.
     */
    public function getBoxSubdomain(): ?string;
}
