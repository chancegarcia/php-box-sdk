<?php

declare(strict_types=1);

namespace Box\Auth\Jwt;

use Box\Exception\BoxException;

readonly class JwtAuthConfig
{
    /**
     * @throws BoxException
     */
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $enterpriseId,
        public string $publicKeyId,
        public string $privateKey,
        public ?string $privateKeyPassphrase = null,
        public string $jwtAlgorithm = 'RS256',
    ) {
        if ('' === $clientId) {
            throw new BoxException('Client ID cannot be empty.');
        }
        if ('' === $enterpriseId) {
            throw new BoxException('Enterprise ID cannot be empty.');
        }
        if ('' === $publicKeyId) {
            throw new BoxException('Public Key ID cannot be empty.');
        }
        if ('' === $privateKey) {
            throw new BoxException('Private Key cannot be empty.');
        }
    }
}
