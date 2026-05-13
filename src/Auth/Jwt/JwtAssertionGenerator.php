<?php

declare(strict_types=1);

namespace Box\Auth\Jwt;

use Box\Exception\BoxException;

class JwtAssertionGenerator implements JwtAssertionGeneratorInterface
{
    public function generate(JwtAuthConfig $config, string $subjectId, string $subjectType): string
    {
        if ('enterprise' !== $subjectType && 'user' !== $subjectType) {
            throw new BoxException(sprintf('Invalid subject type: %s. Must be exactly "enterprise" or "user".', $subjectType));
        }

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => $config->publicKeyId,
        ];

        $payload = [
            'iss' => $config->clientId,
            'sub' => $subjectId,
            'box_sub_type' => $subjectType,
            'aud' => 'https://api.box.com/oauth2/token',
            'jti' => bin2hex(random_bytes(16)),
            'exp' => time() + 60,
        ];

        $encodedHeader = self::base64UrlEncode((string) json_encode($header));
        $encodedPayload = self::base64UrlEncode((string) json_encode($payload));

        $privateKey = openssl_pkey_get_private($config->privateKey, $config->privateKeyPassphrase ?? '');

        if (false === $privateKey) {
            throw new BoxException('The private key could not be loaded.');
        }

        $dataToSign = $encodedHeader . '.' . $encodedPayload;
        $signature = '';

        if (!openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new BoxException('Could not sign the JWT assertion.');
        }

        return $dataToSign . '.' . self::base64UrlEncode($signature);
    }

    private static function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
