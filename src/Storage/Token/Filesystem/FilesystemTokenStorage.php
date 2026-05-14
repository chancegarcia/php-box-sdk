<?php

declare(strict_types=1);

namespace Box\Storage\Token\Filesystem;

use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Exception\TokenStorageException;
use Box\Storage\Token\TokenStorageInterface;
use JsonException;

class FilesystemTokenStorage implements TokenStorageInterface
{
    public function __construct(private readonly string $filePath)
    {
    }

    public function storeToken(TokenInterface $token, TokenStorageContext $context): void
    {
        $map = $this->loadMap();
        $map[$context->getCanonicalKey()] = $this->tokenToArray($token);
        $this->saveMap($map);
    }

    public function updateToken(TokenInterface $token, TokenStorageContext $context): void
    {
        $this->storeToken($token, $context);
    }

    public function retrieveToken(TokenStorageContext $context): ?TokenInterface
    {
        $map = $this->loadMap();
        $key = $context->getCanonicalKey();

        if (!array_key_exists($key, $map)) {
            return null;
        }

        return new Token($map[$key]);
    }

    public function removeToken(TokenStorageContext $context): void
    {
        $map = $this->loadMap();
        $key = $context->getCanonicalKey();

        if (!array_key_exists($key, $map)) {
            return;
        }

        unset($map[$key]);
        $this->saveMap($map);
    }

    public function clear(): void
    {
        $this->saveMap([]);
    }

    private function tokenToArray(TokenInterface $token): array
    {
        return [
            'access_token' => $token->getAccessToken(),
            'refresh_token' => $token->getRefreshToken(),
            'grant_type' => $token->getGrantType(),
            'expires_in' => $token->getExpiresIn(),
            'token_type' => $token->getTokenType(),
        ];
    }

    private function loadMap(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $contents = file_get_contents($this->filePath);
        if (false === $contents || '' === $contents) {
            return [];
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $exception = new TokenStorageException(
                sprintf('Failed to parse token storage file "%s": %s', $this->filePath, $e->getMessage()),
                0,
                $e
            );
            throw $exception;
        }

        return is_array($data) ? $data : [];
    }

    private function saveMap(array $map): void
    {
        try {
            $json = json_encode($map, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TokenStorageException(
                sprintf('Failed to encode token data for file "%s": %s', $this->filePath, $e->getMessage()),
                0,
                $e
            );
        }

        $dir = dirname($this->filePath);
        if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new TokenStorageException(
                sprintf('Failed to create directory "%s" for token storage file', $dir)
            );
        }

        if (false === file_put_contents($this->filePath, $json)) {
            throw new TokenStorageException(
                sprintf('Failed to write token storage file "%s"', $this->filePath)
            );
        }
    }
}
